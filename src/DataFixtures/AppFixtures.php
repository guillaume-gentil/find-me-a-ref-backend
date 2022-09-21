<?php

namespace App\DataFixtures;

use App\DataFixtures\Provider\findMeARefProvider;
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

        


        $manager->flush();
    }
}
