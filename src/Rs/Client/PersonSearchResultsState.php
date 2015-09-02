<?php

namespace Gedcomx\Rs\Client;

use Gedcomx\Atom\Entry;
use Gedcomx\Atom\Feed;
use Gedcomx\Conclusion\Person;
use Gedcomx\Rs\Client\Options\StateTransitionOption;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;

/**
 * The PersonSearchResultsState exposes management functions for person search results.
 *
 * Class PersonSearchResultsState
 *
 * @package Gedcomx\Rs\Client
 */
class PersonSearchResultsState extends GedcomxApplicationState
{
    /**
     * Constructs a new person search results state using the specified client, request, response, access token, and state factory.
     *
     * @param \GuzzleHttp\Client             $client
     * @param \GuzzleHttp\Psr7\Request    $request
     * @param \GuzzleHttp\Psr7\Response   $response
     * @param string                          $accessToken
     * @param \Gedcomx\Rs\Client\StateFactory $stateFactory
     */
    function __construct(Client $client, Request $request, Response $response, $accessToken, StateFactory $stateFactory)
    {
        parent::__construct($client, $request, $response, $accessToken, $stateFactory);
    }

    /**
     * Clones the current state instance.
     *
     * @param \GuzzleHttp\Psr7\Request  $request
     * @param \GuzzleHttp\Psr7\Response $response
     *
     * @return \Gedcomx\Rs\Client\PersonSearchResultsState
     */
    protected function reconstruct(Request $request, Response $response)
    {
        return new PersonSearchResultsState($this->client, $request, $response, $this->accessToken, $this->stateFactory);
    }

    /**
     * Returns the entity from the REST API response.
     *
     * @return \Gedcomx\Atom\Feed
     */
    protected function loadEntity()
    {
        $json = json_decode($this->response->getBody(), true);
        return new Feed($json);
    }

    /**
     * Gets the main data element represented by this state instance.
     *
     * @return \Gedcomx\Atom\Feed
     */
    protected function getScope()
    {
        return $this->getResults();
    }

    /**
     * Gets the search results from the atom feed response.
     *
     * @return Feed
     */
    public function getResults()
    {
        return $this->getEntity();
    }

    /**
     * Reads the person from the specified person model.
     *
     * @param \Gedcomx\Conclusion\Person                       $person
     * @param \Gedcomx\Rs\Client\Options\StateTransitionOption $option,...
     *
     * @return PersonState|null
     */
    public function readPersonFromConclusion( Person $person, StateTransitionOption $option = null)
    {
        $link = $person->getLink(Rel::PERSON);
        if ($link === null) {
            $link = $person->getLink(Rel::SELF);
        }
        if ($link === null || $link->getHref() === null) {
            return null;
        }

        $request = $this->createAuthenticatedGedcomxRequest("GET", $link->getHref());
        return $this->stateFactory->createState(
            "PersonState",
            $this->client,
            $request,
            $this->passOptionsTo('invoke', array($request), func_get_args()),
            $this->accessToken
        );
    }

    /**
     * Reads the person from the specified atom feed entry.
     *
     * @param \Gedcomx\Atom\Entry                              $person
     * @param \Gedcomx\Rs\Client\Options\StateTransitionOption $option,...
     *
     * @return PersonState|null
     */
    public function readPersonFromEntry( Entry $person, StateTransitionOption $option = null)
    {
        $link = $person->getLink(Rel::PERSON);
        if ($link === null) {
            $link = $person->getLink(Rel::SELF);
        }
        if ($link === null || $link->getHref() === null) {
            return null;
        }

        $request = $this->createAuthenticatedGedcomxRequest("GET", $link->getHref());
        return $this->stateFactory->createState(
            "PersonState",
            $this->client,
            $request,
            $this->passOptionsTo('invoke', array($request), func_get_args()),
            $this->accessToken
        );
    }

    /**
     * Reads the person record from the specified atom feed entry.
     *
     * @param \Gedcomx\Atom\Entry                              $person
     * @param \Gedcomx\Rs\Client\Options\StateTransitionOption $option,...
     *
     * @return RecordState|null
     */
    public function readRecord(Entry $person, StateTransitionOption $option = null)
    {
        $link = $person->getLink(Rel::RECORD);
        if ($link === null) {
            $link = $person->getLink(Rel::SELF);
        }
        if ($link === null || $link->getHref() === null) {
            return null;
        }

        $request = $this->createAuthenticatedGedcomxRequest("GET", $link->getHref());
        return $this->stateFactory->createState(
            "RecordState",
            $this->client,
            $request,
            $this->passOptionsTo('invoke', array($request), func_get_args()),
            $this->accessToken
        );
    }

    /**
     * @param \Gedcomx\Rs\Client\Options\StateTransitionOption $option,...
     *
     * @return GedcomxApplicationState The next page.
     */
    public function readNextPage(StateTransitionOption $option = null)
    {
        return $this->passOptionsTo('readPage', array(Rel::NEXT), func_get_args());
    }

    /**
     * @param \Gedcomx\Rs\Client\Options\StateTransitionOption $option,...
     *
     * @return GedcomxApplicationState The previous page.
     */
    public function readPreviousPage(StateTransitionOption $option = null)
    {
        return $this->passOptionsTo('readPage', array(Rel::PREVIOUS), func_get_args());
    }

    /**
     * @param \Gedcomx\Rs\Client\Options\StateTransitionOption $option,...
     *
     * @return GedcomxApplicationState The first page.
     */
    public function readFirstPage(StateTransitionOption $option = null)
    {
        return $this->passOptionsTo('readPage', array(Rel::FIRST), func_get_args());

    }

    /**
     * @param \Gedcomx\Rs\Client\Options\StateTransitionOption $option,...
     *
     * @return GedcomxApplicationState the last page.
     */
    public function readLastPage(StateTransitionOption $option = null)
    {
        return $this->passOptionsTo('readPage', array(Rel::LAST), func_get_args());
    }
}