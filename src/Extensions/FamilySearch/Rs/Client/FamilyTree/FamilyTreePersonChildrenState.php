<?php

namespace Gedcomx\Extensions\FamilySearch\Rs\Client\FamilyTree;

use Gedcomx\Extensions\FamilySearch\FamilySearchPlatform;
use Gedcomx\Rs\Client\PersonChildrenState;
use Guzzle\Http\Message\Request;
use Guzzle\Http\Message\Response;

class FamilyTreePersonChildrenState extends PersonChildrenState
{
    /**
     * @param Request $request
     * @param Response $response
     *
     * @return FamilyTreePersonChildrenState
     */
    protected function reconstruct(Request $request, Response $response)
    {
        return new FamilyTreePersonChildrenState($this->client, $request, $response, $this->accessToken, $this->stateFactory);
    }

    /**
     * @return FamilySearchPlatform
     */
    protected function loadEntity()
    {
        $json = json_decode($this->response->getBody(), true);

        return new FamilySearchPlatform($json);
    }

    /**
     * @return ChildAndParentsRelationship[]|null
     */
    public function getChildAndParentsRelationships()
    {
        return $this->getEntity() == null ? null : $this->getEntity()->getChildAndParentsRelationships();
    }

    public function findChildAndParentsRelationshipTo(Person $child)
    {
        $relationships = $this->getChildAndParentsRelationships();
        if ($relationships != null) {
            foreach ($relationships as $relation) {
                $personReference = $relation->getChild();
                if ($personReference != null) {
                    $reference = $personReference->getResource()->toString();
                    if ($reference == "#" . child . getId()) {
                        return $relation;
                    }
                }
            }
        }
        return null;
    }

} 