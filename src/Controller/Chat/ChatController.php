<?php

declare(strict_types=1);

namespace App\Controller\Chat;

use App\Entity\Chat\Chat;
use App\Entity\Chat\Message;
use App\Entity\User\User;
use App\Repository\Chat\ChatRepository;
use App\Repository\Chat\MessageRepository;
use App\Repository\User\UserProfileRepository;
use App\Service\User\UserResolverService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mercure\HubInterface;
use Symfony\Component\Mercure\Update;
use Symfony\Component\Routing\Annotation\Route;

class ChatController extends AbstractController
{
    public function __construct(
        private readonly UserProfileRepository $userProfileRepository,
        private readonly RequestStack $requestStack,
        private readonly UserResolverService $userResolverService,
        private readonly ChatRepository $chatRepository,
        private readonly EntityManagerInterface $entityManager,
        private readonly MessageRepository $messageRepository,
        private readonly HubInterface $hub
    )
    {
    }

    #[Route('/chat', name: 'chat_messages_get', methods: 'get')]
    public function getChatMessages(HubInterface $hub): Response
    {
        $username = $this->requestStack->getCurrentRequest()->get('username') ?? null;
        $user2 = $this->userResolverService->getUser($username);

        if (!$user2) {
            return $this->json(null, 404);
        }

        $chat = $this->getChatByUsers($this->getUser(), $user2);

        if (!$chat) {
            return $this->json(null, 404);
        }

        $messages = $this->messageRepository->getAllMessages($chat);

        if (!$messages) {
            return $this->json(null, 404);
        }

        return $this->json(['messages' => $messages], 200);
    }

    #[Route('/chat', name: 'chat_message_post', methods: 'POST')]
    public function handleMessage(): Response
    {
        $data = json_decode($this->requestStack->getCurrentRequest()->getContent());

        if (!property_exists($data, 'message') || !property_exists($data, 'user2')) {
            return $this->json(['errors' => [['message' => 'Invalid data']]], 400);
        }

        // TODO reik checkint dar kad stringai butu
        if (!$data->message || !$data->user2) {
            return $this->json(['errors' => [['message' => 'Invalid data']]], 400);
        }

        $user2 = $this->userResolverService->getUser($data->user2);
        if (!$user2) {
            return $this->json(['errors' => [['message' => 'Invalid data2']]], 400);
        }

        $chat = $this->getChatByUsers($this->getUser(), $user2);

        if (!$chat) {
            $newChat = new Chat();
            $newChat->setUserOne($this->getUser());
            $newChat->setUserTwo($user2);
            $this->entityManager->persist($newChat);
            $chat = $newChat;
        }

        $newMessage = new Message();
        $newMessage->setMessage($data->message);
        $newMessage->setOwner($chat);
        $newMessage->setSeen(false);
        $newMessage->setSender($this->getUser());
        $this->entityManager->persist($newMessage);

        $this->entityManager->flush();
        $this->publish();

        return $this->json(null, 200);
    }


    public function publish(): void
    {
        $data = json_decode($this->requestStack->getCurrentRequest()->getContent());

//        dd($data->message);
//        if($this->getUser() && $this->getUser()->getId() === 1) {
//            $data = $this->userProfileRepository->findOneById(1);
////            dd($data);
            $update = new Update(
                'chat/2',
                json_encode(['newMessage' => true])
            );

            $this->hub->publish($update);
//        } else {
//            return $this->json(null, 401);
//        }
    }

    private function getChatByUsers(User $user1, User $user2): ?Chat
    {
        return $this->chatRepository->findChatByUsers($user1, $user2) ?? null;
    }

}