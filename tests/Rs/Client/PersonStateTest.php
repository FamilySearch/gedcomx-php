<?php

namespace Gedcomx\Tests\Rs\Client;

use Gedcomx\Common\Attribution;
use Gedcomx\Common\Note;
use Gedcomx\Conclusion\Gender;
use Gedcomx\Conclusion\Person;
use Gedcomx\Conclusion\Relationship;
use Gedcomx\Extensions\FamilySearch\Platform\Tree\ChildAndParentsRelationship;
use Gedcomx\Extensions\FamilySearch\Platform\Tree\DiscussionReference;
use Gedcomx\Extensions\FamilySearch\Rs\Client\FamilyTree\FamilyTreeStateFactory;
use Gedcomx\Rs\Client\Options\HeaderParameter;
use Gedcomx\Rs\Client\Options\Preconditions;
use Gedcomx\Rs\Client\Options\QueryParameter;
use Gedcomx\Rs\Client\Rel;
use Gedcomx\Rs\Client\StateFactory;
use Gedcomx\Source\SourceReference;
use Gedcomx\Tests\ApiTestCase;
use Gedcomx\Tests\DiscussionBuilder;
use Gedcomx\Tests\FactBuilder;
use Gedcomx\Tests\NoteBuilder;
use Gedcomx\Tests\PersonBuilder;
use Gedcomx\Tests\SourceBuilder;
use Gedcomx\Types\GenderType;
use Gedcomx\Rs\Client\Util\HttpStatus;

/*
 * Testing use cases https://familysearch.org/developers/docs/api/tree/Person_resource
 *
 * Only testing we get the expected response codes from the API. Data validation will
 * have to be added elsewhere.
 */
class PersonStateTest extends ApiTestCase{

    /**
     * @link https://familysearch.org/developers/docs/api/tree/Create_Person_Source_Reference_usecase
     * @throws \Gedcomx\Rs\Client\Exception\GedcomxApplicationException
     */
    public function testCreatePersonSourceReferenceWithStateObject()
    {
        $factory = new StateFactory();
        $this->collectionState($factory);

        if( self::$personState == null ){
            self::$personState = $this->createPerson();
        }
        $source = SourceBuilder::newSource();
        $sourceState = $this->collectionState()->addSourceDescription($source);

        $newState = self::$personState->addSourceReferenceState($sourceState);

        $this->assertAttributeEquals(HttpStatus::CREATED, "statusCode", $newState->getResponse() );
    }

    /**
     * @link https://familysearch.org/developers/docs/api/tree/Read_Notes_usecase
     */
    public function testReadPersonNote()
    {
        $factory = new StateFactory();
        $this->collectionState($factory);

        self::$personState = $this->getPerson();
        self::$personState->loadNotes();
        $person = self::$personState->getPerson();
        $notes = $person->getNotes();
        $newState = self::$personState
            ->readNote($notes[0]);

        $this->assertAttributeEquals(HttpStatus::OK, "statusCode", $newState->getResponse() );
    }

    /**
     * @link https://familysearch.org/developers/docs/api/tree/Delete_Person_usecase
     */
    public function testDeletePersonNote()
    {
        $factory = new StateFactory();
        $this->collectionState($factory);

        if( self::$personState == null ){
            self::$personState = $this->createPerson();
        }

        $note = NoteBuilder::createNote();
        $noteState = self::$personState->addNote( $note );

        $note = new Note();
        $note->addLink($noteState->getLink(Rel::SELF));

        $delState = self::$personState->deleteNote($note);

        $this->assertAttributeEquals(HttpStatus::NO_CONTENT, "statusCode", $delState->getResponse() );
    }

    /**
     * @link https://familysearch.org/developers/docs/api/tree/Update_Person_With_Preconditions_usecase
     */
    public function testUpdatePersonWithPreconditions()
    {
        $factory = new StateFactory();
        $this->collectionState($factory);

        if( self::$personState == null ){
            self::$personState = $this->createPerson()->get();
        }

        $mangled = str_replace(array(1,3,5,'a','b','d'), array(8,4,3,'Z','X','W'), self::$personState->getResponse()->getEtag());
        $check = new Preconditions();
        $check->setEtag($mangled);
        $check->setLastModified(new \DateTime(self::$personState->getResponse()->getLastModified()));

        $persons = self::$personState->getEntity()->getPersons();
        $state = self::$personState->update($persons[0], $check);
        $this->assertAttributeEquals(HttpStatus::PRECONDITION_FAILED, "statusCode", $state->getResponse());
    }

    /**
     * @link https://familysearch.org/developers/docs/api/tree/Delete_Person_Source_Reference_usecase
     */
    public function testDeletePersonSourceReference()
    {
        $factory = new StateFactory();
        $this->collectionState($factory);

        if( self::$personState == null ){
            self::$personState = $this->createPerson()->get();
        }

        $sourceState = $this->createSource();
        $this->assertAttributeEquals(HttpStatus::CREATED, "statusCode", $sourceState->getResponse() );

        $reference = new SourceReference();
        $reference->setDescriptionRef($sourceState->getSelfUri());

        self::$personState->addSourceReferenceObj($reference);
        $newState = self::$personState->loadSourceReferences();

        /** @var \Gedcomx\Conclusion\Person[] $persons */
        $persons = $newState->getEntity()->getPersons();
        $references = $persons[0]->getSources();
        $newerState = $newState->deleteSourceReference($references[0]);
        $this->assertAttributeEquals(HttpStatus::NO_CONTENT, "statusCode", $newerState->getResponse());
    }

    /**
     * @link https://familysearch.org/developers/docs/api/tree/Delete_Person_Conclusion_usecase
     */
    public function testDeletePersonConclusion()
    {
        $factory = new StateFactory();
        $this->collectionState($factory);

        if( self::$personState == null ){
            self::$personState = $this->createPerson();
        }
        if( self::$personState->getPerson() == null ){
            $uri = self::$personState->getSelfUri();
            self::$personState = $this->collectionState()->readPerson($uri);
        }
        $name = PersonBuilder::nickName();
        $newPersonState = self::$personState->addName($name);

        $this->assertAttributeEquals(HttpStatus::NO_CONTENT, "statusCode", $newPersonState->getResponse() );
        $newPersonState = self::$personState->get();

        /** @var \Gedcomx\Conclusion\Person[] $persons */
        $persons = $newPersonState->getEntity()->getPersons();
        $names = $persons[0]->getNames();
        $deletedState = $newPersonState->deleteName($names[1]);

        $this->assertAttributeEquals(HttpStatus::NO_CONTENT, "statusCode", $deletedState->getResponse());
    }

    /**
     * @link https://familysearch.org/developers/docs/api/tree/Delete_Person_usecase
     */
    public function testDeletePerson()
    {
        $factory = new StateFactory();
        $this->collectionState($factory);

        $personState = $this->createPerson()->get();

        $dState = $personState->delete();
        $this->assertAttributeEquals(HttpStatus::NO_CONTENT, "statusCode", $dState->getResponse());
    }

    /**
     * @link https://familysearch.org/developers/docs/api/tree/Delete_Person_With_Preconditions_usecase
     */
    public function testDeletePersonWithPreconditions()
    {
        $factory = new StateFactory();
        $this->collectionState($factory);

        if( self::$personState == null ){
            self::$personState = $this->createPerson()->get();
        }

        $mangled = str_replace(array(1,3,5,'a','b','d'), array(8,4,3,'Z','X','W'), self::$personState->getResponse()->getEtag());
        $check = new Preconditions();
        $check->setEtag($mangled);
        $check->setLastModified(new \DateTime(self::$personState->getResponse()->getLastModified()));

        $dState = self::$personState->delete($check);
        $this->assertAttributeEquals(HttpStatus::PRECONDITION_FAILED, "statusCode", $dState->getResponse());
    }

    /**
     * @link https://familysearch.org/developers/docs/api/tree/Delete_Discussion_Reference_usecase
     */
    public function testDeleteDiscussionReference()
    {
        $factory = new FamilyTreeStateFactory();
        $this->collectionState($factory);

        $userState = $this->collectionState()->readCurrentUser();
        $discussion = DiscussionBuilder::createDiscussion($userState->getUser()->getTreeUserId());

        $discussionState = $this->collectionState()->addDiscussion($discussion);
        $ref = new DiscussionReference();
        $ref->setResource($discussionState->getSelfUri());

        $personState = $this->getPerson();
        $newState = $personState->deleteDiscussionReference($ref);

        $this->assertAttributeEquals(HttpStatus::NO_CONTENT, "statusCode", $newState->getResponse(), $this->buildFailMessage(__METHOD__, $newState) );
    }

    /**
     * @link https://familysearch.org/developers/docs/api/tree/Delete_Person_usecase
     * @link https://familysearch.org/developers/docs/api/tree/Read_Deleted_Person_usecase
     * @link https://familysearch.org/developers/docs/api/tree/Restore_Person_usecase
     */
    public function testDeleteAndRestorePerson()
    {
        $factory = new StateFactory();
        $this->collectionState($factory);

        $personState = $this->createPerson()->get();
        $newState = $personState->delete();
        $this->assertAttributeEquals(HttpStatus::NO_CONTENT, "statusCode", $newState->getResponse(), "Delete person failed. Returned {$newState->getResponse()->getStatusCode()}");

        /** @var \Gedcomx\Conclusion\Person[] $persons */
        $persons = $personState->getEntity()->getPersons();
        $id = $persons[0]->getId();
        $newState = $this->getPerson($id);
        $this->assertAttributeEquals(HttpStatus::GONE, "statusCode", $newState->getResponse(), "Read deleted person failed. Returned {$newState->getResponse()->getStatusCode()}");

        $factory = new FamilyTreeStateFactory();
        $ftOne = $this->collectionState($factory);
        $ftTwo = $ftOne->readPersonById($id);
        $ftThree = $ftTwo->restore();
        $this->assertAttributeEquals(HttpStatus::NO_CONTENT, "statusCode", $ftThree->getResponse(), "Restore person failed. Returned {$ftThree->getResponse()->getStatusCode()}");
    }

    /**
     * @link https://familysearch.org/developers/docs/api/tree/Read_Person_With_Relationships_usecase
     */
    public function testPersonWithRelationships()
    {
        $factory = new FamilyTreeStateFactory();
        $this->collectionState($factory);

        $person = $this->collectionState()->readPersonWithRelationshipsById($this->getPersonId());
        $this->assertAttributeEquals(HttpStatus::OK, "statusCode", $person->getResponse(), "Restore person failed. Returned {$person->getResponse()->getStatusCode()}");

        $thePerson = $person->getPerson();
        $ftRelationships = $person->getChildAndParentsRelationships();
        $relationships = $person->getRelationships();

        $data_check = $thePerson instanceof Person
                          && count($ftRelationships) > 0
                          && $ftRelationships[0] instanceof ChildAndParentsRelationship
                          && count($relationships) > 0
                          && $relationships[0] instanceof Relationship;
        $this->assertTrue($data_check);
    }

    /**
     * @link https://familysearch.org/developers/docs/api/tree/Update_Person_Not-a-Match_Declarations_usecase
     */
    public function testUpdatePersonNotAMatch()
    {
        $factory = new FamilyTreeStateFactory();
        $this->collectionState($factory);

        $personData = PersonBuilder::buildPerson(null);

        $one = $this->collectionState()->addPerson($personData)->get();
        $two = $this->collectionState()->addPerson($personData)->get();

        $nonMatch = $one->addNonMatchPerson($two->getPerson());
        $this->assertAttributeEquals(HttpStatus::NO_CONTENT, "statusCode", $nonMatch->getResponse(), "Restore person failed. Returned {$nonMatch->getResponse()->getStatusCode()}");
    }

    /**
     * @link https://familysearch.org/developers/docs/api/tree/Delete_Person_Not-a-Match_usecase
     */
    public function testDeletePersonNotAMatch()
    {
        $factory = new FamilyTreeStateFactory();
        $this->collectionState($factory);

        $personData = PersonBuilder::buildPerson(null);

        $one = $this->collectionState()->addPerson($personData)->get();
        $two = $this->collectionState()->addPerson($personData)->get();

        $nonMatch = $one->addNonMatchPerson($two->getPerson());
        $rematch = $nonMatch->removeNonMatch($two->getPerson());

        $this->assertAttributeEquals(HttpStatus::NO_CONTENT, "statusCode", $rematch->getResponse(), "Restore person failed. Returned {$rematch->getResponse()->getStatusCode()}");
    }

    /**
     * @link https://familysearch.org/developers/docs/api/tree/Update_Preferred_Spouse_Relationship_usecase
     * @link https://familysearch.org/developers/docs/api/tree/Read_Preferred_Spouse_Relationship_usecase
     * @link https://familysearch.org/developers/docs/api/tree/Delete_Preferred_Spouse_Relationship_usecase
     */
    public function testPreferredSpouseRelationship()
    {
        $factory = new FamilyTreeStateFactory();
        $this->collectionState($factory);

        $userState = $this->collectionState()->readCurrentUser();

        /* First create a relationship */
        $person1 = $this->createPerson('male')->get();
        $person2 = $this->createPerson('female')->get();
        $relation = $this->collectionState()->addSpouseRelationship($person1, $person2);
        $this->assertAttributeEquals(HttpStatus::CREATED, "statusCode", $relation->getResponse(), $this->buildFailMessage(__METHOD__, $relation));

        /* Set the preferred relationship */
        $updated = $this->collectionState()->updatePreferredSpouseRelationship(
            $userState->getUser()->getTreeUserId(),
            $person1->getPerson()->getId(),
            $relation
        );
        $this->assertAttributeEquals(HttpStatus::NO_CONTENT, "statusCode", $updated->getResponse(), $this->buildFailMessage(__METHOD__, $updated));

        /* Read the preferred state */
        $preferred = $this->collectionState()->readPreferredSpouseRelationship(
            $userState->getUser()->getTreeUserId(),
            $person1->getPerson()->getId()
        );
        /*
         * readPreferredSpouseRelationship returns a '303/See Other' response which
         * the HTTP client will follow. We'll test to make sure that the effective
         * URL on the response contains 'couple-relationship' which indicates we've
         * been bounced to the preferred relationship.
         */
        $this->assertAttributeContains('couple-relationship', "effectiveUrl", $preferred->getResponse(), $this->buildFailMessage(__METHOD__, $preferred));

        /* Now clean up */
        $updated = $this->collectionState()->deletePreferredSpouseRelationship(
            $userState->getUser()->getTreeUserId(),
            $person1->getPerson()->getId()
        );
        $this->assertAttributeEquals(HttpStatus::NO_CONTENT, "statusCode", $updated->getResponse(), $this->buildFailMessage(__METHOD__, $updated));

        $relation->delete();
        $person1->delete();
        $person2->delete();

    }

    /**
     * @link https://familysearch.org/developers/docs/api/tree/Update_Preferred_Parent_Relationship_usecase
     * @link https://familysearch.org/developers/docs/api/tree/Read_Preferred_Parent_Relationship_usecase
     * @link https://familysearch.org/developers/docs/api/tree/Delete_Preferred_Parent_Relationship_usecase
     */
    public function testPreferredParentRelationship()
    {
        $factory = new FamilyTreeStateFactory();
        $this->collectionState($factory);

        $userState = $this->collectionState()->readCurrentUser();

        /* First create a relationship */
        $person1 = $this->createPerson('male')->get();
        $person2 = $this->createPerson('male')->get();
        $relation = $this->collectionState()->addChildAndParents($person1, $person2);
        $this->assertAttributeEquals(HttpStatus::CREATED, "statusCode", $relation->getResponse(), $this->buildFailMessage(__METHOD__, $relation));

        /* Set the preferred relationship */
        $updated = $this->collectionState()->updatePreferredParentRelationship(
            $userState->getUser()->getTreeUserId(),
            $person1->getPerson()->getId(),
            $relation
        );
        $this->assertAttributeEquals(HttpStatus::NO_CONTENT, "statusCode", $updated->getResponse(), $this->buildFailMessage(__METHOD__, $updated));

        /* Read the preferred state */
        $preferred = $this->collectionState()->readPreferredParentRelationship(
            $userState->getUser()->getTreeUserId(),
            $person1->getPerson()->getId()
        );
        /*
         * readPreferredParentRelationship returns a '303/See Other' response which
         * the HTTP client will follow. We'll test to make sure that the effective
         * URL on the response contains 'child-and-parents-relationship' which indicates we've
         * been bounced to the preferred relationship.
         */
        $this->assertAttributeContains('child-and-parents-relationship', "effectiveUrl", $preferred->getResponse(), $this->buildFailMessage(__METHOD__, $preferred));

        /* Now clean up */
        $updated = $this->collectionState()->deletePreferredSpouseRelationship(
            $userState->getUser()->getTreeUserId(),
            $person1->getPerson()->getId()
        );
        $this->assertAttributeEquals(HttpStatus::NO_CONTENT, "statusCode", $updated->getResponse(), $this->buildFailMessage(__METHOD__, $updated));

        $relation->delete();
        $person1->delete();
        $person2->delete();

    }

    /**
     * @link https://familysearch.org/developers/docs/api/tree/Read_User_usecase
     */
    public function testAgentReadUser()
    {
        $factory = new StateFactory();
        $this->collectionState($factory);
        $personState = $this->createPerson()->get();
        $names = $personState->getPerson()->getNames();
        $agentState = $personState->readAttributableContributor($names[0]);

        $this->assertEquals(
            HttpStatus::OK,
            $agentState->getResponse()->getStatusCode(),
            $this->buildFailMessage(__METHOD__, $agentState)
        );
        $this->assertNotEmpty($agentState->getAgent());
    }


}