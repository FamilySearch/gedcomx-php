<?php

namespace Gedcomx\Rs\Client;

use Gedcomx\Gedcomx;
use Gedcomx\Rs\Client\Util\AncestryTree;
use Guzzle\Http\Client;
use Guzzle\Http\Message\Request;
use Guzzle\Http\Message\Response;

class AncestryResultsState extends GedcomxApplicationState
{

    function __construct(Client $client, Request $request, Response $response, $accessToken, StateFactory $stateFactory)
    {
        parent::__construct($client, $request, $response, $accessToken, $stateFactory);
    }

    protected function reconstruct(Request $request, Response $response)
    {
        return new AncestryResultsState($this->client, $request, $response, $this->accessToken, $this->stateFactory);
    }

    public function getSelfRel()
    {
        return Rel::ANCESTRY;
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
        if ($this->getEntity()) {
            return new AncestryTree($this->getEntity());
        }

        return null;
    }

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