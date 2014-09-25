<?php


namespace Gedcomx\Rs\Client;

use Gedcomx\Gedcomx;
use Gedcomx\Conclusion\Person;
use Gedcomx\Conclusion\Relationship;
use RuntimeException;

class PersonChildrenState extends GedcomxApplicationState
{

    function __construct($client, $request, $response, $accessToken, $stateFactory)
    {
        parent::__construct($client, $request, $response, $accessToken, $stateFactory);
    }

    protected function reconstruct($request, $response)
    {
        return new PersonChildrenState($this->client, $request, $response, $this->accessToken, $this->stateFactory);
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
     * @param Person $child
     * @return Relationship|null
     */
    public function findRelationshipTo($child)
    {
        $relationships = $this->getRelationships();
        if ($relationships) {
            foreach ($relationships as $relationship) {
                if ($relationship->getPerson2() && $relationship->getPerson2()->getResource() && $relationship->getPerson2()->getResource() == '#' . $child->getId()) {
                    return $relationship;
                }
            }
        }

        return null;
    }

    /**
     * @return PersonState
     */
    public function readPerson()
    {
        throw new RuntimeException("function currently not implemented."); //todo: implement
    }

    /**
     * @param Person $child
     * @return PersonState
     */
    public function readChild($child)
    {
        throw new RuntimeException("function currently not implemented."); //todo: implement
    }

    /**
     * @param Relationship $relationship
     * @return RelationshipState
     */
    public function readRelationship($relationship)
    {
        throw new RuntimeException("function currently not implemented."); //todo: implement
    }

    /**
     * @param Relationship $relationship
     * @return RelationshipState
     */
    public function removeRelationship($relationship)
    {
        throw new RuntimeException("function currently not implemented."); //todo: implement
    }

    /**
     * @param Person $child
     * @return RelationshipState
     */
    public function removeRelationshipTo($child)
    {
        throw new RuntimeException("function currently not implemented."); //todo: implement
    }

}