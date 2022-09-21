<?php

namespace App\DataFixtures;

use App\DataFixtures\Provider\findMeARefProvider;
use App\Entity\Club;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        //add Provider class for real name of club in fixtures
        $findMeARefProvider = new findMeARefProvider();
        
        //add Fakerphp in french for fixtures
        $faker = Factory::create('fr_FR');

        $clubs = [];

        $clubs_names = [];
        for ($i = 1; $i <= 5; $i++) {
            $club = new Club();
            $clubName = $findMeARefProvider->getClubName();
            while(in_array($clubName, $clubs_names)) {
                $clubName = $findMeARefProvider->getClubName();
            }

            $club->setName($clubName);
            $club->setAddress($faker->address());
            $club->setZipCode($faker->randomNumber(6, true));
            $club->setCreatedAt(new \DateTimeImmutable("now"));

            $manager->persist($club);
            $clubs[] = $club;
            $clubs_names[] = $club->getName();

        }



        $manager->flush();
    }
}
