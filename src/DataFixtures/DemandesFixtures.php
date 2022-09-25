<?php

namespace App\DataFixtures;

use App\Entity\Demande;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class DemandesFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // $product = new Product();
        // $manager->persist($product);
        $sit = ['epoux', 'epouse'];
        $civ = ['homme', 'femme'];
        for ($i = 1; $i <= 10; $i++) {
            $dm = (new Demande())
                ->setNom("Nom $i")
                ->setEmail("")
                ->setPrenom(" Prenom $i")
                ->setImage("img_".$i.".png")
                ->setTelephone("77 00".$i."0".$i."0".$i)
                ->setSituation($sit[random_int(0,1)])
                ->setEtat(false)
                ->setSlug(uniqid('dmd-'))
                ->setCivilite($civ[random_int(0,1)]);
            if ($i%2 ==0){
                $dm->setHerName("her name $i");
            }

            $manager->persist($dm);
        }

        $manager->flush();
    }
}
