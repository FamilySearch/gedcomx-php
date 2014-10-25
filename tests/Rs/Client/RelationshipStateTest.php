<?php


namespace Gedcomx\Tests\Rs\Client;


use Gedcomx\Extensions\FamilySearch\Rs\Client\FamilyTree\FamilyTreeStateFactory;
use Gedcomx\Rs\Client\Util\HttpStatus;
use Gedcomx\Tests\ApiTestCase;

class RelationshipStateTest extends ApiTestCase {

	/**
	 * @link https://familysearch.org/developers/docs/api/tree/Create_Couple_Relationship_Note_usecase
	 */
	public function testCreateCoupleRelationshipNote()
	{
        $factory = new FamilyTreeStateFactory();
        $this->collectionState($factory);

        $person1 = $this->createPerson();
        $person2 = $this->createPerson();

        $relation = $this->collectionState()->addSpouseRelationship($person1, $person2);

        $this->assertAttributeEquals(HttpStatus::CREATED, "statusCode", $relation->getResponse(), $this->buildFailMessage(__METHOD__, $relation));
	}

    /**
     * @link https://familysearch.org/developers/docs/api/tree/Create_Child-and-Parents_Relationship_usecase
     */
    public function testCreateChildAndParentsRelationship(){
        $factory = new FamilyTreeStateFactory();
        $this->collectionState($factory);

        $child = $this->createPerson();
        $parent = $this->createPerson();

        $relation = $this->collectionState()->addSpouseRelationship($parent, $child);

        $this->assertAttributeEquals(HttpStatus::CREATED, "statusCode", self::$personState->getResponse(), $this->buildFailMessage(__METHOD__, self::$personState) );
    }
} 