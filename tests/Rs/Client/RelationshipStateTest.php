<?php

namespace Gedcomx\Tests\Rs\Client;

use Gedcomx\Common\Attribution;
use Gedcomx\Common\ResourceReference;
use Gedcomx\Conclusion\Relationship;
use Gedcomx\Extensions\FamilySearch\Rs\Client\FamilyTree\FamilyTreeStateFactory;
use Gedcomx\Extensions\FamilySearch\Rs\Client\Rel;
use Gedcomx\Rs\Client\RelationshipState;
use Gedcomx\Rs\Client\Util\HttpStatus;
use Gedcomx\Source\SourceReference;
use Gedcomx\Tests\ApiTestCase;

class RelationshipStateTest extends ApiTestCase
{
    /**
     * @link https://familysearch.org/developers/docs/api/tree/Create_Couple_Relationship_usecase
     * @link https://familysearch.org/developers/docs/api/tree/Read_Couple_Relationship_usecase
     * @link https://familysearch.org/developers/docs/api/tree/Update_Persons_of_a_Couple_Relationship_usecase
     * @link https://familysearch.org/developers/docs/api/tree/Delete_Couple_Relationship_usecase
     */
    public function testCoupleRelationshipCRUD()
    {
        $factory = new FamilyTreeStateFactory();
        $this->collectionState($factory);

        $person1 = $this->createPerson('male')->get();
        $person2 = $this->createPerson('female')->get();

        /* CREATE */
        $relation = $this->collectionState()->addSpouseRelationship($person1, $person2);
        $this->assertAttributeEquals(HttpStatus::CREATED, "statusCode", $relation->getResponse(), $this->buildFailMessage(__METHOD__, $relation));

        /* READ */
        $relation = $relation->get();
        $this->assertAttributeEquals(HttpStatus::OK, "statusCode", $relation->getResponse(), $this->buildFailMessage(__METHOD__, $relation));

        /** @var $entity Relationship */
        $entity = $relation->getRelationship();
        $data_check = $entity->getPerson1() instanceof ResourceReference
            && $entity->getPerson2() instanceof ResourceReference;
        $this->assertTrue( $data_check );

        /* UPDATE */
        $person3 = $this->createPerson('female');
        $entity->setPerson2($person3->getResourceReference());
        $updated = $relation->updateSelf($entity);
        $this->assertAttributeEquals(HttpStatus::NO_CONTENT, "statusCode", $updated->getResponse(), $this->buildFailMessage(__METHOD__, $updated));

        /* DELETE */
        $deleted = $relation->delete();
        $this->assertAttributeEquals(HttpStatus::NO_CONTENT, "statusCode", $deleted->getResponse(), $this->buildFailMessage(__METHOD__, $deleted));

        $person1->delete();
        $person2->delete();
        $person3->delete();

    }

    /**
     * @link https://familysearch.org/developers/docs/api/tree/Create_Couple_Relationship_Source_Reference_usecase
     */
    public function testCreateCoupleRelationshipSourceReference()
    {
        $factory = new FamilyTreeStateFactory();
        $this->collectionState($factory);

        $person1 = $this->createPerson('male')->get();
        $person2 = $this->createPerson('female')->get();

        /* Create Relationship */
        /** @var $relation RelationshipState */
        $relation = $this->collectionState()->addSpouseRelationship($person1, $person2)->get();
        $this->assertAttributeEquals(HttpStatus::OK, "statusCode", $relation->getResponse(), $this->buildFailMessage(__METHOD__."(addSpouse)", $relation));

        /* Create source */
        $sourceState = $this->createSource();
        $this->assertAttributeEquals(HttpStatus::CREATED, "statusCode", $sourceState->getResponse(), $this->buildFailMessage(__METHOD__."(createSource)", $sourceState));

        $reference = new SourceReference();
        $reference->setDescriptionRef($sourceState->getSelfUri());
        $reference->setAttribution( new Attribution( array(
            "changeMessage" => $this->faker->sentence(6)
        )));

        /* CREATE the source reference on the relationship */
        $relation->addSourceReference($reference);
        $this->assertAttributeEquals(HttpStatus::CREATED, "statusCode", $sourceState->getResponse(), $this->buildFailMessage(__METHOD__."(addReference)", $sourceState));

        /* READ the source references back */
        $relation->loadSourceReferences();
        $this->assertNotEmpty($relation->getRelationship()->getSources(), "loadForRead");

        /* UPDATE */
        $newMessage = $this->faker->sentence(8);
        $reference->setAttribution( new Attribution( array(
            "changeMessage" => $newMessage
        )));
        $updated = $relation->updateSourceReference($reference);
        $this->assertAttributeEquals(HttpStatus::NO_CONTENT, "statusCode", $updated->getResponse(), $this->buildFailMessage(__METHOD__."(updateSource)", $updated));

        $relation->loadSourceReferences();
        $sources = $relation->getRelationship()->getSources();
        $changeMessage = $sources[0]->getAttribution()->getChangeMessage();
        $this->assertEquals($newMessage, $changeMessage, "Change message doesn't match");

        /* DELETE */
        $relation->deleteSourceReference($sources[0]);
        $this->assertAttributeEquals(HttpStatus::NO_CONTENT, "statusCode", $sourceState->getResponse(), $this->buildFailMessage(__METHOD__."(deleteSource)", $relation) );

        $relation->loadSourceReferences();
        $sources = $relation->getRelationship()->getSources();
        $this->assertEmpty($sources, "Sources should be empty.");

        $sourceState->delete();
        $relation->delete();
        $person1->delete();
        $person2->delete();

    }

    /**
     * @link https://familysearch.org/developers/docs/api/tree/Create_Couple_Relationship_Conclusion_usecase
     */
    public function testCreateCoupleRelationshipConclusion()
    {

    }

    /**
     * @link https://familysearch.org/developers/docs/api/tree/Create_Couple_Relationship_Note_usecase
     */
    public function testCreateCoupleRelationshipNote()
    {

    }

    /**
     * @link https://familysearch.org/developers/docs/api/tree/Read_Couple_Relationship_(Conditional)_usecase
     */
    public function testReadCoupleRelationshipConditional()
    {

    }

    /**
     * @link https://familysearch.org/developers/docs/api/tree/Read_Couple_Relationship_Source_References_usecase
     */
    public function testReadCoupleRelationshipSourceReferences()
    {

    }

    /**
     * @link https://familysearch.org/developers/docs/api/tree/Read_Couple_Relationship_Sources_usecase
     */
    public function testReadCoupleRelationshipSources()
    {

    }

    /**
     * @link https://familysearch.org/developers/docs/api/tree/Read_Couple_Relationship_Notes_usecase
     */
    public function testReadCoupleRelationshipNotes()
    {

    }

    /**
     * @link https://familysearch.org/developers/docs/api/tree/Read_Non-Existent_Couple_Relationship_usecase
     */
    public function testReadNonExistentCoupleRelationship()
    {

    }

    /**
     * @link https://familysearch.org/developers/docs/api/tree/Head_Couple_Relationship_usecase
     */
    public function testHeadCoupleRelationship()
    {
        $factory = new FamilyTreeStateFactory();
        $this->collectionState($factory);

        $person1 = $this->createPerson('male')->get();
        $person2 = $this->createPerson('female')->get();

        $relation = $this->collectionState()->addSpouseRelationship($person1, $person2);
        $this->assertAttributeEquals(HttpStatus::CREATED, "statusCode", $relation->getResponse(), $this->buildFailMessage(__METHOD__, $relation));

        $headers = $relation->head();
        $this->assertAttributeEquals(HttpStatus::OK, "statusCode", $headers->getResponse(), $this->buildFailMessage(__METHOD__, $headers));
    }

    /**
     * @link https://familysearch.org/developers/docs/api/tree/Update_Couple_Relationship_Conclusion_usecase
     */
    public function testUpdateCoupleRelationshipConclusion()
    {

    }

    /**
     * @link https://familysearch.org/developers/docs/api/tree/Update_Illegal_Couple_Relationship_usecase
     */
    public function testUpdateIllegalCoupleRelationship()
    {
        $factory = new FamilyTreeStateFactory();
        $this->collectionState($factory);

        $person1 = $this->createPerson('male')->get();
        $person2 = $this->createPerson('female')->get();

        /* CREATE */
        $relation = $this->collectionState()->addSpouseRelationship($person1, $person2);
        $this->assertAttributeEquals(HttpStatus::CREATED, "statusCode", $relation->getResponse(), $this->buildFailMessage(__METHOD__, $relation));

        /* READ */
        $relation = $relation->get();
        $this->assertAttributeEquals(HttpStatus::OK, "statusCode", $relation->getResponse(), $this->buildFailMessage(__METHOD__, $relation));

        /** @var $entity Relationship */
        $entity = $relation->getRelationship();

        /* Person1 must be male. Person2 must be female. */
        $person3 = $this->createPerson('male');
        $entity->setPerson2($person3->getResourceReference());
        $updated = $relation->updateSelf($entity);
        $this->assertAttributeEquals(HttpStatus::BAD_REQUEST, "statusCode", $updated->getResponse(), $this->buildFailMessage(__METHOD__, $updated));

        $warnings = $updated->getHeader('warning');
        $this->assertNotEmpty($warnings, "No warnings found in headers.");

        /* Clean up */
        $relation->delete();
        $person1->delete();
        $person2->delete();
        $person3->delete();

    }

    /**
     * @link https://familysearch.org/developers/docs/api/tree/Delete_Couple_Relationship_Conclusion_usecase
     */
    public function testDeleteCoupleRelationshipConclusion()
    {

    }

    /**
     * @link https://familysearch.org/developers/docs/api/tree/Restore_Couple_Relationship_usecase
     */
    public function testRestoreCoupleRelationship()
    {

    }
}