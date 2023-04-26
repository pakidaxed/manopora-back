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

class UserProfileController extends AbstractController
{
    public function __construct(
        private readonly UserProfileRepository    $userProfileRepository,
        private readonly EntityManagerInterface   $entityManager,
        private readonly RequestStack             $requestStack,
        private readonly PayloadValidationService $payloadValidation,
        private readonly GenderResolverService    $genderResolver
    )
    {
    }

    #[Route('/user/profile', name: 'user_profile_get', methods: 'GET')]
    public function getProfileInfo(): JsonResponse
    {
        if (!$this->getUserProfile($this->getUser())) {
            return $this->json([
                'errors' => ['message' => 'Profilis neužpildytas']
            ], 400);
        }

        return $this->json([
            'profile' => $this->getUserProfile($this->getUser())
        ]);
    }

    #[Route('/user/profile', name: 'user_profile_post', methods: 'POST')]
    public function updateUserProfile(): JsonResponse
    {
        $data = json_decode($this->requestStack->getCurrentRequest()->getContent());

        if (!$data) return $this->json(['message' => 'Invalid data'], 400);

        $userProfile = $this->getUserProfile($this->getUser());

        if (!$userProfile) {
            $userProfile = new UserProfile();
            $userProfile->setOwner($this->getUser());
        }

        $userProfile->setName($data->name);
        $profileDate = $data->birthDate ? DateTimeImmutable::createFromFormat('Y-m-d', $data->birthDate) : new \DateTime('today');
        $userProfile->setBirthDate($profileDate);
        $userProfile->setGender($this->genderResolver->getGender($data->gender));
        $userProfile->setInterest($this->genderResolver->getGender($data->interest));
        $userProfile->setDescription($data->description);

        $errors = $this->payloadValidation->validatePayload($userProfile);

        if ($errors) {
            return $this->json(['errors' => $errors], 400);
        }

        $this->entityManager->persist($userProfile);
        $this->entityManager->flush();

        return $this->json('', 201);
    }

    private function getUserProfile(User $user): ?UserProfile
    {
        return $this->userProfileRepository->findOneByOwner($user) ?? null;
    }
}