<?php

declare(strict_types=1);

namespace App\Service\User;

use App\Entity\User\User;
use App\Repository\User\UserRepository;

class UserResolverService
{
    public function __construct(
        private readonly UserRepository $userRepository
    )
    {
    }

    public function getUser(?string $username): ?User
    {
        return $this->userRepository->findOneByUsername($username);
    }
}