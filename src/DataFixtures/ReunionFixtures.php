<?php

namespace App\DataFixtures;

use App\Entity\Reunion;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class ReunionFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // $product = new Product();
        // $manager->persist($product);
        $reunion = (new Reunion())
            ->setUrl('https://us05web.zoom.us/j/82187490426?pwd=OVdHK2tmcDY3aVpyNHFESkpVOXFVUT09')
            ->setPassword('HfvP8y');
        $manager->persist($reunion);
        $manager->flush();
    }
}
