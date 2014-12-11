<?php

namespace Gedcomx\Extensions\FamilySearch\Rs\Client\FamilyTree;

use Gedcomx\Common\ResourceReference;
use Gedcomx\Conclusion\Relationship;
use Gedcomx\Extensions\FamilySearch\FamilySearchPlatform;
use Gedcomx\Extensions\FamilySearch\Platform\Tree\ChildAndParentsRelationship;
use Gedcomx\Rs\Client\Exception\GedcomxApplicationException;
use Gedcomx\Rs\Client\Options\StateTransitionOption;
use Gedcomx\Rs\Client\PersonState;
use Gedcomx\Rs\Client\RelationshipsState;
use Gedcomx\Types\RelationshipType;
use Guzzle\Http\Message\Request;

/**
 * The FamilyTreeRelationshipsState exposes management functions for family tree relationships.
 *
 * Class FamilyTreeRelationshipsState
 *
 * @package Gedcomx\Extensions\FamilySearch\Rs\Client\FamilyTree
 */
class FamilyTreeRelationshipsState extends RelationshipsState {

    /**
     * Gets the child and parents relationships of the current state instance.
     *
     * @return ChildAndParentsRelationship[]|null
     */
    public function getChildAndParentsRelationships() {
        if ($this->getEntity() != null) {
            return $this->getEntity()->getChildAndParentsRelationships();
        }

        return null;
    }

    /**
     * Adds the specified relationship to this collection of relationships. To add a RelationshipType::PARENTCHILD
     * relationship, use addChildAndParents() or addChildAndParentsRelationship() instead.
     *
     * @param \Gedcomx\Conclusion\Relationship                 $relationship
     * @param \Gedcomx\Rs\Client\Options\StateTransitionOption $option,...
     *
     * @return \Gedcomx\Extensions\FamilySearch\Rs\Client\FamilyTree\FamilyTreeRelationshipsState
     * @throws GedcomxApplicationException
     */
    public function addRelationship(Relationship $relationship, StateTransitionOption $option = null)
    {
        if ($relationship->getKnownType() == RelationshipType::PARENTCHILD) {
            throw new GedcomxApplicationException("FamilySearch Family Tree doesn't support adding parent-child relationships. You must instead add a child-and-parents relationship.");
        }

        return $this->passOptionsTo('addRelationship', array($relationship), func_get_args(), 'parent');
    }

    /**
     * Adds a child and parents relationship to the current relationships collection.
     *
     * @param \Gedcomx\Rs\Client\PersonState                   $child
     * @param \Gedcomx\Rs\Client\PersonState                   $father
     * @param \Gedcomx\Rs\Client\PersonState                   $mother
     * @param \Gedcomx\Rs\Client\Options\StateTransitionOption $option,...
     *
     * @return \Gedcomx\Extensions\FamilySearch\Rs\Client\FamilyTree\ChildAndParentsRelationshipState
     */
    public function addChildAndParents(PersonState $child, PersonState $father = null, PersonState $mother = null, StateTransitionOption $option = null) {
        $chap = new ChildAndParentsRelationship();
        $chap->setChild(new ResourceReference($child->getSelfUri()));
        if ($father != null) {
            $chap->setFather(new ResourceReference($father->getSelfUri()));
        }
        if ($mother != null) {
            $chap->setMother(new ResourceReference($mother->getSelfUri()));
        }

        return $this->passOptionsTo('addChildAndParentsRelationship', array($chap), func_get_args());
    }

    /**
     * Adds a child and parents relationship to the current relationships collection.
     *
     * @param \Gedcomx\Extensions\FamilySearch\Platform\Tree\ChildAndParentsRelationship $chap
     * @param \Gedcomx\Rs\Client\Options\StateTransitionOption                           $option,...
     *
     * @return mixed
     */
    public function addChildAndParentsRelationship(ChildAndParentsRelationship $chap, StateTransitionOption $option = null) {
        $entity = new FamilySearchPlatform();
        $entity->setChildAndParentsRelationships(array($chap));

        $request = $this->createAuthenticatedRequest(Request::POST, $this->getSelfUri());
        return $this->stateFactory->createState(
            'ChildAndParentsRelationshipState',
            $this->client,
            $request,
            $this->passOptionsTo('invoke', array($request), func_get_args()),
            $this->accessToken
        );
    }
}