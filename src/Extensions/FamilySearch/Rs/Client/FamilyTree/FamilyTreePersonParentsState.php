<?php

namespace Gedcomx\Extensions\FamilySearch\Rs\Client\FamilyTree;

use Gedcomx\Conclusion\Person;
use Gedcomx\Extensions\FamilySearch\FamilySearchPlatform;
use Gedcomx\Extensions\FamilySearch\Platform\Tree\ChildAndParentsRelationship;
use Gedcomx\Rs\Client\PersonParentsState;
use Guzzle\Http\Message\Request;
use Guzzle\Http\Message\Response;

class FamilyTreePersonParentsState extends PersonParentsState
{
    /**
     * Clone this instance of FamilyTreePersonParentsState
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
     * Parse the JSON data in the response body
     *
     * @return FamilySearchPlatform
     */
    protected function loadEntity()
    {
        $json = json_decode($this->response->getBody(), true);

        return new FamilySearchPlatform($json);
    }

    /**
     * Return the ChildAndParentsRelationship objects on this state object
     *
     * @return ChildAndParentsRelationship[]|null
     */
    public function getChildAndParentsRelationships()
    {
        return $this->getEntity() == null ? null : $this->getEntity()->getChildAndParentsRelationships();
    }

    /**
     * Return the
     * @param \Gedcomx\Conclusion\Person $spouse
     *
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