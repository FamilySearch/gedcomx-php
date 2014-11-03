<?php

namespace Gedcomx\Tests\Rs\Client;

use Gedcomx\Common\Attribution;
use Gedcomx\Common\Note;
use Gedcomx\Common\ResourceReference;
use Gedcomx\Conclusion\DateInfo;
use Gedcomx\Conclusion\Fact;
use Gedcomx\Conclusion\Relationship;
use Gedcomx\Extensions\FamilySearch\Rs\Client\FamilyTree\FamilyTreeStateFactory;
use Gedcomx\Rs\Client\Options\HeaderParameter;
use Gedcomx\Rs\Client\RelationshipState;
use Gedcomx\Rs\Client\Util\HttpStatus;
use Gedcomx\Source\SourceReference;
use Gedcomx\Tests\ApiTestCase;
use Gedcomx\Tests\FactBuilder;
use Gedcomx\Tests\NoteBuilder;
use Gedcomx\Types\FactType;

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
     * @link https://familysearch.org/developers/docs/api/tree/Read_Couple_Relationship_(Conditional)_usecase
     */
    public function testReadCoupleRelationshipConditional()
    {
        $factory = new FamilyTreeStateFactory();
        $this->collectionState($factory);

        $person1 = $this->createPerson('male')->get();
        $person2 = $this->createPerson('female')->get();

        //$relation = $this->collectionState()->addSpouseRelationship($person1, $person2);
        $relation = $person1->addSpouse($person2);
        $relation = $relation->get();
        $this->assertAttributeEquals(HttpStatus::OK, "statusCode", $relation->getResponse(), $this->buildFailMessage(__METHOD__, $relation));

        $etagHeader = $relation->getEtag()->toArray();
        $noneMatch = new HeaderParameter(true, HeaderParameter::IF_NONE_MATCH, $etagHeader[0]);
        $eTag = new HeaderParameter(true, HeaderParameter::ETAG, $etagHeader[0]);

        $secondState = $relation->get($noneMatch, $eTag);

        $this->assertAttributeEquals(HttpStatus::NOT_MODIFIED, "statusCode", $secondState->getResponse() );
    }

    /**
     * @link https://familysearch.org/developers/docs/api/tree/Read_Non-Existent_Couple_Relationship_usecase
     */
    public function testReadNonExistentCoupleRelationship()
    {
        $factory = new FamilyTreeStateFactory();
        $this->collectionState($factory);

        $relationship = new Relationship(array(
            "links" => array(
                array(
                    "rel" => 'relationship',
                    'href' => "https://sandbox.familysearch.org/platform/tree/couple-relationships/NOTFOUND"
                )
            )
        ));

        $person1 = $this->createPerson();
        $relation = $person1->readRelationship($relationship);
        $this->assertAttributeEquals(HttpStatus::NOT_FOUND, "statusCode", $relation->getResponse());
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
     * @link https://familysearch.org/developers/docs/api/tree/Create_Couple_Relationship_Source_Reference_usecase
     * @link https://familysearch.org/developers/docs/api/tree/Read_Couple_Relationship_Sources_usecase
     * @link https://familysearch.org/developers/docs/api/tree/Read_Couple_Relationship_Source_References_usecase
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
        $relation = $relation->addSourceReference($reference);
        $this->assertAttributeEquals(HttpStatus::CREATED, "statusCode", $relation->getResponse(), $this->buildFailMessage(__METHOD__."(addReference)", $relation));

        $sourceState->delete();
        $relation->delete();
        $person1->delete();
        $person2->delete();
    }

    /**
     * @link https://familysearch.org/developers/docs/api/tree/Read_Couple_Relationship_Sources_usecase
     * @link https://familysearch.org/developers/docs/api/tree/Read_Couple_Relationship_Source_References_usecase
     */
    public function testReadCoupleRelationshipSourceReferences()
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
        $updated = $relation->addSourceReference($reference);
        $this->assertAttributeEquals(HttpStatus::CREATED, "statusCode", $updated->getResponse(), $this->buildFailMessage(__METHOD__."(addReference)", $updated));

        /* READ the source references back */
        $relation->loadSourceReferences();
        $this->assertNotEmpty($relation->getRelationship()->getSources(), "loadForRead");

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
        $factory = new FamilyTreeStateFactory();
        $this->collectionState($factory);

        $person1 = $this->createPerson('male')->get();
        $person2 = $this->createPerson('female')->get();

        /* Create Relationship */
        /** @var $relation RelationshipState */
        $relation = $this->collectionState()->addSpouseRelationship($person1, $person2)->get();
        $this->assertAttributeEquals(HttpStatus::OK, "statusCode", $relation->getResponse(), $this->buildFailMessage(__METHOD__."(addSpouse)", $relation));

        /* Create Marriage Fact */
        /** @var Fact $birth */
        $birth = $person1->getPerson()->getFactsOfType(FactType::BIRTH);
        $marriage = FactBuilder::marriage($birth->getDate()->getDateTime());
        $relation = $relation->addFact($marriage);
        $this->assertAttributeEquals(HttpStatus::NO_CONTENT, "statusCode", $relation->getResponse(), $this->buildFailMessage(__METHOD__."(createSource)", $relation));

        $relation->delete();
        $person2->delete();
        $person1->delete();
    }

    /**
     * @link https://familysearch.org/developers/docs/api/tree/Delete_Couple_Relationship_Conclusion_usecase
     */
    public function testDeleteCoupleRelationshipConclusion()
    {
        $factory = new FamilyTreeStateFactory();
        $this->collectionState($factory);

        $person1 = $this->createPerson('male')->get();
        $person2 = $this->createPerson('female')->get();

        /* Create Relationship */
        /** @var $relation RelationshipState */
        $relation = $this->collectionState()->addSpouseRelationship($person1, $person2)->get();
        $this->assertAttributeEquals(HttpStatus::OK, "statusCode", $relation->getResponse(), $this->buildFailMessage(__METHOD__."(addSpouse)", $relation));

        /* Create Marriage Fact */
        /** @var Fact $birth */
        $birth = $person1->getPerson()->getFactsofType(FactType::BIRTH);
        $marriage = FactBuilder::marriage($birth->getDate()->getDateTime());
        $relation = $relation->addFact($marriage);
        $this->assertAttributeEquals(HttpStatus::NO_CONTENT, "statusCode", $relation->getResponse(), $this->buildFailMessage(__METHOD__."(createSource)", $relation));

        $relation = $relation->get()->loadConclusions();

        /** @var Fact $birth */
        $marriage = $relation->getRelationship()->getFactsOfType(FactType::MARRIAGE);
        $deleted = $relation->deleteFact($marriage);
        $this->assertAttributeEquals(HttpStatus::NO_CONTENT, "statusCode", $deleted->getResponse(), $this->buildFailMessage(__METHOD__."(createSource)", $deleted));

        $relation->delete();
        $person2->delete();
        $person1->delete();
    }

    /**
     * @link https://familysearch.org/developers/docs/api/tree/Update_Couple_Relationship_Conclusion_usecase
     */
    public function testUpdateCoupleRelationshipConclusion()
    {
        $factory = new FamilyTreeStateFactory();
        $this->collectionState($factory);

        $person1 = $this->createPerson('male')->get();
        $person2 = $this->createPerson('female')->get();

        /* Create Relationship */
        /** @var $relation RelationshipState */
        $relation = $this->collectionState()->addSpouseRelationship($person1, $person2)->get();
        $this->assertAttributeEquals(HttpStatus::OK, "statusCode", $relation->getResponse(), $this->buildFailMessage(__METHOD__."(addSpouse)", $relation));

        /* Create Marriage Fact */
        /** @var Fact $birth */
        $birth = $person1->getPerson()->getFactsOfType(FactType::BIRTH);
        $marriage = FactBuilder::marriage($birth->getDate()->getDateTime());
        $relation = $relation->addFact($marriage);
        $this->assertAttributeEquals(HttpStatus::NO_CONTENT, "statusCode", $relation->getResponse(), $this->buildFailMessage(__METHOD__."(createSource)", $relation));

        /* Alter Marriage Fact */
        $marriage->setAttribution(new Attribution(array(
            "changeMessage" => $this->faker->sentence(6)
        )));
        $currentDate = $marriage->getDate()->getDateTime();
        $newDate = new \DateTime($currentDate->format('Y-m-d')." +5 days");
        $marriage->setDate(new DateInfo(array(
            "original" => $newDate->format('F m, Y')
        )));
        $relation = $relation->addFact($marriage);
        $this->assertAttributeEquals(HttpStatus::NO_CONTENT, "statusCode", $relation->getResponse(), $this->buildFailMessage(__METHOD__."(createSource)", $relation));

        $relation->delete();
        $person2->delete();
        $person1->delete();

    }

    /**
     * @link https://familysearch.org/developers/docs/api/tree/Create_Couple_Relationship_Note_usecase
     */
    public function testCreateCoupleRelationshipNote()
    {
        $factory = new FamilyTreeStateFactory();
        $this->collectionState($factory);

        $person1 = $this->createPerson('male')->get();
        $person2 = $this->createPerson('female')->get();

        /* Create Relationship */
        /** @var $relation RelationshipState */
        $relation = $this->collectionState()->addSpouseRelationship($person1, $person2)->get();
        $this->assertAttributeEquals(HttpStatus::OK, "statusCode", $relation->getResponse(), $this->buildFailMessage(__METHOD__."(addSpouse)", $relation));

        $note = NoteBuilder::createNote();
        $updated = $relation->addNote($note);
        $this->assertAttributeEquals(HttpStatus::CREATED, "statusCode", $updated->getResponse(), $this->buildFailMessage(__METHOD__."(addSpouse)", $updated));

        $relation->delete();
        $person2->delete();
        $person1->delete();
    }

    /**
     * @link https://familysearch.org/developers/docs/api/tree/Read_Couple_Relationship_Notes_usecase
     */
    public function testReadCoupleRelationshipNotes()
    {
        $factory = new FamilyTreeStateFactory();
        $this->collectionState($factory);

        $person1 = $this->createPerson('male')->get();
        $person2 = $this->createPerson('female')->get();

        /* Create Relationship */
        /** @var $relation RelationshipState */
        $relation = $this->collectionState()->addSpouseRelationship($person1, $person2)->get();
        $this->assertAttributeEquals(HttpStatus::OK, "statusCode", $relation->getResponse(), $this->buildFailMessage(__METHOD__."(addSpouse)", $relation));

        $note = NoteBuilder::createNote();
        $updated = $relation->addNote($note);
        $this->assertAttributeEquals(HttpStatus::CREATED, "statusCode", $updated->getResponse(), $this->buildFailMessage(__METHOD__."(addSpouse)", $updated));

        $relation->loadNotes();
        $this->assertNotEmpty($relation->getRelationship()->getNotes());
    }

    /**
     * @link https://familysearch.org/developers/docs/api/tree/Couple_Relationship_Note_resource
     */
    public function testReadCoupleRelationshipNote()
    {
        $factory = new FamilyTreeStateFactory();
        $this->collectionState($factory);

        $person1 = $this->createPerson('male')->get();
        $person2 = $this->createPerson('female')->get();

        /* Create Relationship */
        /** @var $relation RelationshipState */
        $relation = $this->collectionState()->addSpouseRelationship($person1, $person2)->get();
        $this->assertAttributeEquals(HttpStatus::OK, "statusCode", $relation->getResponse(), $this->buildFailMessage(__METHOD__."(addSpouse)", $relation));

        $note = NoteBuilder::createNote();
        $updated = $relation->addNote($note);
        $this->assertAttributeEquals(HttpStatus::CREATED, "statusCode", $updated->getResponse(), $this->buildFailMessage(__METHOD__."(addSpouse)", $updated));

        $relation->loadNotes();
        $notes = $relation->getRelationship()->getNotes();
        $noted = $relation->readNote($notes[0]);
        $this->assertAttributeEquals(HttpStatus::OK, "statusCode", $noted->getResponse(), $this->buildFailMessage(__METHOD__."(addSpouse)", $noted));
    }

    /**
     * @link https://familysearch.org/developers/docs/api/tree/Update_Couple_Relationship_Note_usecase
     */
    public function testUpdateCoupleRelationshipNote()
    {
        $factory = new FamilyTreeStateFactory();
        $this->collectionState($factory);

        $person1 = $this->createPerson('male')->get();
        $person2 = $this->createPerson('female')->get();

        /* Create Relationship */
        /** @var $relation RelationshipState */
        $relation = $this->collectionState()->addSpouseRelationship($person1, $person2)->get();
        $this->assertAttributeEquals(HttpStatus::OK, "statusCode", $relation->getResponse(), $this->buildFailMessage(__METHOD__."(addSpouse)", $relation));

        $note = NoteBuilder::createNote();
        $noted = $relation->addNote($note);
        $this->assertAttributeEquals(HttpStatus::CREATED, "statusCode", $noted->getResponse(), $this->buildFailMessage(__METHOD__."(addSpouse)", $noted));

        $relation->loadNotes();
        $notes = $relation->getRelationship()->getNotes();
        $notes[0]->setText($this->faker->sentence(12));
        $noted = $relation->updateNote($notes[0]);
        $this->assertAttributeEquals(HttpStatus::NO_CONTENT, "statusCode", $noted->getResponse(), $this->buildFailMessage(__METHOD__."(addSpouse)", $noted));
    }

    /**
     * @link https://familysearch.org/developers/docs/api/tree/Update_Couple_Relationship_Note_usecase
     */
    public function testDeleteCoupleRelationshipNote()
    {
        $factory = new FamilyTreeStateFactory();
        $this->collectionState($factory);

        $person1 = $this->createPerson('male')->get();
        $person2 = $this->createPerson('female')->get();

        /* Create Relationship */
        /** @var $relation RelationshipState */
        $relation = $this->collectionState()->addSpouseRelationship($person1, $person2)->get();
        $this->assertAttributeEquals(HttpStatus::OK, "statusCode", $relation->getResponse(), $this->buildFailMessage(__METHOD__."(addSpouse)", $relation));

        $note = NoteBuilder::createNote();
        $noted = $relation->addNote($note);
        $this->assertAttributeEquals(HttpStatus::CREATED, "statusCode", $noted->getResponse(), $this->buildFailMessage(__METHOD__."(addSpouse)", $noted));

        $relation->loadNotes();
        $notes = $relation->getRelationship()->getNotes();
        $noted = $relation->deleteNote($notes[0]);
        $this->assertAttributeEquals(HttpStatus::NO_CONTENT, "statusCode", $noted->getResponse(), $this->buildFailMessage(__METHOD__."(addSpouse)", $noted));
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
     * @link https://familysearch.org/developers/docs/api/tree/Delete_Couple_Relationship_usecase
     * @link https://familysearch.org/developers/docs/api/tree/Restore_Couple_Relationship_usecase
     */
    public function testDeleteAndRestoreCoupleRelationship()
    {
        $factory = new FamilyTreeStateFactory();
        $this->collectionState($factory);

        $person1 = $this->createPerson('male')->get();
        $person2 = $this->createPerson('female')->get();

        /* CREATE */
        $relation = $this->collectionState()->addSpouseRelationship($person1, $person2);
        $this->assertAttributeEquals(HttpStatus::CREATED, "statusCode", $relation->getResponse(), $this->buildFailMessage(__METHOD__, $relation));
        $relation = $relation->get();

        $relationship = new Relationship(array(
            "links" => array(
                array(
                    "rel" => 'relationship',
                    'href' => "https://sandbox.familysearch.org/platform/tree/couple-relationships/" . $relation->getRelationship()->getId()
                )
            )
        ));

        /* DELETE */
        $deleted = $relation->delete();
        $this->assertAttributeEquals(HttpStatus::NO_CONTENT, "statusCode", $deleted->getResponse(), "Check deleted success. Returned {$deleted->getResponse()->getStatusCode()}");

        $missing = $person1->readRelationship($relationship);
        $this->assertAttributeEquals(HttpStatus::GONE, "statusCode", $missing->getResponse(), "Read deleted person failed. Returned {$missing->getResponse()->getStatusCode()}");

        /* RESTORE */
        $restored = $missing->restore();
        $this->assertAttributeEquals(HttpStatus::NO_CONTENT, "statusCode", $restored->getResponse(), "Restore person failed. Returned {$restored->getResponse()->getStatusCode()}");

        $relation->delete();
        $person1->delete();
        $person2->delete();
    }

    /**
     * @link https://familysearch.org/developers/docs/api/tree/Create_Child-and-Parents_Relationship_Note_usecase
     */
    public function testCreateChildAndParentsRelationshipNote()
    {
        $factory = new FamilyTreeStateFactory();
        $this->collectionState($factory);

        $child = $this->createPerson();
        $father = $this->createPerson('male')->get();
        $mother = $this->createPerson('female')->get();

        $relation = $this->collectionState()->addChildAndParents($child, $father, $mother);
        $note = NoteBuilder::createNote();
        $noted  = $relation->addNote($note);
        $this->assertAttributeEquals(HttpStatus::CREATED, "statusCode", $noted->getResponse(), "Restore person failed. Returned {$noted->getResponse()->getStatusCode()}");

        $relation->delete();
        $child->delete();
        $mother->delete();
        $father->delete();
    }

    /**
     * @link https://familysearch.org/developers/docs/api/tree/Read_Child-and-Parents_Relationship_Notes_usecase
     */
    public function testReadChildAndParentsRelationshipNotes()
    {
        $factory = new FamilyTreeStateFactory();
        $this->collectionState($factory);

        $child = $this->createPerson();
        $father = $this->createPerson('male')->get();
        $mother = $this->createPerson('female')->get();

        $relation = $this->collectionState()->addChildAndParents($child, $father, $mother);
        $note = NoteBuilder::createNote();
        $relation->addNote($note);
        $note = NoteBuilder::createNote();
        $relation->addNote($note);

        $relation = $relation->get()->loadNotes();
        $notes = $relation->getRelationship()->getNotes();
        $this->assertEquals(2, count($notes));

        $relation->delete();
        $child->delete();
        $mother->delete();
        $father->delete();
    }

    /**
     * @link https://familysearch.org/developers/docs/api/tree/Read_Child-and-Parents_Relationship_Note_usecase
     */
    public function testReadChildAndParentsRelationshipNote()
    {
        $factory = new FamilyTreeStateFactory();
        $this->collectionState($factory);

        $child = $this->createPerson();
        $father = $this->createPerson('male')->get();
        $mother = $this->createPerson('female')->get();

        $relation = $this->collectionState()->addChildAndParents($child, $father, $mother);
        $note = NoteBuilder::createNote();
        $relation->addNote($note);

        $relation = $relation->get()->loadNotes();
        $notes = $relation->getRelationship()->getNotes();
        $noted = $relation->readNote($notes[0]);
        $this->assertAttributeEquals(HttpStatus::OK, "statusCode", $noted->getResponse(), $this->buildFailMessage(__METHOD__."(addSpouse)", $noted));

        $relation->delete();
        $child->delete();
        $mother->delete();
        $father->delete();
    }

    /**
     * @link https://familysearch.org/developers/docs/api/tree/Update_Child-and-Parents_Relationship_Note_usecase
     */
    public function testUpdateChildAndParentsRelationshipNote()
    {
        $factory = new FamilyTreeStateFactory();
        $this->collectionState($factory);

        $child = $this->createPerson();
        $father = $this->createPerson('male')->get();
        $mother = $this->createPerson('female')->get();

        $relation = $this->collectionState()->addChildAndParents($child, $father, $mother);
        $note = NoteBuilder::createNote();
        $relation->addNote($note);

        $relation = $relation->get()->loadNotes();
        $notes = $relation->getRelationship()->getNotes();
        $notes[0]->setText($this->faker->sentence(12));
        $noted = $relation->updateNote($notes[0]);
        $this->assertAttributeEquals(HttpStatus::NO_CONTENT, "statusCode", $noted->getResponse(), $this->buildFailMessage(__METHOD__."(addSpouse)", $noted));

        $relation->delete();
        $child->delete();
        $mother->delete();
        $father->delete();
    }

    /**
     * @link https://familysearch.org/developers/docs/api/tree/Delete_Child-and-Parents_Relationship_Note_usecase
     */
    public function testDeleteChildAndParentRelationshipNote()
    {
        $factory = new FamilyTreeStateFactory();
        $this->collectionState($factory);

        $child = $this->createPerson();
        $father = $this->createPerson('male')->get();
        $mother = $this->createPerson('female')->get();

        $relation = $this->collectionState()->addChildAndParents($child, $father, $mother);
        $note = NoteBuilder::createNote();
        $relation->addNote($note);

        $relation = $relation->get()->loadNotes();
        $notes = $relation->getRelationship()->getNotes();
        $noted = $relation->deleteNote($notes[0]);
        $this->assertAttributeEquals(HttpStatus::NO_CONTENT, "statusCode", $noted->getResponse(), $this->buildFailMessage(__METHOD__, $noted));

        $relation->delete();
        $child->delete();
        $mother->delete();
        $father->delete();
    }
}