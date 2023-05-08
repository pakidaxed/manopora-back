<?php

namespace App\DataFixtures;

use App\Entity\User\User;
use App\Entity\User\UserProfile;
use App\Repository\Props\CityRepository;
use App\Service\User\GenderResolverService;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class FakeUsersFixture extends Fixture
{
    const GENDER_VALUES = ['vyras', 'moteris', 'pora', 'kita'];

    public function __construct(
        private readonly GenderResolverService       $genderResolver,
        private readonly CityRepository $cityRepository,
        private readonly UserPasswordHasherInterface $userPasswordHasher
    )
    {

    }

    public function load(ObjectManager $manager): void
    {
        for ($i = 201; $i <= 500; $i++) {
            $user = new User();
            $user->setUsername('fUser' . $i);
            $user->setEmail('fUserEmail' . $i . '@mp.lt');
            $user->setTerms(true);
            $user->setPassword($this->userPasswordHasher->hashPassword($user, 'mikiss'));
            $manager->persist($user);

            $profile = new UserProfile();
            $profile->setOwner($user);
            $profile->setName('fUserName' . $i);
            $profile->setBirthDate(new \DateTimeImmutable('1988-05-27'));
            $profile->setGender($this->genderResolver->getGender(self::GENDER_VALUES[array_rand(self::GENDER_VALUES)]));
            $profile->setCity($this->cityRepository->findAll()[array_rand($this->cityRepository->findAll())]);
            $profile->setInterest($this->genderResolver->getGender(self::GENDER_VALUES[array_rand(self::GENDER_VALUES)]));
            $manager->persist($profile);
            $manager->flush();
        }

    }
}
