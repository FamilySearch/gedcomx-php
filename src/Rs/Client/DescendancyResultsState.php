<?php

namespace Gedcomx\Rs\Client;

use Gedcomx\Gedcomx;
use Gedcomx\Rs\Client\Util\DescendancyTree;
use Guzzle\Http\Client;
use Guzzle\Http\Message\Request;
use Guzzle\Http\Message\Response;

/**
 * The DescendancyResultsState exposes management functions for descendancy results.
 *
 * Class DescendancyResultsState
 *
 * @package Gedcomx\Rs\Client
 */
class DescendancyResultsState extends GedcomxApplicationState
{
    /**
     * Constructs a new descendancy results state using the specified client, request, response, access token, and state factory.
     *
     * @param \Guzzle\Http\Client             $client
     * @param \Guzzle\Http\Message\Request    $request
     * @param \Guzzle\Http\Message\Response   $response
     * @param string                          $accessToken
     * @param \Gedcomx\Rs\Client\StateFactory $stateFactory
     */
    function __construct(Client $client, Request $request, Response $response, $accessToken, StateFactory $stateFactory)
    {
        parent::__construct($client, $request, $response, $accessToken, $stateFactory);
    }

    /**
     * Clones the current state instance.
     * @param \Guzzle\Http\Message\Request  $request
     * @param \Guzzle\Http\Message\Response $response
     *
     * @return \Gedcomx\Rs\Client\DescendancyResultsState
     */
    protected function reconstruct(Request $request, Response $response)
    {
        return new DescendancyResultsState($this->client, $request, $response, $this->accessToken, $this->stateFactory);
    }

    /**
     * Returns the entity from the REST API response.
     *
     * @return \Gedcomx\Gedcomx
     */
    protected function loadEntity()
    {
        $json = json_decode($this->response->getBody(), true);
        return new Gedcomx($json);
    }

    /**
     * Gets the main data element represented by this state instance.
     *
     * @return object
     */
    protected function getScope()
    {
        return $this->getEntity();
    }

    /**
     * Gets the tree represented by the REST API response.
     *
     * @return \Gedcomx\Rs\Client\Util\DescendancyTree
     */
    public function getTree()
    {
        if ($this->getEntity() != null) {
            return new DescendancyTree($this->getEntity());
        }
    }
}