<?php

namespace App\DataFixtures;

use App\DataFixtures\Provider\providerForProd;
use App\Entity\Category;
use App\Entity\Type;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;


class AppFixturesProd extends Fixture implements FixtureGroupInterface
{
    public function load(ObjectManager $manager): void
    {
        // add Provider class for real name in fixtures
        $provider = new providerForProd;

        // create categories
        $types = $provider->getTypes();

        foreach ($types as $type_name) {
            $type = new Type();
            $type->setName($type_name);
            $type->setCreatedAt(new \DateTimeImmutable("now"));

            $manager->persist($type);
        }

        // create categories
        $categories = $provider->getCategories();

        foreach ($categories as $category_name) {
            $category = new Category();
            $category->setName($category_name);
            $category->setCreatedAt(new \DateTimeImmutable("now"));

            $manager->persist($category);
        }

        // create user admin
        $user = new User;
        $user->setFirstname("admin");
        $user->setLastname("admin");
        $user->setEmail("findmearef@gmail.com");
        $user->setPassword('$2y$13$LZvVrqaT/gEPKNfRgVm6lOoG37h1rcrOieZrRFcBT3Litp0VeeSj.');
        $user->setRoles(["ROLE_ADMIN"]);
        $user->setCreatedAt(new \DateTimeImmutable("now"));

        $manager->persist($user);
        
        // save all object in DB
        $manager->flush();
    }

    /**
     * Set a group for fixtures
     * https://symfony.com/bundles/DoctrineFixturesBundle/current/index.html#fixture-groups-only-executing-some-fixtures
     * to execute a single group : `php bin/console d:f:l --group=prod`
     *
     * @return array
     */
    public static function getGroups(): array
    {
        return ['prod'];
    }
}