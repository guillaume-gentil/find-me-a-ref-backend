<?php

namespace App\DataFixtures\Provider;

class findMeARefProvider
{
    private $club = [
        "Les Aiglons",
        "Les Yeti's",
        "Les Nounours",
        "Les Alligators",
        "Les primates",
        "Les Frelons",
        "Lyon Roller Hockey",
        "Seynod Roller Hockey",
        "Krokos",
        "Les Cerfs",
        "Les Dauphins",
    ];

    /**
     * return a random club's name
     */
    public function getClubName()
    {
        return $this->club[array_rand($this->club)];
    }
}