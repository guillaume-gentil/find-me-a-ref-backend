<?php

namespace App\DataFixtures;

use App\DataFixtures\Provider\findMeARefProvider;
use App\Entity\Category;
use App\Entity\Club;
use App\Entity\Team;
use App\Entity\Type;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        //add Provider class for real name in fixtures
        $findMeARefProvider = new findMeARefProvider();
        
        //add Fakerphp in french for fixtures
        $faker = Factory::create('fr_FR');

        // Empty array for save club's name and don't have two clubs with the same name.
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

        //type fixtures

        $types = [];
        $types_names = [];
        for ($i =1; $i <=5; $i++) {
            $type = new Type();
            $typeName = $findMeARefProvider->getTypeName();
            while(in_array($typeName, $types_names)) {
                $typeName = $findMeARefProvider->getTypeName();
            }
            $type->setName($typeName);
            $type->setCreatedAt(new \DateTimeImmutable("now"));

            $manager->persist($type);
            $types[] = $type;
            $types_names[] = $type->getName();
        }

        //Category fixtures

        $categories = [];
        $categories_names = [];
        for ($i = 1; $i <= 10; $i++) {
            $category = new Category();
            $categoryName = $findMeARefProvider->getCategoryName();
            while(in_array($categoryName, $categories_names)) {
                $categoryName = $findMeARefProvider->getCategoryName();
            }
            $category->setName($categoryName);
            $category->setCreatedAt(new \DateTimeImmutable("now"));

            $manager->persist($category);
            $categories[] = $category;
        }

        //Team fixtures

        $teams = [];
        for ($i = 1; $i <= 2; $i++) {
            $team = new Team();
            // the better name for a team is $club->getname . $category->getName but it's doesn't work yet
            $team->setName($faker->word());
            // don't know how to code a random existing $category id, for the moment this is just last id create in the fixture.
            $team->setCategory($category);
            // same as category maybe it's possible to use __tostring function in club entity.
            $team->setClub($club);
            $team->setCreatedAt(new \DateTimeImmutable("now"));

            $manager->persist($team);
            $teams[] = $team;
        }

        $manager->flush();
    }
}
