<?php

declare(strict_types=1);

namespace App\Controller\Chat;

use App\Entity\Chat\Chat;
use App\Entity\Chat\Message;
use App\Entity\User\User;
use App\Repository\Chat\ChatRepository;
use App\Repository\Chat\MessageRepository;
use App\Repository\User\UserPictureRepository;
use App\Service\User\UserResolverService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Mercure\HubInterface;
use Symfony\Component\Mercure\Update;
use Symfony\Component\Routing\Annotation\Route;

class ChatController extends AbstractController
{
    public function __construct(
        private readonly RequestStack           $requestStack,
        private readonly UserResolverService    $userResolverService,
        private readonly ChatRepository         $chatRepository,
        private readonly EntityManagerInterface $entityManager,
        private readonly MessageRepository      $messageRepository,
        private readonly HubInterface           $hub,
        private readonly UserPictureRepository  $userPictureRepository
    )
    {
    }

    #[Route('/chat/list', name: 'chat_list_get', methods: 'get')]
    public function getChatList(): JsonResponse
    {

        $chats = $this->chatRepository->findChatListByUser($this->getUser());
        foreach ($chats as &$chat)
            {
                $chat['mainImage'] = $this->userPictureRepository->findMainImagePath($this->userResolverService->getUser($chat['user2'])) ?? null;
            }

        if (!$chats) {
            return $this->json(null, 404);
        }

        return $this->json(['chats' => $chats], 200);
    }

    #[Route('/chat', name: 'chat_messages_get', methods: 'get')]
    public function getChatMessages(): JsonResponse
    {
        $username = $this->requestStack->getCurrentRequest()->get('username') ?? null;
        $user2 = $this->userResolverService->getUser($username);

        if (!$user2) {
            return $this->json(null, 404);
        }

        if ($user2 === $this->getUser()) {
            return $this->json(['errors' => [['message' => 'Invalid data']]], 400);
        }

        $chat = $this->getChatByUsers($this->getUser(), $user2);

        if (!$chat) {
            return $this->json(null, 200);
        }

        $messages = $this->messageRepository->getAllMessages($chat);

        foreach ($messages as $message) {
            if ($message['username'] !== $this->getUser()->getUsername()) {
                $messageObject = $this->messageRepository->findOneById($message['id']);
                $messageObject->setSeen(true);
                $this->entityManager->persist($messageObject);
            }
        }
        $this->entityManager->flush();

        if (!$messages) {
            return $this->json(null, 404);
        }


        return $this->json(['messages' => $messages], 200);
    }

    #[Route('/chat', name: 'chat_message_post', methods: 'POST')]
    public function handleMessage(): JsonResponse
    {
        $data = json_decode($this->requestStack->getCurrentRequest()->getContent());

        if (!property_exists($data, 'message') || !property_exists($data, 'user2')) {
            return $this->json(['errors' => [['message' => 'Invalid data']]], 400);
        }

        $user2 = $this->userResolverService->getUser($data->user2);
        if (!$user2) {
            return $this->json(['errors' => [['message' => 'No such user']]], 404);
        }

        if ($user2 === $this->getUser()) {
            return $this->json(['errors' => [['message' => 'Invalid data']]], 400);
        }

        $chat = $this->getChatByUsers($this->getUser(), $user2);

        if (!$chat) {
            $newChat = new Chat();
            $newChat->setUserOne($this->getUser());
            $newChat->setUserTwo($user2);
            $chat = $newChat;
        }

        $chat->setUpdatedAt();
        $this->entityManager->persist($chat);

        $newMessage = new Message();
        $newMessage->setMessage($data->message);
        $newMessage->setOwner($chat);
        $newMessage->setSeen(false);
        $newMessage->setSender($this->getUser());
        $this->entityManager->persist($newMessage);

        $this->entityManager->flush();
        $this->publish($user2->getUsername());

        return $this->json(null, 200);
    }

    #[Route('/chat/message/count', name: 'chat_message_count_get', methods: 'get')]
    public function getNewMessagesCount(): JsonResponse
    {
        $totalNewMessagesCount = $this->chatRepository->getNewMessagesCount($this->getUser());

        return $this->json(['totalNewMessagesCount' => $totalNewMessagesCount ?? 0], 200);
    }

    public function publish(string $user2): void
    {
        $update = new Update(
            'chat/' . $user2,
            json_encode(null)
        );

        $this->hub->publish($update);
    }

    private function getChatByUsers(User $user1, User $user2): ?Chat
    {
        return $this->chatRepository->findChatByUsers($user1, $user2) ?? null;
    }
}