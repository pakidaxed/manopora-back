<?php

declare(strict_types=1);

namespace App\Controller\Security;

use App\Entity\User\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class RegisterController extends AbstractController
{
    public function __construct(
        private readonly EntityManagerInterface                                    $entityManager,
        private readonly RequestStack                                              $requestStack,
        private readonly UserPasswordHasherInterface                               $userPasswordHasher,
        private readonly \Symfony\Component\Validator\Validator\ValidatorInterface $validator
    )
    {
    }

    #[Route('/register', name: 'app_register', methods: 'POST')]
    public function register()
    {

        $data = json_decode($this->requestStack->getCurrentRequest()->getContent());

        $user = new User();
        $user->setEmail($data->email ?? '');
        $user->setPassword($this->userPasswordHasher->hashPassword($user, $data->password));

        $validationErrors = $this->validator->validate($user);

        $errors = [];
        if (count($validationErrors) > 0) {
            foreach ($validationErrors as $error) {
                $errors[] = [
                    'field' => $error->getPropertyPath(),
                    'message' => $error->getMessage()
                ];
            }
            return $this->json($errors, 400);
        }

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return $this->json(['message' => 'User successfully created'], 201);
    }
}