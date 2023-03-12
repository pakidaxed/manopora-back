<?php

declare(strict_types=1);

namespace App\Controller\Security;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class LoginController extends AbstractController
{
    #[Route('/login', name: 'app_login', methods: 'POST')]
    public function login()
    {
        if (!$this->isGranted('IS_AUTHENTICATED_FULLY')) {
            return $this->json([
                'error' => 'Invalid login request'
            ], 400);
        }

        return $this->json([
            'user' => $this->getUser() ? $this->getUser()->getUserIdentifier() : null
        ]);
    }
    #[Route('/logout', name: 'app_logout')]
    public function logout()
    {
        // Logout logic
    }
}