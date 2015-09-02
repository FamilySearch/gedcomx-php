<?php

namespace Gedcomx\Rs\Client;

use Gedcomx\Atom\Entry;
use Gedcomx\Atom\Feed;
use Gedcomx\Rs\Client\Options\StateTransitionOption;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;

/**
 * The PlaceSearchResultsState exposes management functions for place search results.
 */
class PlaceSearchResultsState extends GedcomxApplicationState
{
    /**
     * Constructs a new place search results state using the specified request and response.
     * @param Request $request
     * @param Response $response
     * @return PlaceSearchResultsState
     */
    protected function reconstruct(Request $request, Response $response)
    {
        return new PlaceSearchResultsState($this->client, $request, $response, $this->accessToken, $this->stateFactory);
    }

    /**
     * Returns the entity from the REST API response.
     *
     * @return Feed
     */
    protected function loadEntity()
    {
        $json = json_decode($this->response->getBody(), true);
        return new Feed($json);
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
     * Gets the results of the current search response.
     *
     * @return mixed
     */
    public function getResults()
    {
        return $this->getEntity()->getEntries();
    }

    /**
     * Reads the place description described by a single entry from the results.
     *
     * @param \Gedcomx\Atom\Entry                              $place
     * @param \Gedcomx\Rs\Client\Options\StateTransitionOption $option,...
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

        $request = $this->createAuthenticatedGedcomxRequest('GET', $link->getHref());

        return $this->stateFactory->createState(
            'PlaceDescriptionState',
            $this->client,
            $request,
            $this->passOptionsTo('invoke', array($request), func_get_args()),
            $this->accessToken
        );
    }
}