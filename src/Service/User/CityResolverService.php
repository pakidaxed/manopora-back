<?php

declare(strict_types=1);

namespace App\Service\User;

use App\Entity\Props\City;
use App\Repository\Props\CityRepository;

class CityResolverService
{
    public function __construct(
        private readonly CityRepository $cityRepository
    )
    {
    }

    public function getCity(?string $city): ?City
    {
        return $this->cityRepository->findOneByName($city);
    }
}