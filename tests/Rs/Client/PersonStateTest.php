<?php

namespace Gedcomx\Tests\Rs\Client;

use Gedcomx\Common\Attribution;
use Gedcomx\Common\Note;
use Gedcomx\Conclusion\Gender;
use Gedcomx\Extensions\FamilySearch\Rs\Client\FamilyTree\FamilyTreeStateFactory;
use Gedcomx\Rs\Client\Options\HeaderParameter;
use Gedcomx\Rs\Client\Options\Preconditions;
use Gedcomx\Rs\Client\Options\QueryParameter;
use Gedcomx\Rs\Client\Rel;
use Gedcomx\Rs\Client\StateFactory;
use Gedcomx\Source\SourceReference;
use Gedcomx\Tests\ApiTestCase;
use Gedcomx\Tests\NoteBuilder;
use Gedcomx\Tests\PersonBuilder;
use Gedcomx\Tests\SourceBuilder;
use Gedcomx\Types\GenderType;
use Gedcomx\Rs\Client\Util\HttpStatus;

class PersonStateTest extends ApiTestCase{

    /**
     * @var \Gedcomx\Rs\Client\PersonState
     */
    private static $personState;

    /*
     * Example requests from https://familysearch.org/developers/docs/api/tree/Person_resource
     */
    public function testCreatePerson(){
        $factory = new StateFactory();
        $this->collectionState($factory);

        self::$personState = $this->createPerson();

        $this->assertAttributeEquals(HttpStatus::CREATED, "statusCode", self::$personState->getResponse() );
    }

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
     * https://familysearch.org/developers/docs/api/tree/Create_Person_Conclusion_usecase
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
        $fact = PersonBuilder::militaryService();
        $newState = self::$personState->addFact($fact);

        $this->assertAttributeEquals(HttpStatus::NO_CONTENT, "statusCode", $newState->getResponse() );
    }

    /**
     * https://familysearch.org/developers/docs/api/tree/Create_Discussion_Reference_usecase
     */
    public function testCreateDiscussionReference(){
        //todo: implement testCreateDiscussionReference: requires FamilyTree Extensions
        $this->assertTrue(true);
    }

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
     * https://familysearch.org/developers/docs/api/tree/Read_Merged_Person_usecase
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
        $persons = self::$personState->getEntity()->getPersons();
        $person = $persons[0];

        $this->assertNotEquals( 'KWWV-DN4', $person->getId() );
    }

    /**
     * https://familysearch.org/developers/docs/api/tree/Read_Person_usecase
     */
    public function testReadPerson(){
        $factory = new StateFactory();
        $this->collectionState($factory);

        self::$personState = $this->getPerson();

        $this->assertAttributeEquals(HttpStatus::OK, "statusCode", self::$personState->getResponse() );
    }

    /**
     * https://familysearch.org/developers/docs/api/tree/Read_Person_Source_References_usecase
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
     * https://familysearch.org/developers/docs/api/tree/Read_Person_Sources_usecase
     * Not implemented in the API. No links returned on PersonState for
     * /platform/tree/persons/PPPP-PPP/sources
     */

    /**
     * https://familysearch.org/developers/docs/api/tree/Read_Relationships_to_Children_usecase
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
     * https://familysearch.org/developers/docs/api/tree/Read_Relationships_to_Parents_usecase
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
     * https://familysearch.org/developers/docs/api/tree/Read_Relationships_To_Spouses_usecase
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
     * https://familysearch.org/developers/docs/api/tree/Read_Relationships_To_Spouses_with_Persons_usecase
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
     * https://familysearch.org/developers/docs/api/tree/Read_Discussion_References_usecase
     *
     * Requires Family Search Extensions to work properly. Will come back to it.
     */
    public function testReadDiscussionReferences(){
        //todo: implement testReadDiscussionReferences: requires FamilyTree Extentions
        $this->assertTrue(true);
    }

    /**
     * https://familysearch.org/developers/docs/api/tree/Read_Children_of_a_Person_usecase
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
     * https://familysearch.org/developers/docs/api/tree/Read_Not_Found_Person_usecase
     */
    public function testReadNotFoundPerson(){
        $factory = new StateFactory();
        $this->collectionState($factory);

        self::$personState = $this->getPerson('ABCD-123');
        $this->assertAttributeEquals(HttpStatus::NOT_FOUND, "statusCode", self::$personState ->getResponse() );
    }

    /**
     * https://familysearch.org/developers/docs/api/tree/Read_Not-Modified_Person_usecase
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
     * https://familysearch.org/developers/docs/api/tree/Read_Notes_usecase
     */
    public function testReadPersonNotes(){
        $factory = new StateFactory();
        $this->collectionState($factory);

        self::$personState = $this->getPerson();
        self::$personState->loadNotes();

        $this->assertAttributeEquals(HttpStatus::OK, "statusCode", self::$personState->getResponse() );
    }

    /**
     * https://familysearch.org/developers/docs/api/tree/Read_Notes_usecase
     */
    public function testReadPersonNote()
    {
        $factory = new StateFactory();
        $this->collectionState($factory);

        self::$personState = $this->getPerson();
        self::$personState->loadNotes();
        $persons = self::$personState->getEntity()->getPersons();
        $notes = $persons[0]->getNotes();
        $newState = self::$personState
            ->readNote($notes[0]);

        $this->assertAttributeEquals(HttpStatus::OK, "statusCode", $newState->getResponse() );
    }

    /**
     * @throws \Gedcomx\Rs\Client\Exception\GedcomxApplicationException
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
     * https://familysearch.org/developers/docs/api/tree/Read_Parents_of_a_Person_usecase
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

    public function testHeadPerson()
    {
        $factory = new StateFactory();
        $this->collectionState($factory);

        self::$personState = $this->getPerson();
        $newState = self::$personState->head();

        $this->assertAttributeEquals(HttpStatus::OK, "statusCode", $newState->getResponse());
    }

    /**
     * https://familysearch.org/developers/docs/api/tree/Update_Person_Source_Reference_usecase
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
        $fact = PersonBuilder::eagleScout();
        $newState = self::$personState->addFact($fact);

        $this->assertAttributeEquals(HttpStatus::NO_CONTENT, "statusCode", $newState->getResponse());
    }

    /**
     * https://familysearch.org/developers/docs/api/tree/Update_Person_With_Preconditions_usecase
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
     * https://familysearch.org/developers/docs/api/tree/Delete_Person_Source_Reference_usecase
     *
     * @throws \Gedcomx\Rs\Client\Exception\GedcomxApplicationException
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
     * https://familysearch.org/developers/docs/api/tree/Delete_Person_Conclusion_usecase
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
     * https://familysearch.org/developers/docs/api/tree/Delete_Person_Conclusion_usecase
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
     * https://familysearch.org/developers/docs/api/tree/Delete_Discussion_Reference_usecase
     */
    public function testDeleteDiscussionReference()
    {
        //todo: implement testDeleteDiscussionReference: requires FamilyTree Extensions
        $this->assertTrue(true);
    }

    /**
     * https://familysearch.org/developers/docs/api/tree/Delete_Person_usecase
     * https://familysearch.org/developers/docs/api/tree/Read_Deleted_Person_usecase
     * https://familysearch.org/developers/docs/api/tree/Restore_Person_usecase
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

    /*
     * Private helper functions
     */

    private function createPerson()
    {
        $person = PersonBuilder::buildPerson();
        return $this->collectionState()->addPerson($person);
    }

    private function getPerson($pid = 'KWW6-H43', array $options = array()){
        $link = $this->collectionState()->getLink(Rel::PERSON);
        if ($link === null || $link->getTemplate() === null) {
            return null;
        }
        $uri = array(
            $link->getTemplate(),
            array(
                "pid" => $pid
            )
        );

        $args = array_merge(array($uri), $options);
        return call_user_func_array(array($this->collectionState(),"readPerson"), $args);
    }

    private function createSource(){
        $source = SourceBuilder::buildSource();
        $link = $this->collectionState()->getLink(Rel::SOURCE_DESCRIPTIONS);
        if ($link === null || $link->getHref() === null) {
            return null;
        }

        return $this->collectionState()->addSourceDescription($source);
    }
}