<?php
namespace JessicaDigital\TflCycles;

class TflCycles {
    
    /**
     * TFL API endpoint
     */
    protected $api_endpoint = 'https://api.tfl.gov.uk/bikepoint';
    
    /*
     * Coordinates of locations we want to find cycle hire stations near
     */
    protected $coordinates;
    
    /*
     * Maximum distance from coordinates for cycle hire stations
     */
    protected $distance = 1000;
    
    const EARTH_RADIUS = 6367.4447;
    
    public function __construct() {
        $this->coordinates = array();
    }
    
    /**
     * Add a location for filtering cycle hire stations
     * @param float $latitude Coordinate latitude
     * @param float $longitude Coordinate longitude
     */
    public function addLocation($latitude, $longitude) {
        $this->coordinates[] = array($latitude, $longitude);
    }
    
    /**
     * Calculates distance between two points(in km)
     * From https://github.com/dominikveils/coordinates
     *
     * @param array $point1 ['latitude' => 1, 'longitude' => 1]
     * @param array $point2 ['latitude' => 2, 'longitude' => 2]
     *
     * @return float
     */
    protected function distance(array $point1, array $point2) {
        $dLat = $this->toRadian($point2[0] - $point1[0]);
        $dLng = $this->toRadian($point2[1] - $point1[1]);
        $lat1 = $this->toRadian($point1[0]);
        $lat2 = $this->toRadian($point2[0]);
        $a = sin($dLat / 2) * sin($dLat / 2) +
            sin($dLng / 2) * sin($dLng / 2) *
            cos($lat1) * cos($lat2);
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
        $d = static::EARTH_RADIUS * $c;
        return (int)round($d*1000,0);
    }
    
    /**
     * Fetch and return a full API availability feed
     */
    protected function fetchFeed() {
        return json_decode(file_get_contents($this->api_endpoint));
    }
    
    /**
     * Finds all cycle hire locations within the specified parameters
     */
    public function find() {
        
        // Don't bother fetching the feed if we have no coordinates
        if (empty($this->coordinates)) {
            return false;
        }
        
        // Fetch the feed
        $locations = $this->fetchFeed();
        
        // Create results variable
        $cyclestations = array();        
        
        // Process the locations
        foreach ($locations as $location) {
            
            $coords1 = array($location->lat,$location->lon);
            
            foreach ($this->coordinates as $coords2) {
                $distance = $this->distance($coords1, $coords2);
                if ($distance <= $this->distance) {
                    // Keep the cycle station
                    $cyclestations[] = array(
                        'distance' => $distance,
                        'location' => $coords1,
                        'name' => $location->commonName
                    );
                    break;
                }
            }
        }
        
        return $cyclestations;
    }
    
    /**
     * Save data to JSON file
     * @param string $filename Destination filename relative to current script
     */
    public function save($filename) {
        file_put_contents($filename, json_encode($this->find()));
    }
    
    /**
     * Set the max distance for found cycle hire stations
     * @param integer $distance Distance in metres
     */
    public function setDistance($distance) {
        $this->distance = $distance;
    }
    
    /**
     * Convert point value to radians
     * From https://github.com/dominikveils/coordinates
     *
     * @param $value
     *
     * return float
     */
    protected function toRadian($value) {
        return $value * (M_PI / 180);
    }
}