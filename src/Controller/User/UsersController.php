<?php

declare(strict_types=1);

namespace App\Controller\User;

use App\Entity\User\User;
use App\Repository\User\UserPictureRepository;
use App\Repository\User\UserProfileRepository;
use App\Service\User\UserResolverService;
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
        private readonly UserResolverService      $userResolverService,
        private readonly UserPictureRepository    $userPictureRepository
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
        $user2Profile = $this->userResolverService->getUser($username);

        if (!$user2Profile) {
            return $this->json(null, 404);
        }

        $userProfile = $this->getUserProfile($user2Profile);
        $allPictures = $this->getAllOtherUserPictures($user2Profile);

        foreach ($allPictures as $picture) {
            $userProfile['userOtherPictures'][] = $picture;
        }

        return $this->json(['profile' => $userProfile], 200);
    }

    private function getUserProfiles(User $user, int $offset, ?string $city = null): ?array
    {
        return $this->userProfileRepository->findAllUserProfiles($user, $offset, $city) ?? null;
    }

    private function getUserProfile(User $user): ?array
    {
        return $this->userProfileRepository->findSingleUserProfile($user) ?? null;
    }

    private function getAllOtherUserPictures(User $user): ?array
    {
        return $this->userPictureRepository->getAllOtherUserPictures($user) ?? null;
    }
}