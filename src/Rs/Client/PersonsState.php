<?php


namespace Gedcomx\Rs\Client;

use Gedcomx\Gedcomx;
use Gedcomx\Conclusion\Person;
use RuntimeException;

class PersonsState extends GedcomxApplicationState
{

    function __construct($client, $request, $response, $accessToken, $stateFactory)
    {
        parent::__construct($client, $request, $response, $accessToken, $stateFactory);
    }

    protected function reconstruct($request, $response)
    {
        return new PersonsState($this->client, $request, $response, $this->accessToken, $this->stateFactory);
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

    /**
     * @return Person[]|null
     */
    public function getPersons()
    {
        if ($this->entity) {
            return $this->entity->getPersons();
        }

        return null;
    }

    /**
     * @return CollectionState|null
     */
    public function readCollection()
    {
        throw new RuntimeException("function currently not implemented."); //todo: implement
    }

    /**
     * @param Person|Gedcomx $person
     * @return PersonState|null
     */
    public function addPerson($person)
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