<?php 

namespace Gedcomx\Tests\Functional;

use Gedcomx\Common\Note;
use Gedcomx\Extensions\FamilySearch\Rs\Client\FamilyTree\FamilyTreeCollectionState;
use Gedcomx\Extensions\FamilySearch\Rs\Client\FamilyTree\FamilyTreePersonState;
use Gedcomx\Extensions\FamilySearch\Rs\Client\FamilyTree\FamilyTreeStateFactory;
use Gedcomx\Rs\Client\Rel;
use Gedcomx\Rs\Client\RelationshipState;
use Gedcomx\Rs\Client\StateFactory;
use Gedcomx\Rs\Client\Util\HttpStatus;
use Gedcomx\Tests\ApiTestCase;
use Gedcomx\Tests\NoteBuilder;

class NotesTests extends ApiTestCase
{
    /**
     * @link https://familysearch.org/developers/docs/api/tree/Create_Child-and-Parents_Relationship_Note_usecase
     */
    public function testCreateChildAndParentsRelationshipNote()
    {
        $factory = new FamilyTreeStateFactory();
        /** @var FamilyTreeCollectionState $collection */
        $this->collectionState($factory);

        $relation = $this->createRelationship();
        $this->assertAttributeEquals(HttpStatus::CREATED, "statusCode", $relation->getResponse(), $this->buildFailMessage(__METHOD__, $relation));

        $note = NoteBuilder::createNote();
        $noteState = $relation->addNote($note);
        $this->assertAttributeEquals(HttpStatus::CREATED, "statusCode", $noteState->getResponse(), $this->buildFailMessage(__METHOD__, $noteState));
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
        $this->queueForDelete($relation);

        $this->assertAttributeEquals(HttpStatus::OK, "statusCode", $relation->getResponse(), $this->buildFailMessage(__METHOD__."(addSpouse)", $relation));

        $note = NoteBuilder::createNote();
        $updated = $relation->addNote($note);
        $this->assertAttributeEquals(HttpStatus::CREATED, "statusCode", $updated->getResponse(), $this->buildFailMessage(__METHOD__."(addSpouse)", $updated));
    }

    /**
     * @link https://familysearch.org/developers/docs/api/tree/Create_Note_usecase
     */
    public function testCreateNote(){
        $factory = new StateFactory();
        $this->collectionState($factory);

        $personState = $this->createPerson();

        $note = NoteBuilder::createNote();
        $noteState = $personState->addNote( $note );

        $this->assertAttributeEquals(HttpStatus::CREATED, "statusCode", $noteState->getResponse() );
    }

    /**
     * @link https://familysearch.org/developers/docs/api/tree/Read_Child-and-Parents_Relationship_Note_usecase
     */
    public function testReadChildAndParentsRelationshipNote()
    {
        $factory = new FamilyTreeStateFactory();
        $this->collectionState($factory);

        /** @var ChildAndParentsRelationshipState $relation */
        $relation = $this->createRelationship();
        $note = NoteBuilder::createNote();
        $relation->addNote($note);

        $relation = $relation->get()->loadNotes();
        $notes = $relation->getRelationship()->getNotes();
        $noted = $relation->readNote($notes[0]);
        $this->assertAttributeEquals(HttpStatus::OK, "statusCode", $noted->getResponse(), $this->buildFailMessage(__METHOD__."(addSpouse)", $noted));
    }

    /**
     * @link https://familysearch.org/developers/docs/api/tree/Read_Child-and-Parents_Relationship_Notes_usecase
     */
    public function testReadChildAndParentsRelationshipNotes()
    {
        $factory = new FamilyTreeStateFactory();
        /** @var FamilyTreeCollectionState $collection */
        $this->collectionState($factory);

        /** @var ChildAndParentsRelationshipState $relation */
        $relation = $this->createRelationship();

        $note = NoteBuilder::createNote();
        $noteState = $relation->addNote($note);
        $this->assertAttributeEquals(HttpStatus::CREATED, "statusCode", $noteState->getResponse(), $this->buildFailMessage(__METHOD__, $noteState));

        $relation = $relation->get();
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
        $this->queueForDelete($relation);

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
        $this->queueForDelete($relation);

        $this->assertAttributeEquals(HttpStatus::OK, "statusCode", $relation->getResponse(), $this->buildFailMessage(__METHOD__."(addSpouse)", $relation));

        $note = NoteBuilder::createNote();
        $updated = $relation->addNote($note);
        $this->assertAttributeEquals(HttpStatus::CREATED, "statusCode", $updated->getResponse(), $this->buildFailMessage(__METHOD__."(addSpouse)", $updated));

        $relation->loadNotes();
        $this->assertNotEmpty($relation->getRelationship()->getNotes());
    }

    /**
     * @link https://familysearch.org/developers/docs/api/tree/Read_Note_usecase
     */
    public function testReadNote()
    {
        $factory = new StateFactory();
        $this->collectionState($factory);
        //  Set up the data we need
        $testSubject = $this->createPerson()->get();
        $note = NoteBuilder::createNote();
        $testSubject->addNote( $note );

        //  Now test it
        $testSubject->loadNotes();
        $person = $testSubject->getPerson();
        $notes = $person->getNotes();
        $newState = $testSubject->readNote($notes[0]);

        $this->assertAttributeEquals(HttpStatus::OK, "statusCode", $newState->getResponse() );
    }

    /**
     * @link https://familysearch.org/developers/docs/api/tree/Read_Notes_usecase
     */
    public function testReadNotes(){
        $factory = new StateFactory();
        $this->collectionState($factory);
        //  Set up the data we need
        $testSubject = $this->createPerson()->get();
        $note = NoteBuilder::createNote();
        $testSubject->addNote( $note );

        //  Now test it
        $testSubject->loadNotes();

        $this->assertAttributeEquals(HttpStatus::OK, "statusCode", $testSubject->getResponse() );
    }

    /**
     * @link https://familysearch.org/developers/docs/api/tree/Update_Child-and-Parents_Relationship_Note_usecase
     */
    public function testUpdateChildAndParentsRelationshipNote()
    {
        $factory = new FamilyTreeStateFactory();
        $this->collectionState($factory);

        /** @var ChildAndParentsRelationshipState $relation */
        $relation = $this->createRelationship();
        $note = NoteBuilder::createNote();
        $relation->addNote($note);

        $relation = $relation->get()->loadNotes();
        $notes = $relation->getRelationship()->getNotes();
        $notes[0]->setText($this->faker->sentence(12));
        $noted = $relation->updateNote($notes[0]);
        $this->assertAttributeEquals(HttpStatus::NO_CONTENT, "statusCode", $noted->getResponse(), $this->buildFailMessage(__METHOD__."(addSpouse)", $noted));
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
        $this->queueForDelete($relation);

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
     * @link https://familysearch.org/developers/docs/api/tree/Update_Note_usecase
     */
    public function testUpdateNote()
    {
        $factory = new FamilyTreeStateFactory();
        $this->collectionState($factory);

        /** @var FamilyTreePersonState $person */
        $person = $this->createPerson()->get();
        $person->addNote(NoteBuilder::createNote());
        $notes = $person->loadNotes();
        $note = $notes->getNote();
        $state = $person->updateNote($note);
        $person->delete();

        $this->assertNotNull($state->ifSuccessful());
        $this->assertEquals(HttpStatus::NO_CONTENT, $state->getResponse()->getStatusCode());
    }

    /**
     * @link https://familysearch.org/developers/docs/api/tree/Delete_Child-and-Parents_Relationship_Note_usecase
     */
    public function testDeleteChildAndParentRelationshipNote()
    {
        $factory = new FamilyTreeStateFactory();
        $this->collectionState($factory);

        /** @var ChildAndParentsRelationshipState $relation */
        $relation = $this->createRelationship();
        $note = NoteBuilder::createNote();
        $relation->addNote($note);

        $relation = $relation->get()->loadNotes();
        $notes = $relation->getRelationship()->getNotes();
        $noted = $relation->deleteNote($notes[0]);
        $this->assertAttributeEquals(HttpStatus::NO_CONTENT, "statusCode", $noted->getResponse(), $this->buildFailMessage(__METHOD__, $noted));
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
        $this->queueForDelete($relation);

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
     * @link https://familysearch.org/developers/docs/api/tree/Delete_Note_usecase
     */
    public function testDeleteNote()
    {
        $factory = new StateFactory();
        $this->collectionState($factory);

        $personState = $this->createPerson();

        $note = NoteBuilder::createNote();
        $noteState = $personState->addNote( $note );

        $note = new Note();
        $note->addLink($noteState->getLink(Rel::SELF));

        $delState = $personState->deleteNote($note);

        $this->assertAttributeEquals(HttpStatus::NO_CONTENT, "statusCode", $delState->getResponse() );
    }
}