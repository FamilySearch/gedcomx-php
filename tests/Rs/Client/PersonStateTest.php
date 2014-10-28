<?php

namespace Gedcomx\Tests\Rs\Client;

use Gedcomx\Common\Attribution;
use Gedcomx\Common\Note;
use Gedcomx\Conclusion\Gender;
use Gedcomx\Conclusion\Person;
use Gedcomx\Conclusion\Relationship;
use Gedcomx\Extensions\FamilySearch\Platform\Tree\ChildAndParentsRelationship;
use Gedcomx\Extensions\FamilySearch\Platform\Tree\DiscussionReference;
use Gedcomx\Extensions\FamilySearch\Rs\Client\FamilySearchStateFactory;
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
     * @var \Gedcomx\Rs\Client\PersonState
     */
    private static $personState;

    /**
     * @link https://familysearch.org/developers/docs/api/tree/Create_Person_usecase
     */
    public function testCreatePerson(){
        $factory = new StateFactory();
        $this->collectionState($factory);

        self::$personState = $this->createPerson();

        $this->assertAttributeEquals(HttpStatus::CREATED, "statusCode", self::$personState->getResponse(), $this->buildFailMessage(__METHOD__, self::$personState) );
    }

    /**
     * @link https://familysearch.org/developers/docs/api/tree/Create_Person_Source_Reference_usecase
     */
    public function testCreatePersonSourceReference()
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
        $reference->setAttribution( new Attribution( array(
            "changeMessage" => $this->faker->sentence(6)
        )));
        $newState = self::$personState->addSourceReferenceObj($reference);
        $this->assertAttributeEquals(HttpStatus::CREATED, "statusCode", $newState->getResponse() );
        /*
         * todo: implement test for PersonState::addSourceReferenceRecord
         */
    }

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
        $source = SourceBuilder::buildSource();
        $sourceState = $this->collectionState()->addSourceDescription($source);

        $newState = self::$personState->addSourceReferenceState($sourceState);

        $this->assertAttributeEquals(HttpStatus::CREATED, "statusCode", $newState->getResponse() );
    }


    /*
     * @link https://familysearch.org/developers/docs/api/tree/Create_Person_Conclusion_usecase
     */
    public function testAddFactToPerson(){
        $factory = new StateFactory();
        $this->collectionState($factory);

        if( self::$personState == null ){
            self::$personState = $this->createPerson();
        }
        if( self::$personState->getPerson() == null ){
            $uri = self::$personState->getSelfUri();
            self::$personState = $this->collectionState()->readPerson($uri);
        }
        $fact = FactBuilder::militaryService();
        $newState = self::$personState->addFact($fact);

        $this->assertAttributeEquals(HttpStatus::NO_CONTENT, "statusCode", $newState->getResponse() );
    }

    /**
     * @link https://familysearch.org/developers/docs/api/tree/Create_Discussion_Reference_usecase
     * @link https://familysearch.org/developers/docs/api/tree/Read_Discussion_References_usecase
     */
    public function testCreateAndReadDiscussionReference(){
        $factory = new FamilyTreeStateFactory();
        $this->collectionState($factory);

        $userState = $this->collectionState()->readCurrentUser();
        $discussion = DiscussionBuilder::createDiscussion($userState->getUser()->getTreeUserId());

        $discussionState = $this->collectionState()->addDiscussion($discussion);

        $personState = $this->getPerson();
        $newState = $personState->addDiscussionState($discussionState);

        $this->assertAttributeEquals(HttpStatus::CREATED, "statusCode", $newState->getResponse(), $this->buildFailMessage(__METHOD__, $newState) );

        $personState->loadDiscussionReferences();

        $found = false;
        foreach ($personState->getPerson()->getExtensionElements() as $ext) {
            if ($ext instanceof DiscussionReference) {
                $found = true;
                break;
            }
        }
        $this->assertTrue($found);
    }

    /**
     * @link https://familysearch.org/developers/docs/api/tree/Create_Note_usecase
     */
    public function testCreateNote(){
        $factory = new StateFactory();
        $this->collectionState($factory);

        if( self::$personState == null ){
            self::$personState = $this->createPerson();
        }

        $note = NoteBuilder::createNote();
        $noteState = self::$personState->addNote( $note );

        $this->assertAttributeEquals(HttpStatus::CREATED, "statusCode", $noteState->getResponse() );
    }

    /**
     * @link https://familysearch.org/developers/docs/api/tree/Read_Merged_Person_usecase
     */
    public function testReadMergedPerson(){
        // KWWV-DN4 was merged with KWWN-MQY
        $factory = new StateFactory();
        $this->collectionState($factory);

        self::$personState = $this->getPerson('KWWV-DN4');
        /**
         * This assertion--technically the correct response for a person that has been merged--
         * assumes that the HTTP client code does not automatically follow redirects.
         *
         * $this->assertAttributeEquals(HttpStatus::MOVED_PERMANENTLY, "statusCode", self::$personState->getResponse() );
         *
         * Hacking the code to disable the redirect feature for this test seems undesirable. Instead we'll
         * assert that an id different from the one we requested is returned.
         */
        $person = self::$personState->getPerson();

        $this->assertNotEquals( 'KWWV-DN4', $person->getId() );
    }

    /**
     * @link https://familysearch.org/developers/docs/api/tree/Read_Person_usecase
     */
    public function testReadPerson(){
        $factory = new StateFactory();
        $this->collectionState($factory);

        self::$personState = $this->getPerson();

        $this->assertAttributeEquals(HttpStatus::OK, "statusCode", self::$personState->getResponse() );
    }

    /**
     * @link https://familysearch.org/developers/docs/api/tree/Read_Person_Source_References_usecase
     */
    public function testReadPersonSourceReferences(){
        $factory = new StateFactory();
        $this->collectionState($factory);

        self::$personState = $this->getPerson();
        self::$personState
            ->loadSourceReferences();

        $this->assertAttributeEquals(HttpStatus::OK, "statusCode", self::$personState->getResponse() );
    }

    /**
     * @link https://familysearch.org/developers/docs/api/tree/Read_Person_Sources_usecase
     */
    public function testReadPersonSources()
    {
        // Not currently implemented in the API. No links returned on PersonState for
        // /platform/tree/persons/PPPP-PPP/sources
        $this->assertTrue(true);
    }

    /**
     * @link https://familysearch.org/developers/docs/api/tree/Read_Relationships_to_Children_usecase
     */
    public function testReadRelationshipsToChildren(){
        $factory = new StateFactory();
        $this->collectionState($factory);

        self::$personState = $this->getPerson();
        self::$personState
            ->loadChildRelationships();

        $this->assertAttributeEquals(HttpStatus::OK, "statusCode", self::$personState->getResponse() );
    }

    /**
     * @link https://familysearch.org/developers/docs/api/tree/Read_Relationships_to_Parents_usecase
     */
    public function testReadRelationshipsToParents(){
        $factory = new StateFactory();
        $this->collectionState($factory);

        self::$personState = $this->getPerson();
        self::$personState
            ->loadParentRelationships();

        $this->assertAttributeEquals(HttpStatus::OK, "statusCode", self::$personState->getResponse() );
    }

    /**
     * @link https://familysearch.org/developers/docs/api/tree/Read_Relationships_To_Spouses_usecase
     */
    public function testReadRelationshipsToSpouses(){
        $factory = new StateFactory();
        $this->collectionState($factory);

        self::$personState = $this->getPerson();
        self::$personState
            ->loadSpouseRelationships();

        $this->assertAttributeEquals(HttpStatus::OK, "statusCode", self::$personState->getResponse() );
    }

    /**
     * @link https://familysearch.org/developers/docs/api/tree/Read_Relationships_To_Spouses_with_Persons_usecase
     */
    public function testReadRelationshipsToSpousesWithPersons(){
        $factory = new StateFactory();
        $this->collectionState($factory);

        self::$personState = $this->getPerson();
        $option = new QueryParameter(true,"persons","");
        self::$personState
            ->loadSpouseRelationships($option);

        $this->assertAttributeEquals(HttpStatus::OK, "statusCode", self::$personState->getResponse() );
    }

    /**
     * @link https://familysearch.org/developers/docs/api/tree/Read_Children_of_a_Person_usecase
     */
    public function testReadPersonChildren(){
        $factory = new StateFactory();
        $this->collectionState($factory);

        if( self::$personState == null ){
            self::$personState = $this->getPerson();
        }
        $childrenState = self::$personState
            ->readChildren();

        $this->assertAttributeEquals(HttpStatus::OK, "statusCode", $childrenState->getResponse() );
    }

    /**
     * @link https://familysearch.org/developers/docs/api/tree/Read_Not_Found_Person_usecase
     */
    public function testReadNotFoundPerson(){
        $factory = new StateFactory();
        $this->collectionState($factory);

        self::$personState = $this->getPerson('ABCD-123');
        $this->assertAttributeEquals(HttpStatus::NOT_FOUND, "statusCode", self::$personState ->getResponse() );
    }

    /**
     * @link https://familysearch.org/developers/docs/api/tree/Read_Not-Modified_Person_usecase
     */
    public function testReadNotModifiedPerson(){
        $factory = new StateFactory();
        $this->collectionState($factory);

        self::$personState = $this->getPerson();
        $options = array();
        $options[] = new HeaderParameter(true, HeaderParameter::IF_NONE_MATCH, self::$personState->getResponse()->getEtag());
        $options[] = new HeaderParameter(true, HeaderParameter::ETAG, self::$personState->getResponse()->getEtag());

        $secondState = $this->getPerson(self::$personState->getPerson()->getId(), $options);

        $this->assertAttributeEquals(HttpStatus::NOT_MODIFIED, "statusCode", $secondState->getResponse() );
    }

    /**
     * @link https://familysearch.org/developers/docs/api/tree/Read_Notes_usecase
     */
    public function testReadPersonNotes(){
        $factory = new StateFactory();
        $this->collectionState($factory);

        self::$personState = $this->getPerson();
        self::$personState->loadNotes();

        $this->assertAttributeEquals(HttpStatus::OK, "statusCode", self::$personState->getResponse() );
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
     * @link https://familysearch.org/developers/docs/api/tree/Read_Parents_of_a_Person_usecase
     */
    public function testReadParentsOfPerson()
    {
        $factory = new StateFactory();
        $this->collectionState($factory);

        if( self::$personState == null ){
            self::$personState = $this->getPerson();
        }
        $parentState = self::$personState
            ->readParents();

        $this->assertAttributeEquals(HttpStatus::OK, "statusCode", $parentState->getResponse() );
    }

    /**
     * @link https://familysearch.org/developers/docs/api/tree/Read_Spouses_of_a_Person_usecase
     */
    public function testReadSpousesOfPerson()
    {
        $factory = new StateFactory();
        $this->collectionState($factory);

        if( self::$personState == null ){
            self::$personState = $this->getPerson();
        }
        $spouseState = self::$personState
            ->readSpouses();

        $this->assertAttributeEquals(HttpStatus::OK, "statusCode", $spouseState->getResponse());
    }

    /**
     * @link https://familysearch.org/developers/docs/api/tree/Head_Person_usecase
     */
    public function testHeadPerson()
    {
        $factory = new StateFactory();
        $this->collectionState($factory);

        self::$personState = $this->getPerson();
        $newState = self::$personState->head();

        $this->assertAttributeEquals(HttpStatus::OK, "statusCode", $newState->getResponse());
    }

    /**
     * @link https://familysearch.org/developers/docs/api/tree/Update_Person_Source_Reference_usecase
     */
    public function testUpdatePersonSourceReference()
    {
        $factory = new StateFactory();
        $this->collectionState($factory);

        if( self::$personState == null ){
            self::$personState = $this->createPerson()->get();
        }

        $sourceState = $this->createSource();
        $this->assertAttributeEquals(HttpStatus::CREATED, "statusCode", $sourceState->getResponse());

        $reference = new SourceReference();
        $reference->setDescriptionRef($sourceState->getSelfUri());
        $reference->setAttribution( new Attribution( array(
            "changeMessage" => $this->faker->sentence(6)
        )));

        self::$personState->addSourceReferenceObj($reference);
        $newState = self::$personState->loadSourceReferences();
        $persons = $newState->getEntity()->getPersons();
        $newerState = $newState->updateSourceReferences($persons[0]);
        $this->assertAttributeEquals(HttpStatus::NO_CONTENT, "statusCode", $newerState->getResponse());
    }

    /**
     * @link https://familysearch.org/developers/docs/api/tree/Update_Person_Conclusion_usecase
     */
    public function testUpdatePersonConclusion()
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
        $gender = new Gender(array(
            "type" =>GenderType::MALE
        ));
        self::$personState->updateGender($gender);

        $this->assertAttributeEquals(HttpStatus::OK, "statusCode", self::$personState->getResponse());

    }

    /**
     * @link https://familysearch.org/developers/docs/api/tree/Update_Person_Custom_Non-Event_Fact_usecase
     */
    public function testUpdatePersonCustomNonEventFact()
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
        $fact = FactBuilder::eagleScout();
        $newState = self::$personState->addFact($fact);

        $this->assertAttributeEquals(HttpStatus::NO_CONTENT, "statusCode", $newState->getResponse());
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

}