<?php

declare(strict_types=1);

namespace App\Controller\Props;

use App\Repository\Props\CityRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class CityController extends AbstractController
{
    public function __construct(
        private readonly CityRepository $cityRepository
    )
    {
    }

    #[Route('/props/city', name: 'props_city', methods: 'GET')]
    public function getAllCities(): JsonResponse
    {
        $allCities = $this->cityRepository->findAll();

        return $this->json(['cities' => $allCities], 200);
    }
}