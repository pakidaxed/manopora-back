<?php

declare(strict_types=1);

namespace App\Controller\Props;

use App\Repository\Props\GenderRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class GenderController extends AbstractController
{
    public function __construct(
        private readonly GenderRepository $genderRepository
    )
    {
    }

    #[Route('/props/gender', name: 'props_gender', methods: 'GET')]
    public function getAllGenders(): JsonResponse
    {
        $allGenders = $this->genderRepository->findAll();

        return $this->json(['genders' => $allGenders], 200);
    }
}