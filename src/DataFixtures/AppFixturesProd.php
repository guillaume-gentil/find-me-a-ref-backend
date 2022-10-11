<?php

namespace App\DataFixtures;

use App\DataFixtures\Provider\providerForProd;
use App\Entity\Arena;
use App\Entity\Category;
use App\Entity\Club;
use App\Entity\Game;
use App\Entity\Team;
use App\Entity\Type;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class AppFixturesProd extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        
    }
}