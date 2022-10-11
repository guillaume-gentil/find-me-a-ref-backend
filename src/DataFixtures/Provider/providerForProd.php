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

    /**
     * Get the value of types
     */ 
    public function getTypes()
    {
        return $this->types;
    }

    /**
     * Get the value of categories
     */ 
    public function getCategories()
    {
        return $this->categories;
    }

}
