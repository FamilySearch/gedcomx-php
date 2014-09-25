<?php


namespace Gedcomx\Rs\Client;

use Gedcomx\Conclusion\Relationship;
use Gedcomx\Gedcomx;
use RuntimeException;

class RelationshipsState extends GedcomxApplicationState
{

    function __construct($client, $request, $response, $accessToken, $stateFactory)
    {
        parent::__construct($client, $request, $response, $accessToken, $stateFactory);
    }

    protected function reconstruct($request, $response)
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
     * @return RelationshipState|null
     */
    public function addRelationship($relationship)
    {
        throw new RuntimeException("function currently not implemented."); //todo: implement
    }

    /**
     * @param PersonState $person1
     * @param PersonState $person2
     * @return RelationshipState|null
     */
    public function addSpouseRelationship($person1, $person2)
    {
        throw new RuntimeException("function currently not implemented."); //todo: implement
    }

    /**
     * @param PersonState $person1
     * @param PersonState $person2
     * @return RelationshipState|null
     */
    public function addParentRelationship($person1, $person2)
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