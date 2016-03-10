<?php
namespace Mapzen\Response;

use Mapzen\Utils\GmapPolyline;
use Mapzen\Utils\MapzenPolyline;

/**
 * Request message for Mapzen TurnByTurn method
 */
class TurnByTurnResponse
{
    const MAX_POINTS = 300;

    protected $response = array();

    /**
     * TurnByTurnResponse constructor.
     * @param $response
     */
    public function __construct($response = array())
    {
        if (!is_array($response)) {
            $response = json_decode($response, true);
        }
        $this->response = $response;
    }

    /**
     * @return array|mixed
     */
    public function getResponse()
    {
        return $this->response;
    }

    /**
     * Get route points
     * @return bool|string
     */
    protected function getShapes()
    {
        if (isset($this->response['trip']['legs']) && count($this->response['trip']['legs'])) {
            $shape = '';
            foreach ($this->response['trip']['legs'] as $leg) {
                $shape .= $leg['shape'];
            }
            return $shape;
        }
        return false;
    }

    /**
     * Get decoded route points
     * @return array|bool
     */
    public function getDecodedRoutePoints()
    {
        $points = array();
        if (isset($this->response['trip']['legs']) && count($this->response['trip']['legs'])) {
            $mapzenPolyline = new MapzenPolyline();
            foreach ($this->response['trip']['legs'] as $key => $leg) {
                $points[$key] = $mapzenPolyline->decode($leg['shape']);
            }
            return $points;
        }
        return false;
    }

    /**
     * Get encoded route points for GMaps
     * @return bool|string
     */
    public function getGmapsPolylineRoutePoints()
    {
        if (false !== ($points = $this->getDecodedRoutePoints())) {
            $count = $this->getNumPoints($points);
            if ($count > self::MAX_POINTS) {
                $ratio = ceil($count / self::MAX_POINTS);
                $result = array();
                foreach ($points as $key => $point) {
                    $tempPoints = array();
                    for ($i = 0; $i < count($point); $i = $i + $ratio) {
                        $tempPoints[] = $point[$i];
                    }
                    $result[$key] = $tempPoints;
                }
            } else {
                $result = $points;
            }
            $points = call_user_func_array('array_merge', $result);
            $gmapsPolyline = new GmapPolyline();
            return $gmapsPolyline->encode($points);
        }
        return false;
    }

    /**
     * Get number of points for a two-dimensions array
     * @param array $points
     * @return number
     */
    protected function getNumPoints($points)
    {
        return array_sum(array_map(function ($item) {
            return count($item);
        }, $points));
    }
}
