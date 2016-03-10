<?php
namespace Mapzen;

use Guzzle\Http\Client;
use Mapzen\Response\TurnByTurnResponse;

/**
 * Default Mapzen client implementation
 */
class MapzenClient implements MapzenClientInterface
{

    /** @var string */
    const AMPLITUDE_URL = 'http://valhalla.mapzen.com/';

    /**
     * @var string
     */
    protected $apiKey = '';

    /**
     * MapzenClient constructor.
     * @param null|string $apiKey
     */
    public function __construct($apiKey = null)
    {
        if (null !== $apiKey) {
            $this->setApiKey($apiKey);
        }
    }

    /**
     * @param string $apiKey
     */
    public function setApiKey($apiKey)
    {
        $this->apiKey = $apiKey;
    }

    /**
     * @param Request\TurnByTurn $message
     * @return Response\TurnByTurnResponse
     * @throws Exceptions\ClientException
     */
    public function turnByTurn(Request\TurnByTurn $message)
    {
        $client = new Client($message->getServiceDomain());
        $request = $client->post($message->getMethodName() . '?api_key='.$this->apiKey, null, $message->getFormattedBody());
        $response = $request->send();
        if (!$response->isError()) {
            return new TurnByTurnResponse($response->getBody(true), true);
        }
        throw new Exceptions\ClientException();
    }
}
