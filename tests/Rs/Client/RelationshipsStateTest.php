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
	 * @link https://familysearch.org/developers/docs/api/tree/Create_Couple_Relationship_Note_usecase
	 */
	public function testCreateCoupleRelationship()
	{
        $factory = new FamilyTreeStateFactory();
        $this->collectionState($factory);

        $person1 = $this->createPerson('male')->get();
        $person2 = $this->createPerson('female')->get();

        $relation = $this->collectionState()->addSpouseRelationship($person1, $person2);

        $this->assertAttributeEquals(HttpStatus::CREATED, "statusCode", $relation->getResponse(), $this->buildFailMessage(__METHOD__, $relation));

        $relation = $relation->get();
        $entity = $relation->getRelationship();

        $data_check = $entity->getPerson1() instanceof ResourceReference
                          && $entity->getPerson2() instanceof ResourceReference;
        $this->assertTrue( $data_check );

	}

    /**
     * @link https://familysearch.org/developers/docs/api/tree/Create_Child-and-Parents_Relationship_usecase
     */
    public function testCreateChildAndParentsRelationship(){
        $factory = new FamilyTreeStateFactory();
        $this->collectionState($factory);

        $child = $this->createPerson();
        $father = $this->createPerson('male')->get();
        $mother = $this->createPerson('female')->get();

        $relation = $this->collectionState()->addChildAndParents($child, $father, $mother);

        $this->assertAttributeEquals(HttpStatus::CREATED, "statusCode", $relation->getResponse(), $this->buildFailMessage(__METHOD__, $relation) );

        $relation = $relation->get();
        $entity = $relation->getRelationship();
        $data_check = $entity->getFather() instanceof ResourceReference
                          && $entity->getMother() instanceof ResourceReference
                          && $entity->getChild() instanceof ResourceReference;
        $this->assertTrue( $data_check );
    }

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