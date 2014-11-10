<?php


namespace Gedcomx\Rs\Client;

use Gedcomx\Gedcomx;
use Guzzle\Http\Client;
use Guzzle\Http\Message\Request;
use Guzzle\Http\Message\Response;

class RecordsState extends GedcomxApplicationState
{

    function __construct(Client $client, Request $request, Response $response, $accessToken, StateFactory $stateFactory)
    {
        parent::__construct($client, $request, $response, $accessToken, $stateFactory);
    }

    protected function reconstruct(Request $request, Response $response)
    {
        return new RecordsState($this->client, $request, $response, $this->accessToken, $this->stateFactory);
    }

    protected function loadEntity()
    {
        $json = json_decode($this->response->getBody(), true);
        return new Gedcomx($json);
    }

    protected function getScope()
    {
        return $this->getEntity();
    }

    public function getSelfRel()
    {
        return Rel::RECORDS;
    }

}