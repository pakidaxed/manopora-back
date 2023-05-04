<?php

declare(strict_types=1);

namespace App\Controller\User;

use App\Entity\User\User;
use App\Entity\User\UserProfile;
use App\Repository\User\UserProfileRepository;
use App\Service\User\GenderResolverService;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use App\Service\Validation\PayloadValidationService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\Annotation\Route;

class UsersController extends AbstractController
{
    const PROFILES_PER_PAGE = 5;

    public function __construct(
        private readonly UserProfileRepository    $userProfileRepository,
        private readonly EntityManagerInterface   $entityManager,
        private readonly RequestStack             $requestStack,
        private readonly PayloadValidationService $payloadValidation,
        private readonly GenderResolverService    $genderResolver
    )
    {
    }

    #[Route('/user/profiles', name: 'user_profiles_get', methods: 'GET')]
    public function getProfileInfo(): JsonResponse
    {
        //TODO SAVE ISIMT IS RESULTU

        $page = $this->requestStack->getCurrentRequest()->get('page') ?? 0;

        if (!$page) {
            return $this->json(['profiles' => $this->getUserProfiles(0)]);
        }

        return $this->json(['profiles' => $this->getUserProfiles($page * self::PROFILES_PER_PAGE)]);
    }

    #[Route('/user/profile', name: 'user_profile_get', methods: 'GET')]
    public function getSingleProfileInfo(): JsonResponse
    {
        $username = $this->requestStack->getCurrentRequest()->get('username') ?? null;
        $singleProfile = $this->getUserProfile($username);

        if (!$singleProfile) {
            return $this->json(null, 404);
        }

        return $this->json(['profile' => $singleProfile]);
    }

    private function getUserProfiles(int $offset): ?array
    {
        return $this->userProfileRepository->findAllUserProfiles($offset) ?? null;
    }

    private function getUserProfile(?string $username): ?array
    {
        return $this->userProfileRepository->findSingleUserProfile($username) ?? null;
    }
}