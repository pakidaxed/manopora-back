<?php

declare(strict_types=1);

namespace App\Service\User;

use App\Entity\Props\Gender;
use App\Repository\Props\GenderRepository;

class GenderResolverService
{

    public function __construct(
        private readonly GenderRepository $genderRepository
    )
    {
    }

    public function getGender(?string $gender): ?Gender
    {
        return $this->genderRepository->findOneByName($gender);
    }
}