<?php

declare(strict_types=1);

namespace App\Controller\Security;

use ApiPlatform\Validator\ValidatorInterface;
use App\Entity\User\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\NotBlank;

class RegisterController extends AbstractController
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly RequestStack $requestStack,
        private readonly UserPasswordHasherInterface $userPasswordHasher,
        private readonly \Symfony\Component\Validator\Validator\ValidatorInterface $validator
    ) { }

    #[Route('/register', name: 'app_register', methods: 'POST')]
    public function register()
    {

        $data = json_decode($this->requestStack->getCurrentRequest()->getContent());

        $user = new User();
        $user->setEmail($data->email ?? '');
        $user->setPassword($this->userPasswordHasher->hashPassword($user, $data->password));

        $errors = $this->validator->validate($user);

        if (count($errors) > 0) {

            return $this->json($errors, 400);
        }

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return $this->json('User successfully created', 201);
    }
}