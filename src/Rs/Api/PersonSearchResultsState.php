<?php


namespace Gedcomx\Rs\Api;

use Gedcomx\Atom\Entry;
use Gedcomx\Atom\Feed;
use Gedcomx\Conclusion\Person;
use Gedcomx\Gedcomx;
use RuntimeException;

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
        return new Gedcomx($json);
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
     * @param Entry $entry
     * @return PersonState
     */
    public function readPersonOfEntry($entry)
    {
        throw new RuntimeException("function currently not implemented."); //todo: implement
    }

    /**
     * @param Person $person
     * @return PersonState
     */
    public function readPerson($person)
    {
        throw new RuntimeException("function currently not implemented."); //todo: implement
    }

    public function readNextPage()
    {
        return parent::readNextPage();
    }

    public function readPreviousPage()
    {
        return parent::readPreviousPage();
    }

    public function readFirstPage()
    {
        return parent::readFirstPage();
    }

    public function readLastPage()
    {
        return parent::readLastPage();
    }


}