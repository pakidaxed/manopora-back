<?php

declare(strict_types=1);

namespace App\Controller\Chat;

use App\Repository\User\UserProfileRepository;
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
        private readonly RequestStack $requestStack
    )
    {
    }


    #[Route('/chat/test', name: 'chat_test', methods: 'POST')]
    public function publish(HubInterface $hub): Response
    {
        $data = json_decode($this->requestStack->getCurrentRequest()->getContent());

//        dd($data->message);
//        if($this->getUser() && $this->getUser()->getId() === 1) {
//            $data = $this->userProfileRepository->findOneById(1);
////            dd($data);
            $update = new Update(
                'chat/2',
                json_encode(['id' => rand(1, 999), 'message' => $data->message])
            );

            $hub->publish($update);
//        } else {
//            return $this->json(null, 401);
//        }



        return new Response('published!');
    }

}