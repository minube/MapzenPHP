Mapzen PHP
==========

How to use it
-------------

To extract all the points between different locations
```php
$client = new \Mapzen\MapzenClient($apiKey);

$turnByTurn = new \Mapzen\Request\TurnByTurn();
foreach ($points as $point) {
        $turnByTurn->addLocation($point['latitude'], $point['longitude']);
}
$result = $client->turnByTurn($turnByTurn);
$points = $result->getDecodedRoutePoints();
```
