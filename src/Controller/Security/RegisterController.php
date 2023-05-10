<?php

declare(strict_types=1);

namespace App\Controller\Security;

use App\Entity\User\User;
use App\Service\Validation\PayloadValidationService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class RegisterController extends AbstractController
{
    public function __construct(
        private readonly EntityManagerInterface      $entityManager,
        private readonly RequestStack                $requestStack,
        private readonly UserPasswordHasherInterface $userPasswordHasher,
        private readonly PayloadValidationService $payloadValidation
    )
    {
    }

    #[Route('/register', name: 'app_register', methods: 'POST')]
    public function register(): JsonResponse
    {
        $data = json_decode($this->requestStack->getCurrentRequest()->getContent());

        if (!$data) return $this->json(['message' => 'Invalid data'], 400);

        if (
            !property_exists($data, 'username') ||
            !property_exists($data, 'email') ||
            !property_exists($data, 'password') ||
            !property_exists($data, 'terms')
        ) {
            return $this->json(['errors' => [['message' => 'Invalid data']]], 400);
        }

        $user = new User();
        $user->setUsername($data->username ?? '');
        $user->setEmail($data->email ?? '');
        $user->setPassword($data->password ?? '');
        $user->setTerms($data->terms);

        $errors = $this->payloadValidation->validatePayload($user);

        if ($errors) {
            return $this->json($errors, 400);
        }

        $user->setPassword($this->userPasswordHasher->hashPassword($user, $data->password));

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return $this->json(['message' => 'User successfully created'], 201);
    }
}