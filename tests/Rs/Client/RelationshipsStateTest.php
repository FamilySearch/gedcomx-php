<?php


namespace Gedcomx\Tests\Rs\Client;


use Gedcomx\Common\ResourceReference;
use Gedcomx\Extensions\FamilySearch\Platform\Tree\ChildAndParentsRelationship;
use Gedcomx\Extensions\FamilySearch\Rs\Client\FamilyTree\FamilyTreeStateFactory;
use Gedcomx\Rs\Client\Util\HttpStatus;
use Gedcomx\Tests\ApiTestCase;
use Gedcomx\Tests\FactBuilder;

class RelationshipsStateTest extends ApiTestCase {

    /**
     * @link https://familysearch.org/developers/docs/api/tree/Create_Child-and-Parents_Relationship_usecase
     */
    public function testCreateChildAndParentsRelationshipWithFacts(){
        $factory = new FamilyTreeStateFactory();
        $this->collectionState($factory);

        $child = $this->createPerson()->get();
        $father = $this->createPerson('male')->get();
        $mother = $this->createPerson('female')->get();

        $relation = new ChildAndParentsRelationship();
        $relation->setChild($child->getResourceReference());
        $relation->setFather($father->getResourceReference());
        $relation->setMother($mother->getResourceReference());
        $fact = FactBuilder::adoptiveParent();
        $relation->setFatherFacts(array($fact));


        $relation = $this->collectionState()->addChildAndParentsRelationship($relation);

        $this->assertAttributeEquals(HttpStatus::CREATED, "statusCode", $relation->getResponse(), $this->buildFailMessage(__METHOD__, $relation) );

        $relation = $relation->get();
        $entity = $relation->getRelationship();
        $data_check = $entity->getFather() instanceof ResourceReference
            && $entity->getMother() instanceof ResourceReference
            && $entity->getChild() instanceof ResourceReference;
        $this->assertTrue( $data_check );
    }
} 