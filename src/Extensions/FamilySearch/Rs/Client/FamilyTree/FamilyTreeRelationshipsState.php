<?php

namespace Gedcomx\Extensions\FamilySearch\Rs\Client\FamilyTree;

use Gedcomx\Common\ResourceReference;
use Gedcomx\Extensions\FamilySearch\FamilySearchPlatform;
use Gedcomx\Extensions\FamilySearch\Platform\Tree\ChildAndParentsRelationship;
use Gedcomx\Rs\Client\RelationshipsState;
use Gedcomx\Types\RelationshipType;
use Guzzle\Http\Message\Request;

class FamilyTreeRelationshipsState extends RelationshipsState {

    /**
     * @return ChildAndParentsRelationship[]|null
     */
    public function getChildAndParentsRelationships() {
        if ($this->getEntity() != null) {
            return $this->getEntity()->getChildAndParentsRelationships();
        }

        return null;
    }

    /**
     * @param Relationship          $relationship
     * @param StateTransitionOption $option,...
     *
     * @return RelationshipState
     * @throws GedcomxApplicationException
     */
    public function addRelationship(Relationship $relationship, StateTransitionOption $option = null)
    {
        if ($relationship->getKnownType() == RelationshipType::ParentChild) {
            throw new GedcomxApplicationException("FamilySearch Family Tree doesn't support adding parent-child relationships. You must instead add a child-and-parents relationship.");
        }

        return $this->passOptionsTo('addRelationship', array($relationship), func_get_args(), 'parent');
    }

    /**
     * @param PersonState           $child
     * @param PersonState           $father
     * @param PersonState           $mother
     * @param StateTransitionOption $option,...
     *
     * @return ChildAndParentsRelationshipState
     */
    public function addChildAndParents(PersonState $child, PersonState $father = null, PersonState $mother = null, StateTransitionOption $option = null) {
        $chap = new ChildAndParentsRelationship();
        $chap->setChild(new ResourceReference($child->getSelfUri()));
        if ($father != null) {
            $chap->setFather(new ResourceReference($father->getSelfUri()));
        }
        if (mother != null) {
            chap.setMother(new ResourceReference(new URI(mother.getSelfUri().toString())));
        }

        return $this->passOptionsTo('addChildAndParentsRelationship', array($chap), func_get_args());
    }

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