<?php

declare(strict_types=1);

namespace App\Controller\Security;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class LoginController extends AbstractController
{
    #[Route('/login', name: 'app_login', methods: 'POST')]
    public function login(): JsonResponse
    {
        if (!$this->isGranted('IS_AUTHENTICATED_FULLY')) {
            return $this->json([
                'errors' => ['message' => 'Invalid login request']
            ], 400);
        }

        return $this->json(null, 200);
    }

    #[Route('/check', name: 'app_check', methods: 'GET')]
    public function check(): JsonResponse
    {
        if ($this->isGranted('IS_AUTHENTICATED_FULLY')) {
            return $this->json(['username' => $this->getUser()->getUsername()], 200);
        }

        return $this->json(null, 401);
    }

    #[Route('/logout', name: 'app_logout', methods: 'GET')]
    public function logout(): JsonResponse
    {
        return $this->json(null, 204);
    }
}