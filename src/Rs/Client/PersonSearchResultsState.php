<?php

namespace Gedcomx\Rs\Client;

use Gedcomx\Atom\Entry;
use Gedcomx\Atom\Feed;
use Gedcomx\Conclusion\Person;
use Gedcomx\Rs\Client\Options\StateTransitionOption;
use Guzzle\Http\Client;
use Guzzle\Http\Message\Request;
use Guzzle\Http\Message\Response;

class PersonSearchResultsState extends GedcomxApplicationState
{

    function __construct(Client $client, Request $request, Response $response, $accessToken, StateFactory $stateFactory)
    {
        parent::__construct($client, $request, $response, $accessToken, $stateFactory);
    }

    protected function reconstruct(Request $request, Response $response)
    {
        return new PersonSearchResultsState($this->client, $request, $response, $this->accessToken, $this->stateFactory);
    }

    protected function loadEntity()
    {
        $json = json_decode($this->response->getBody(), true);
        return new Feed($json);
    }

    protected function getScope()
    {
        return $this->getResults();
    }

    /**
     * @return Feed
     */
    public function getResults()
    {
        return $this->getEntity();
    }

    /**
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