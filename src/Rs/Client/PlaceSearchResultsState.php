<?php

namespace Gedcomx\Rs\Client;

use Gedcomx\Atom\Entry;
use Gedcomx\Atom\Feed;
use Gedcomx\Rs\Client\Options\StateTransitionOption;
use Guzzle\Http\Message\Request;
use Guzzle\Http\Message\Response;

class PlaceSearchResultsState extends GedcomxApplicationState
{

    protected function reconstruct(Request $request, Response $response)
    {
        return new PlaceSearchResultsState($this->client, $request, $response, $this->accessToken, $this->stateFactory);
    }

    protected function loadEntity()
    {
        $json = json_decode($this->response->getBody(), true);
        return new Feed($json);
    }

    protected function getScope()
    {
        return $this->getEntity();
    }

    public function getResults()
    {
        return $this->getEntity()->getEntries();
    }

    /**
     * @param \Gedcomx\Atom\Entry                              $place
     * @param \Gedcomx\Rs\Client\Options\StateTransitionOption $option
     *
     * @return \Gedcomx\Rs\Client\PlaceDescriptionState|null
     */
    public function readPlaceDescription(Entry $place, StateTransitionOption $option = null)
    {
        $link = $place->getLink(Rel::DESCRIPTION);
        $link = $link == null ? $place->getLink(Rel::SELF) : $link;
        if ($link == null || $link->getHref() == null) {
            return null;
        }

        $request = $this->createAuthenticatedGedcomxRequest(Request::GET, $link->getHref());

        return $this->stateFactory->createState(
            'PlaceDescriptionState',
            $this->client,
            $request,
            $this->passOptionsTo('invoke', array($request), func_get_args()),
            $this->accessToken
        );
    }
}