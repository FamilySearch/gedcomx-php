<?php


namespace Gedcomx\Rs\Client;

use Gedcomx\Gedcomx;
use RuntimeException;

class DescendancyResultsState extends GedcomxApplicationState
{

    function __construct($client, $request, $response, $accessToken, $stateFactory)
    {
        parent::__construct($client, $request, $response, $accessToken, $stateFactory);
    }

    protected function reconstruct($request, $response)
    {
        return new DescendancyResultsState($this->client, $request, $response, $this->accessToken, $this->stateFactory);
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

    public function getTree()
    {
        throw new RuntimeException("function currently not implemented."); //todo: implement a tree-walking mechanism.
    }
}