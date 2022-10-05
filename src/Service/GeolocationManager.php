<?php

namespace App\Service;
use OpenCage\Geocoder\Geocoder;

class GeolocationManager
{
    private $apiKey;
    private $geocoder;

    public function __construct($apiKey, Geocoder $geocoder)
    {
        $this->apiKey = $apiKey;
        $this->geocoder = $geocoder;
    }

    /**
     * return latitude of adress with geocage API
     * @param  $location adress to find latitude
     *
     * @return void
     */
    public function latitude($location)
    {
        $geocoder = new $this->geocoder($this->apiKey);
        $result = $geocoder->geocode($location->getAddress());
        $latitude = $location->setLatitude($result['results'][0]['geometry']['lat']);
        return $latitude;

    }

    /**
     * return longitude of address with geocage API
     * @param  $location 
     *
     * @return void
     */
    public function longitude($location)
    {
        $geocoder = new $this->geocoder($this->apiKey);
        $result = $geocoder->geocode($location->getAddress());
        $longitude = $location->setLongitude($result['results'][0]['geometry']['lng']);
        return $longitude;
    }
    
    /* $geocoder = new \OpenCage\Geocoder\Geocoder('8e14f9f8abbd4a7c9b30d907d724e3f4');
        $result = $geocoder->geocode($arena->getAddress());

        $arena->setLatitude($result['results'][0]['geometry']['lat']);
        $arena->setLongitude($result['results'][0]['geometry']['lng']); */
}