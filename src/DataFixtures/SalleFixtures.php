<?php

namespace App\DataFixtures;

use App\Entity\Salle;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class SalleFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
       $salle = new Salle();
       $salle->setNom('Radison blue');
        $manager->persist($salle);
        $manager->flush();
    }
}
