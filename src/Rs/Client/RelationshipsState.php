<?php


namespace Gedcomx\Rs\Client;

use Gedcomx\Conclusion\Person;
use Gedcomx\Conclusion\Relationship;
use Gedcomx\Gedcomx;
use Gedcomx\Rs\Client\Options\StateTransitionOption;
use Guzzle\Http\Client;
use Guzzle\Http\Message\Request;
use Guzzle\Http\Message\Response;
use RuntimeException;

class RelationshipsState extends GedcomxApplicationState
{

    function __construct(Client $client, Request $request, Response $response, $accessToken, StateFactory $stateFactory)
    {
        parent::__construct($client, $request, $response, $accessToken, $stateFactory);
    }

    protected function reconstruct(Request $request, Response $response)
    {
        return new RelationshipsState($this->client, $request, $response, $this->accessToken, $this->stateFactory);
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

    public function getSelfRel(){
        return Rel::RELATIONSHIP;
    }
    /**
     * @return Relationship[]|null
     */
    public function getRelationships()
    {
        if ($this->entity) {
            return $this->entity->getRelationships();
        }

        return null;
    }

    /**
     * @return CollectionState|null The collection that contains these collections.
     */
    public function readCollection()
    {
        throw new RuntimeException("function currently not implemented."); //todo: implement
    }

    /**
     * @param Relationship|Gedcomx $relationship
     *
     * @throws \RuntimeException
     */
    public function addRelationship(Relationship $relationship)
    {
        throw new RuntimeException("GedcomX function currently not implemented in the API. Use FamilyTreeRelationshipState.");
    }

    /**
     * @param Person $person1
     * @param Person $person2
     *
     * @throws \RuntimeException
     */
    public function addSpouseRelationship(Person $person1, Person $person2)
    {
        throw new RuntimeException("GedcomX function currently not implemented in the API. Use FamilyTreeRelationshipState.");
    }

    /**
     * @param \Gedcomx\Conclusion\Person $person1
     * @param \Gedcomx\Conclusion\Person $person2
     *
     * @throws \RuntimeException
     */
    public function addParentRelationship(Person $person1, Person $person2)
    {
        throw new RuntimeException("GedcomX function currently not implemented in the API. Use FamilyTreeRelationshipState.");
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