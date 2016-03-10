<?php

namespace Mapzen\Utils;

class MapzenPolyline
{
    /**
     * Reverse Mapzen Polyline algorithm on encoded string
     *
     * @param string $encoded
     * @return array points
     */
    public function decode($encoded)
    {
        $length = strlen($encoded);

        $index = 0;
        $points = array();
        $latitude = 0;
        $longitude = 0;

        while ($index < $length) {
            $byte = 0;

            $shift = 0;
            $result = 0;
            do {
                $byte = ord(substr($encoded, $index++)) - 63;

                $result |= ($byte & 0x1f) << $shift;
                $shift += 5;
            } while ($byte >= 0x20);

            $latitudeChange = (($result & 1) ? ~($result >> 1) : ($result >> 1));

            $latitude += $latitudeChange;

            $shift = 0;
            $result = 0;
            do {
                $byte = ord(substr($encoded, $index++)) - 63;
                $result |= ($byte & 0x1f) << $shift;
                $shift += 5;
            } while ($byte >= 0x20);

            $longitudeChange = (($result & 1) ? ~($result >> 1) : ($result >> 1));
            $longitude += $longitudeChange;

            $points[] = array($latitude * 1e-6, $longitude * 1e-6);
        }

        return $points;
    }
}
