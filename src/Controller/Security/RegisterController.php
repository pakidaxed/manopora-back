<?php

declare(strict_types=1);

namespace App\Controller\Security;

use App\Entity\User\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class RegisterController extends AbstractController
{
    public function __construct(
        private readonly EntityManagerInterface      $entityManager,
        private readonly RequestStack                $requestStack,
        private readonly UserPasswordHasherInterface $userPasswordHasher,
        private readonly ValidatorInterface          $validator
    )
    {
    }

    #[Route('/register', name: 'app_register', methods: 'POST')]
    public function register(): JsonResponse
    {
        $errors = [];
        $data = json_decode($this->requestStack->getCurrentRequest()->getContent());

        if (!$data) return $this->json(['message' => 'Invalid data'], 400);

        $user = new User();
        $user->setUsername($data->username ?? '');
        $user->setEmail($data->email ?? '');
<<<<<<< Updated upstream
        $user->setPassword($data->password);

        // password match validation
        if ($data->password !== $data->passwordConfirm) {
            $errors[] = [
                'field' => 'passwordConfirm',
                'message' => 'Password must match'
            ];
        }
=======
        $user->setPassword($data->password ?? '');
        $user->setTerms($data->terms);
>>>>>>> Stashed changes

        $validationErrors = $this->validator->validate($user);

        if (count($validationErrors) > 0) {
            foreach ($validationErrors as $error) {
                $errors[] = [
                    'field' => $error->getPropertyPath(),
                    'message' => $error->getMessage()
                ];
            }



// TODO jauciu reik padaryt kad grazintu arejuje ['errors' => $errors] fronta perdaryt
            return $this->json($errors, 400);
        }

<<<<<<< Updated upstream
        $user->setPassword($this->userPasswordHasher->hashPassword($user, $data->password));
=======
        $user->setPassword($this->userPasswordHasher->hashPassword($user, $data->password)); // if all ok, hash the pass
>>>>>>> Stashed changes

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return $this->json(['message' => 'User successfully created'], 201);
    }
}