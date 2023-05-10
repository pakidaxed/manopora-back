<?php

declare(strict_types=1);

namespace App\Controller\User;

use App\Entity\User\User;
use App\Repository\User\UserProfileRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\Annotation\Route;

class UsersController extends AbstractController
{
    const PROFILES_PER_PAGE = 5;

    public function __construct(
        private readonly UserProfileRepository    $userProfileRepository,
        private readonly RequestStack             $requestStack,
    )
    {
    }

    #[Route('/user/profiles', name: 'user_profiles_get', methods: 'GET')]
    public function getAllProfiles(): JsonResponse
    {
        $page = $this->requestStack->getCurrentRequest()->get('page') ?? 0;
        $city = $this->requestStack->getCurrentRequest()->get('city') ?? null;

        if ($city === 'all') {
            $city = null;
        }

        if (!$page) {
            return $this->json(['profiles' => $this->getUserProfiles($this->getUser(), 0, $city)]);
        }

        return $this->json(['profiles' => $this->getUserProfiles($this->getUser(), $page * self::PROFILES_PER_PAGE, $city)]);
    }

    #[Route('/user/profile', name: 'user_profile_get', methods: 'GET')]
    public function getSingleProfileInfo(): JsonResponse
    {
        $username = $this->requestStack->getCurrentRequest()->get('username') ?? null;
        $singleProfile = $this->getUserProfile($username);

        if (!$singleProfile) {
            return $this->json(null, 404);
        }

        return $this->json(['profile' => $singleProfile], 200);
    }

    private function getUserProfiles(User $user, int $offset, ?string $city = null): ?array
    {
        return $this->userProfileRepository->findAllUserProfiles($user, $offset, $city) ?? null;
    }

    private function getUserProfile(?string $username): ?array
    {
        return $this->userProfileRepository->findSingleUserProfile($username) ?? null;
    }
}