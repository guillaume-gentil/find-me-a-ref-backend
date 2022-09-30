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

    private $address = [
        "10 Rue Maryse Bastié, 26000 Valence",
        "5 Rue du Stade, 16000 Angoulême",
        "54 avenue des Neigeos, 74600 ANNECY",
        "Avenue Maurice Martin, 33000 Bordeaux",
        "Rue Charles de Gaulle, 38760 VARCES",
        "7 Rue Jean Giono, 75013 Paris",
        "8 Avenue des Gayeulles, 35700 Rennes",
        "343 Rue de Marquillies, 59000 Lille",
        "Avenue de la grangette, 74200 THONON-LES-BAINS",
        "11 Rue Colette, 67200 Strasbourg",
        "13 Av. Joseph Fallen, 13400 Aubagne",
    ];

    private $geocode = [
        # longitude#latitude
        "6.0926087#45.8822264",  # annecy
        "-1.65071179#48.1325361", #Rennes
        "7.6991209#48.5866336",  #Strasbourg
        "4.9#44.9333",  #Valence
        "-0.5983295#44.8310771",  #Bordeaux
        "2.3744378#48.8356758",  #  Paris
        "0.176271#45.6418096",  # Angoulême
        "5.5650095#43.2938845",  # Aubagne
        "6.4662469#46.361827",  # THONON-LES-BAINS
        "3.0571769#50.61448",  # Lille
        "5.6845032#45.0892147"  #VARCES
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
     * return random adress
     */
    public function getAddress()
    {
        return $this->address[array_rand($this->address)];
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

    /**
     * return random longitude and latitude
     *
     */
    public function getGeocode()
    {
        return $this->geocode[array_rand($this->geocode)];
    }
}
