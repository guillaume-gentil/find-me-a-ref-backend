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
        "match aller",
        "match retour",
        "match de barrage",
        "quart de finale",
        "demi finale",
        "finale",
    ];

    private $category = [
        "U9",
        "U11",
        "U15",
        "U17",
        "U20",
        "Loisir",
        "Régional",
        "N4",
        "N3",
    ];

    private $refereeLevel = [
        "D1",
        "D2",
        "D3",
        "D4"
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

    /**
     * return random name of category
     */
    public function getCategoryName()
    {
        return $this->category[array_rand($this->category)];
    }

    /**
     * return random level of referee
     */
    public function getRefereeLevel()
    {
        return $this->refereeLevel[array_rand($this->refereeLevel)];
    }
}
