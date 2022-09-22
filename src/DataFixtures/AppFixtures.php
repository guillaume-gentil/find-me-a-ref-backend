<?php

namespace App\DataFixtures;

use App\DataFixtures\Provider\findMeARefProvider;
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
        for ($i = 1; $i <= 10; $i++) {
            $club = new Club();
            $clubName = $findMeARefProvider->getClubName();
            while(in_array($clubName, $clubs_names)) {
                $clubName = $findMeARefProvider->getClubName();
            }

            $club->setName($clubName);
            $club->setAddress($faker->address());
            $club->setZipCode($faker->randomNumber(5, true));
            $club->setCreatedAt(new \DateTimeImmutable("now"));

            $manager->persist($club);
            $clubs[] = $club;
            $clubs_names[] = $club->getName();

        }

        //type fixtures

        $types = [];
        $types_names = [];
        for ($i =1; $i <=6; $i++) {
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
        for ($i = 1; $i <= 6; $i++) {
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
        for ($i = 1; $i <= 15; $i++) {
            $team = new Team();
            // choice random category in categories array just create warning to choose a number <= of categories created in fixture above
            $team->setCategory($categories[mt_rand(0,5)]);
            $team->setClub($clubs[mt_rand(0,9)]);
            // warning the name of team take name of category and club just created above don't move setName above setCategory and setClub
            $team->setName(($team->getClub()->getName()). " " . $team->getCategory()->getName());
            $team->setCreatedAt(new \DateTimeImmutable("now"));

            $manager->persist($team);
            $teams[] = $team;
        }

        //Arena fixtures

        $arenas = [];
        for ($i = 1; $i <= 15; $i++) {
            $arena = new Arena();

            $arena->setName($faker->company());
            $arena->setAddress($faker->address());
            $arena->setZipCode($faker->randomNumber(5, true));
            $arena->setCreatedAt(new \DateTimeImmutable("now"));

            $manager->persist($arena);
            $arenas[] = $arena;

        }

        //User fixtures

        $users = [];
        
        for ($i = 1; $i <= 20; $i++) {
            $user = new User();

            $user->setFirstname($faker->firstName());
            $user->setLastname($faker->lastName());
            $user->setEmail($faker->email());
            $user->setRole('ROLE_REFEREE');
            $user->setPassword('mdpfix');
            $user->setLicenceId($faker->randomNumber(6, true));

            $refereeLevel = $findMeARefProvider->getRefereeLevel();
            $user->setLevel($refereeLevel);
            $user->setAddress($faker->address());
            $user->setZipCode($faker->randomNumber(5, true));
            $user->setCreatedAt(new \DateTimeImmutable("now"));

            $manager->persist($user);
            $users[] = $user;

        }

        //Game fixtures

        $games = [];
        for($i = 1; $i <= 20; $i++) {
            $game = new Game();

            $game->setDate($faker->dateTimeBetween('+1 week' , '+5 week'));
            $game->setCreatedAt(new \DateTimeImmutable("now"));
            $game->setArena($arenas[mt_rand(0,14)]);
            $game->setType($types[mt_rand(0,5)]);
            //add two teams with two differents parts of teams array for not duplicates same team.
            $game->addTeam($teams[mt_rand(0,6)]);
            $game->addTeam($teams[mt_rand(7,14)]);
            
            $manager->persist($game);
            $games[] = $game;
        }

        $manager->flush();
    }
}
