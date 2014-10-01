<?php

namespace Gedcomx\Rs\Client;

use Gedcomx\Atom\Entry;
use Gedcomx\Atom\Feed;
use Gedcomx\Conclusion\Person;

class PersonSearchResultsState extends GedcomxApplicationState
{

    function __construct($client, $request, $response, $accessToken, $stateFactory)
    {
        parent::__construct($client, $request, $response, $accessToken, $stateFactory);
    }

    protected function reconstruct($request, $response)
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
     * @param Person                $person               Gedcomx\Conclusion\Person
     * @param StateTransitionOption $transitionOption,... zero or more StateTransitionOption objects
     *
     * @return PersonState|null
     */
    public function readPersonFromConclusion( Person $person, StateTransitionOption $transitionOption = null)
    {
        $link = $person->getLink(Rel::PERSON);
        if ($link === null) {
            $link = $person->getLink(Rel::SELF);
        }
        if ($link === null || $link->getHref() === null) {
            return null;
        }

        $transitionOptions = $this->getTransitionOptions(func_get_args());
        $request = $this->createAuthenticatedGedcomxRequest("GET", $link->getHref());
        return $this->stateFactory->buildPersonState(
            $this->client,
            $request,
            $this->invoke($request, $transitionOptions),
            $this->accessToken
        );
    }

    /**
     * @param Entry                 $person               Gedcomx\Atom\Entry
     * @param StateTransitionOption $transitionOption,... zero or more StateTransitionOption objects
     *
     * @return PersonState|null
     */
    public function readPersonFromEntry( Entry $person, StateTransitionOption $transitionOption = null)
    {
        $link = $person->getLink(Rel::PERSON);
        if ($link === null) {
            $link = $person->getLink(Rel::SELF);
        }
        if ($link === null || $link->getHref() === null) {
            return null;
        }

        $transitionOptions = $this->getTransitionOptions(func_get_args());
        $request = $this->createAuthenticatedGedcomxRequest("GET", $link->getHref());
        return $this->stateFactory->buildPersonState(
            $this->client,
            $request,
            $this->invoke($request,$transitionOptions),
            $this->accessToken
        );
    }

    /**
     * @param Entry $person Gedcomx\Atom\Entry
     * @param StateTransitionOption $transitionOption,... zero or more StateTransitionOption objects
     *
     * @return RecordState|null
     */
    public function readRecord(Entry $person, StateTransitionOption $transitionOption = null)
    {
        $link = $person->getLink(Rel::RECORD);
        if ($link === null) {
            $link = $person->getLink(Rel::SELF);
        }
        if ($link === null || $link->getHref() === null) {
            return null;
        }

        $transitionOptions = $this->getTransitionOptions(func_get_args());
        $request = $this->createAuthenticatedGedcomxRequest("GET", $link->getHref());
        return $this->stateFactory->buildRecordState(
            $this->client,
            $request,
            $this->invoke($request,$transitionOptions),
            $this->accessToken
        );
    }
}