<?php

namespace Gedcomx\Extensions\FamilySearch\Rs\Client\FamilyTree;

use Gedcomx\Rs\Client\PersonParentsState;
use Guzzle\Http\Message\Request;
use Guzzle\Http\Message\Response;

class FamilyTreePersonParentsState extends PersonParentsState
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

    /**
     * @param Person $spouse
     * @return ChildAndParentsRelationship|null
     */
    public function findChildAndParentsRelationshipTo(Person $spouse)
    {
        $relationships = $this->getChildAndParentsRelationships();
        if (relationships != null) {
            foreach ($relationships as $relation) {
                $personReference = $relation->getFather();
                if ($personReference != null) {
                    $reference = $personReference->getResource()->toString();
                    if ($reference == "#" . $spouse->getId()) {
                        return $relation;
                    }
                }
                $personReference = $relation->getMother();
                if ($personReference != null) {
                    $reference = $personReference->getResource()->toString();
                    if ($reference == "#" . $spouse->getId()) {
                        return $relation;
                    }
                }
            }
        }
        return null;
    }


} 