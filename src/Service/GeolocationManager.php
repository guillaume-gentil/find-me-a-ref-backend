<?php

namespace App\Service;


class GeolocationManager
{
    private $apiKey;

    public function __construct($apiKey)
    {
        $this->apiKey = $apiKey;
    }

    /**
     * Geocode the address thanks to OpenCage/Geocode API
     * 
     * @param string $address The address from the DB
     * @param string $zipcode The zipcode from the DB
     * @param string $type Two choice : 'lat' for latitude or 'lng' for longitude
     * 
     * @return float
     */
    public function useGeocoder($address, $zipcode, $type)
    {
        $geocoder = new \OpenCage\Geocoder\Geocoder($this->apiKey);
        $result = $geocoder->geocode($address . $zipcode);
        
        if ($type == 'lat') {
            return $result['results'][0]['geometry']['lat'];
        } else if ($type == 'lng') {
            return $result['results'][0]['geometry']['lng'];
        }
    }

}