<?php


namespace Gedcomx\Rs\Api;

use Gedcomx\Gedcomx;
use RuntimeException;

class AncestryResultsState extends GedcomxApplicationState
{

    function __construct($client, $request, $response, $accessToken, $stateFactory)
    {
        parent::__construct($client, $request, $response, $accessToken, $stateFactory);
    }

    protected function reconstruct($request, $response)
    {
        return new AncestryResultsState($this->client, $request, $response, $this->accessToken, $this->stateFactory);
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