<?php

namespace App\DataFixtures;

use App\Entity\Props\Gender;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class PropsGenders extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $gender = new Gender();
        $gender->setName('vyras');
        $gender->setTitle('Vyras');
        $gender->setInterestTitle('Vyro');
        $gender->setEnabled(true);
        $manager->persist($gender);

        $gender = new Gender();
        $gender->setName('moteris');
        $gender->setTitle('Moteris');
        $gender->setInterestTitle('Moters');
        $gender->setEnabled(true);
        $manager->persist($gender);

        $gender = new Gender();
        $gender->setName('pora');
        $gender->setTitle('Pora');
        $gender->setInterestTitle('Poros');
        $gender->setEnabled(true);
        $manager->persist($gender);

        $gender = new Gender();
        $gender->setName('kita');
        $gender->setTitle('Kita');
        $gender->setInterestTitle('Kita');
        $gender->setEnabled(true);
        $manager->persist($gender);

        $manager->flush();
    }
}
