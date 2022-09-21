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

    private $type = [
        "match amical",
        "match de poule",
        "match allé",
        "match retour",
        "match de barrage",
        "quart de finale",
        "demi finale",
        "finale",
    ];

    /**
     * return random club's name
     */
    public function getClubName()
    {
        return $this->club[array_rand($this->club)];
    }

    /**
     * return random type of match
     */
    public function getTypeName()
    {
        return $this->type[array_rand($this->type)];
    }
}
