<?php

namespace Gedcomx\Extensions\FamilySearch\Rs\Client\FamilyTree;

use Gedcomx\Extensions\FamilySearch\FamilySearchPlatform;
use Gedcomx\Rs\Client\PersonChildrenState;
use Guzzle\Http\Message\Request;
use Guzzle\Http\Message\Response;

class FamilyTreePersonChildrenState extends PersonChildrenState
{
    /**
     * Clone this instance of FamilyTreePersonChildrenState
     *
     * @param \Guzzle\Http\Message\Request  $request
     * @param \Guzzle\Http\Message\Response $response
     *
     * @return FamilyTreePersonChildrenState
     */
    protected function reconstruct(Request $request, Response $response)
    {
        return new FamilyTreePersonChildrenState($this->client, $request, $response, $this->accessToken, $this->stateFactory);
    }

    /**
     * Parse the JSON data from the response body
     *
     * @return FamilySearchPlatform
     */
    protected function loadEntity()
    {
        $json = json_decode($this->response->getBody(), true);

        return new FamilySearchPlatform($json);
    }

    /**
     * Return the ChildAndParentsRelationship objects from the response
     *
     * @return ChildAndParentsRelationship[]|null
     */
    public function getChildAndParentsRelationships()
    {
        return $this->getEntity() == null ? null : $this->getEntity()->getChildAndParentsRelationships();
    }

    /**
     * Find the relationships in which the given person is a child
     *
     * @param \Gedcomx\Extensions\FamilySearch\Rs\Client\FamilyTree\Person $child
     *
     * @return \Gedcomx\Extensions\FamilySearch\Rs\Client\FamilyTree\ChildAndParentsRelationship|null
     */
    public function findChildAndParentsRelationshipTo(Person $child)
    {
        $relationships = $this->getChildAndParentsRelationships();
        if ($relationships != null) {
            foreach ($relationships as $relation) {
                $personReference = $relation->getChild();
                if ($personReference != null) {
                    $reference = $personReference->getResource();
                    if ($reference == "#" . $child->getId()) {
                        return $relation;
                    }
                }
            }
        }
        return null;
    }

} 