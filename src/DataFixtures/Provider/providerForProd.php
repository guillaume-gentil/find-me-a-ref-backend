<?php

namespace App\DataFixtures\Provider;

class providerForProd
{
    
    private $types = [
        "match amical",
        "match de poule",
        "quart de finale",
        "demi finale",
        "finale",
    ];

    private $categories = [
        "U7",
        "U9",
        "U11",
        "U15",
        "U17",
        "U20",
        "Loisir",
        "Régional",
        "Pré National",
        "N3",
        "N2",
        "N1",
        "Elite",
        "Féminine N2",
        "Féminine N1"
    ];

    private $userAdmin = [
        "firstname" => "admin",
        "lastname" => "admin",
        "email" => "findmearef@gmail.com",
        "password" => "$2y$13$LZvVrqaT/gEPKNfRgVm6lOoG37h1rcrOieZrRFcBT3Litp0VeeSj.", # hash a password : bin/console security:hash-password
        "roles" => "ROLE_ADMIN"
    ];



    /**
     * Get the value of userAdmin
     */ 
    public function getUserAdmin()
    {
        return $this->userAdmin;
    }

    /**
     * Get the value of category
     */ 
    public function getCategory()
    {
        return $this->categories;
    }

    /**
     * Get the value of type
     */ 
    public function getType()
    {
        return $this->types;
    }
}
