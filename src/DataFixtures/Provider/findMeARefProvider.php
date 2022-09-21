<?php

namespace App\DataFixtures\Provider;

class findMeARefProvider
{
    private $club = [
        "Les Aiglons de Valence",
        "Les Yeti's de Grenoble",
        "Les Nounours de Nice",
        "Les Alligators de Tricastin",
        "Les primates de Voreppe",
        "Les Frelons de Varces",
        "Lyon Roller Hockey",
        "Seynod Roller Hockey",
        "Krokos de Nîmes",
        "Les Cerfs de Thonon",
        "Les Dauphins de Villard-Bonnot",
    ];

    /**
     * return a random club's name
     */
    public function getClubName()
    {
        return $this->club[array_rand($this->club)];
    }
}