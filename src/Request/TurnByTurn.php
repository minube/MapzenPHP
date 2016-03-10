<?php
namespace Mapzen\Request;

/**
 * Event to be tracked by Mapzen
 */
class TurnByTurn extends RequestAbstract
{
    const SERVICE_DOMAIN = 'http://valhalla.mapzen.com/';
    const METHOD_NAME = 'route';

    const COSTING_DEFAULT = 'auto';
    const COSTING_PEDESTRIAN = 'pedestrian';

    const MAX_DISTANCE_PEDESTRIAN = 250000;
    const MAX_POINTS_PEDESTRIAN = 50;

    const LOCATION_TYPE_THOUGH = 'though';
    const LOCATION_TYPE_BREAK = 'break';

    /** @var array */
    protected $locations = array();

    /** @var string */
    protected $costing = self::COSTING_DEFAULT;

    /** @var string */
    protected $units = self::DEFAULT_UNITS;

    /**
     * Add location
     * @param float $latitude
     * @param float $longitude
     * @param string $type
     * @return $this
     */
    public function addLocation($latitude, $longitude, $type = self::LOCATION_TYPE_THOUGH)
    {
        $this->locations[] = array(
            'lat' => $latitude,
            'lon' => $longitude,
            'type' => $type,
        );
        return $this;
    }

    /**
     * @param array $points
     * @return $this
     */
    public function setLocations($points)
    {
        foreach ($points as $point) {
            $this->addLocation($point[0], $point[1]);
        }
        return $this;
    }

    /**
     * @param string $name
     * @param string $value
     * @return $this
     */
    public function set($name, $value)
    {
        if (property_exists(get_class(), $name)) {
            $this->{$name} = $value;
        }
        return $this;
    }

    /**
     * Format entity
     * @return string
     */
    public function getFormattedBody()
    {
        return json_encode(
            array(
                'locations' => $this->getLocations(),
                'costing' => $this->getCosting(),
                'directions_options' => array(
                    'units' => $this->getUnits()
                ),
            )
        );
    }

    /**
     * @return mixed
     */
    protected function getCosting()
    {
        $numberLocations = count($this->getLocations());
        if (
            $numberLocations < self::MAX_POINTS_PEDESTRIAN &&
            $this->getDistance() < self::MAX_DISTANCE_PEDESTRIAN
        ) {
            return self::COSTING_PEDESTRIAN;
        }
        return self::COSTING_DEFAULT;
    }

    /**
     * Get route distance
     * @return int
     */
    public function getDistance()
    {
        $distance = 0;
        if (count($this->locations) > 1) {
            for ($i = 1; $i < count($this->locations); $i++) {
                $previousCoordinate = $this->locations[$i - 1];
                $currentCoordinate = $this->locations[$i];
                $distance += $this->haversineGreatCircleDistance(
                    $currentCoordinate['lat'], $currentCoordinate['lon'],
                    $previousCoordinate['lat'], $previousCoordinate['lon']
                );
            }
        }
        return $distance;
    }

    /**
     * Haversine great circle distance
     * @param double $latitudeFrom
     * @param double $longitudeFrom
     * @param double $latitudeTo
     * @param double $longitudeTo
     * @param int $earthRadius
     * @return int
     */
    protected function haversineGreatCircleDistance(
        $latitudeFrom, $longitudeFrom, $latitudeTo, $longitudeTo, $earthRadius = 6378137)
    {
        $deltaLatitude = deg2rad($latitudeTo - $latitudeFrom);
        $deltaLongitude = deg2rad($longitudeTo - $longitudeFrom);

        $alpha = sin($deltaLatitude / 2) * sin($deltaLatitude / 2) +
            cos(deg2rad($latitudeFrom)) * cos(deg2rad($latitudeTo)) * sin($deltaLongitude / 2) * sin($deltaLongitude / 2);
        return $earthRadius * atan2(sqrt($alpha), sqrt(1 - $alpha));
    }
}
