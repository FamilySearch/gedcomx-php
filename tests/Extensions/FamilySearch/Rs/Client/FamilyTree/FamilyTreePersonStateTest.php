<?php

namespace Gedcomx\tests\Extensions\FamilySearch\Rs\Client\FamilyTree;

use Faker\Provider\da_DK\Person;
use Gedcomx\Extensions\FamilySearch\Rs\Client\FamilyTree\FamilyTreeStateFactory;
use Gedcomx\Extensions\FamilySearch\Rs\Client\Rel;
use Gedcomx\Rs\Client\Util\HttpStatus;
use Gedcomx\Tests\ApiTestCase;
use Gedcomx\Tests\PersonBuilder;

class FamilyTreePersonStateTest extends ApiTestCase
{
    /**
     * @link https://familysearch.org/developers/docs/api/tree/Read_Person_Merge_Analysis_usecase
     */
    public function testReadMergeAnalysis()
    {
        $factory = new FamilyTreeStateFactory();
        $collection = $this->collectionState($factory);

        $person = PersonBuilder::buildPerson('male');
        $stateOne = $collection->addPerson($person)->get();
        $stateTwo = $collection->addPerson($person)->get();

        $analysis = $stateOne->readMergeAnalysis($stateTwo);
        $this->assertEquals(
            HttpStatus::OK,
            $analysis->getResponse()->getStatusCode(),
            $this->buildFailMessage(__METHOD__,$analysis)
        );
        $this->assertNotEmpty($analysis->getAnalysis());

        $stateTwo->delete();
        $stateOne->delete();
    }

    /**
     * @link https://familysearch.org/developers/docs/api/tree/Read_Person_Merge_Constraint_(Can_Merge_Any_Order)_usecase
     */
    public function testReadPersonMergeConstraintAnyOrder()
    {
        $factory = new FamilyTreeStateFactory();
        $collection = $this->collectionState($factory);

        $person = PersonBuilder::buildPerson('male');
        $person1 = $collection->addPerson($person)->get();
        $person2 = $collection->addPerson($person)->get();

        $state = $person1->readMergeOptions($person2);
        $this->assertEquals(
            HttpStatus::OK,
            $state->getResponse()->getStatusCode(),
            $this->buildFailMessage(__METHOD__, $state)
        );
        $this->assertArrayHasKey(Rel::MERGE_MIRROR, $state->getLinks(), $this->buildFailMessage(__METHOD__, $state));
    }

    /**
     * @link https://familysearch.org/developers/docs/api/tree/Read_Person_Merge_Constraint_(Can_Merge_Other_Order_Only)_usecase
     */
    public function testReadPersonMergeConstraintOtherOrder()
    {
        $factory = new FamilyTreeStateFactory();
        $collection = $this->collectionState($factory);

        $male = PersonBuilder::buildPerson('male');
        $female = PersonBuilder::buildPerson('female');
        $person1 = $collection->addPerson($male)->get();
        $person2 = $collection->addPerson($female)->get();

        $state = $person1->readMergeOptions($person2);
        $this->assertEquals(
            HttpStatus::OK,
            $state->getResponse()->getStatusCode(),
            $this->buildFailMessage(__METHOD__, $state)
        );
        $this->assertFalse($state->isAllowed(), $this->buildFailMessage(__METHOD__, $state));

        $person1->delete();
        $person2->delete();
    }
}