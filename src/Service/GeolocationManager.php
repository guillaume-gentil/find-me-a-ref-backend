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

    /**
     * Calculate the distance between two points
     *
     * @param float $lng1
     * @param float $lat1
     * @param float $lng2
     * @param float $lat2
     * @return float distance (in km) between the two points sent in parameter
     */
    public function crowFliesDistance($lng1, $lat1, $lng2, $lat2)
    {
        //? snippet source : https://phpsources.net/code/php/maths/459_distance-en-metre-entre-deux-points-avec-coordonnees-gps
        // earth radius : 6378km
        $earth_radius = 6378137;

        // conversion in radian
        $rlo1 = deg2rad($lng1);
        $rla1 = deg2rad($lat1);
        $rlo2 = deg2rad($lng2);
        $rla2 = deg2rad($lat2);

        // distance calculating
        $dlo = ($rlo2 - $rlo1) / 2;
        $dla = ($rla2 - $rla1) / 2;
        $a = (sin($dla) * sin($dla)) + cos($rla1) * cos($rla2) * (sin($dlo) * sin($dlo));
        $d = 2 * atan2(sqrt($a), sqrt(1 - $a));

        // convert meters to kilometers
        $distance = round(($earth_radius * $d) / 1000, 3);

        // return distance (in km) between the two points sent in parameter
        return $distance;
    }

}