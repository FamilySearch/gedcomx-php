<?php

namespace Gedcomx\Rs\Client;

use Gedcomx\Gedcomx;
use Gedcomx\Rs\Client\Util\AncestryTree;
use Guzzle\Http\Client;
use Guzzle\Http\Message\Request;
use Guzzle\Http\Message\Response;

/**
 * The AncestryResultsState exposes management functions for ancestry results.
 *
 * Class AncestryResultsState
 *
 * @package Gedcomx\Rs\Client
 */
class AncestryResultsState extends GedcomxApplicationState
{
    /**
     * Constructs a new ancestry results state using the specified client, request, response, access token, and state factory.
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
     * @return \Gedcomx\Rs\Client\AncestryResultsState
     */
    protected function reconstruct(Request $request, Response $response)
    {
        return new AncestryResultsState($this->client, $request, $response, $this->accessToken, $this->stateFactory);
    }

    /**
     * Gets the rel name for the current state instance.
     *
     * @return string
     */
    public function getSelfRel()
    {
        return Rel::ANCESTRY;
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
     * @return \Gedcomx\Rs\Client\Util\AncestryTree
     */
    public function getTree()
    {
        if ($this->getEntity()) {
            return new AncestryTree($this->getEntity());
        }

        return null;
    }

    /**
     * Reads the person at the specified one-based index number.
     *
     * @param                                          $ancestorNumber
     * @param \Gedcomx\Rs\Client\StateTransitionOption $option
     *
     * @return mixed|null
     */
    public function readPerson($ancestorNumber, StateTransitionOption $option = null)
    {
        $ancestor = $this->getTree()->getAncestor(ancestorNumber);
        if ($ancestor == null) {
            return null;
        }

        $selfLink = $ancestor->getPerson()->getLink(Rel::PERSON);
        if ($selfLink == null || $selfLink->getHref() == null) {
            $selfLink = $ancestor->getPerson()->getLink(Rel::SELF);
        }

        $personUri = null;
        if ($selfLink && $selfLink->getHref()) {
            $personUri = $selfLink->getHref();
        }
        if (!$personUri) {
            return null;
        }

        $request = $this->createAuthenticatedGedcomxRequest(Request::GET, $personUri);

        return $this->stateFactory->createState(
            "PersonState",
            $this->client,
            $request,
            $this->passOptionsTo('invoke', array($request), func_get_args()),
            $this->accessToken
        );
    }
}