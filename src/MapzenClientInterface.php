<?php
namespace Mapzen;

/**
 * Represents an Mapzen client.
 */
interface MapzenClientInterface
{
    /**
     * @param Request\TurnByTurn $route
     * @return Response\TurnByTurnResponse
     */
    public function turnByTurn(Request\TurnByTurn $route);
}
