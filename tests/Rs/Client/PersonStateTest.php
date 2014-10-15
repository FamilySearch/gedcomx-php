<?php

namespace Gedcomx\Tests\Rs\Client;

use Gedcomx\Common\Attribution;
use Gedcomx\Conclusion\Gender;
use Gedcomx\Rs\Client\Options\HeaderParameter;
use Gedcomx\Rs\Client\Options\Preconditions;
use Gedcomx\Rs\Client\Options\QueryParameter;
use Gedcomx\Rs\Client\Rel;
use Gedcomx\Source\SourceReference;
use Gedcomx\Tests\ApiTestCase;
use Gedcomx\Tests\NoteBuilder;
use Gedcomx\Tests\PersonBuilder;
use Gedcomx\Tests\SourceBuilder;
use Gedcomx\Types\GenderType;

class PersonStateTest extends ApiTestCase{

    /**
     * @var \Gedcomx\Rs\Client\PersonState
     */
    private static $personState;

    /*
     * Example requests from https://familysearch.org/developers/docs/api/tree/Person_resource
     */
    public function testCreatePerson(){
        self::$personState = $this->createPerson();

        $this->assertAttributeEquals( "201", "statusCode", self::$personState->getResponse() );
    }

    public function testCreatePersonSourceReference()
    {
        if( self::$personState == null ){
            self::$personState = $this->createPerson()->get();
        }

        $sourceState = $this->createSource();
        $this->assertAttributeEquals( "201", "statusCode", $sourceState->getResponse() );

        $reference = new SourceReference();
        $reference->setDescriptionRef($sourceState->getSelfUri());
        $reference->setAttribution( new Attribution( array(
            "changeMessage" => $this->faker->sentence(6)
        )));
        $newState = self::$personState->addSourceReferenceObj($reference);
        $this->assertAttributeEquals( "201", "statusCode", $newState->getResponse() );
        /*
         * todo: implement test for PersonState::addSourceReferenceRecord
         */
    }

    public function testCreatePersonSourceReferenceWithStateObject()
    {
        if( self::$personState == null ){
            self::$personState = $this->createPerson();
        }
        $source = SourceBuilder::buildSource();
        $sourceState = $this->collectionState
            ->addSourceDescription($source);

        $newState = self::$personState->addSourceReferenceState($sourceState);

        $this->assertAttributeEquals( "201", "statusCode", $newState->getResponse() );
    }


    /*
     * https://familysearch.org/developers/docs/api/tree/Create_Person_Conclusion_usecase
     */
    public function testAddFactToPerson(){
        if( self::$personState == null ){
            self::$personState = $this->createPerson();
        }
        if( self::$personState->getPerson() == null ){
            $uri = self::$personState->getSelfUri();
            self::$personState = $this->collectionState
                ->readPerson($uri);
        }
        $fact = PersonBuilder::militaryService();
        $newState = self::$personState->addFact($fact);

        $this->assertAttributeEquals( "200", "statusCode", $newState->getResponse() );
    }

    /**
     * https://familysearch.org/developers/docs/api/tree/Create_Discussion_Reference_usecase
     */
    public function testCreateDiscussionReference(){
        //todo: implement testCreateDiscussionReference: requires FamilyTree Extensions
        $this->assertTrue(true);
    }

    public function testCreateNote(){
        if( self::$personState == null ){
            self::$personState = $this->createPerson();
        }

        $note = NoteBuilder::createNote();
        $noteState = self::$personState->addNote( $note );

        $this->assertAttributeEquals( "201", "statusCode", $noteState->getResponse() );
    }

    /**
     * https://familysearch.org/developers/docs/api/tree/Read_Merged_Person_usecase
     */
    public function testReadMergedPerson(){
        // KWWV-DN4 was merged with KWWN-MQY
        self::$personState = $this->getPerson('KWWV-DN4');

        $this->assertAttributeEquals( "301", "statusCode", self::$personState->getResponse() );

    }

    /**
     * https://familysearch.org/developers/docs/api/tree/Read_Person_usecase
     */
    public function testReadPerson(){
        self::$personState = $this->getPerson();

        $this->assertAttributeEquals( "200", "statusCode", self::$personState->getResponse() );
    }

    /**
     * https://familysearch.org/developers/docs/api/tree/Read_Person_Source_References_usecase
     */
    public function testReadPersonSourceReferences(){
        self::$personState = $this->getPerson();
        self::$personState
            ->loadSourceReferences();

        $this->assertAttributeEquals( "200", "statusCode", self::$personState->getResponse() );
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
        self::$personState = $this->getPerson();
        self::$personState
            ->loadChildRelationships();

        $this->assertAttributeEquals( "200", "statusCode", self::$personState->getResponse() );
    }

    /**
     * https://familysearch.org/developers/docs/api/tree/Read_Relationships_to_Parents_usecase
     */
    public function testReadRelationshipsToParents(){
        self::$personState = $this->getPerson();
        self::$personState
            ->loadParentRelationships();

        $this->assertAttributeEquals( "200", "statusCode", self::$personState->getResponse() );
    }

    /**
     * https://familysearch.org/developers/docs/api/tree/Read_Relationships_To_Spouses_usecase
     */
    public function testReadRelationshipsToSpouses(){
        self::$personState = $this->getPerson();
        self::$personState
            ->loadSpouseRelationships();

        $this->assertAttributeEquals( "200", "statusCode", self::$personState->getResponse() );
    }

    /**
     * https://familysearch.org/developers/docs/api/tree/Read_Relationships_To_Spouses_with_Persons_usecase
     */
    public function testReadRelationshipsToSpousesWithPersons(){
        self::$personState = $this->getPerson();
        $option = new QueryParameter(true,"persons","");
        self::$personState
            ->loadSpouseRelationships($option);

        $this->assertAttributeEquals( "200", "statusCode", self::$personState->getResponse() );
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
        if( self::$personState == null ){
            self::$personState = $this->getPerson();
        }
        $childrenState = self::$personState
            ->readChildren();

        $this->assertAttributeEquals( "200", "statusCode", $childrenState->getResponse() );
    }

    /**
     * https://familysearch.org/developers/docs/api/tree/Read_Not_Found_Person_usecase
     *
     * @expectedException \Gedcomx\Rs\Client\Exception\GedcomxApplicationException
     * @expectedExceptionCode 404
     */
    public function testReadNotFoundPerson(){
        self::$personState = $this->getPerson('ABCD-123');
    }

    /**
     * https://familysearch.org/developers/docs/api/tree/Read_Not-Modified_Person_usecase
     */
    public function testReadNotModifiedPerson(){
        self::$personState = $this->getPerson();
        $options = array();
        $options[] = new HeaderParameter(true, HeaderParameter::IF_NONE_MATCH, self::$personState->getResponse()->getEtag());
        $options[] = new HeaderParameter(true, HeaderParameter::ETAG, self::$personState->getResponse()->getEtag());

        $secondState = $this->getPerson(self::$personState->getPerson()->getId(), $options);

        $this->assertAttributeEquals( "304", "statusCode", $secondState->getResponse() );
    }

    /**
     * https://familysearch.org/developers/docs/api/tree/Read_Notes_usecase
     */
    public function testReadPersonNotes(){
        self::$personState = $this->getPerson();
        self::$personState
            ->loadNotes();

        $this->assertAttributeEquals( "200", "statusCode", self::$personState->getResponse() );
    }

    /**
     * https://familysearch.org/developers/docs/api/tree/Read_Parents_of_a_Person_usecase
     */
    public function testReadParentsOfPerson()
    {
        if( self::$personState == null ){
            self::$personState = $this->getPerson();
        }
        $parentState = self::$personState
            ->readParents();

        $this->assertAttributeEquals( "200", "statusCode", $parentState->getResponse() );
    }
    
    public function testReadSpousesOfPerson()
    {
        if( self::$personState == null ){
            self::$personState = $this->getPerson();
        }
        $spouseState = self::$personState
            ->readSpouses();

        $this->assertAttributeEquals( "200", "statusCode", $spouseState->getResponse() );
    }

    public function testHeadPerson()
    {
        self::$personState = $this->getPerson();
        $newState = self::$personState->head();

        $this->assertAttributeEquals( "200", "statusCode", $newState->getResponse() );
    }

    /**
     * https://familysearch.org/developers/docs/api/tree/Update_Person_Source_Reference_usecase
     */
    public function testUpdatePersonSourceReference()
    {
        if( self::$personState == null ){
            self::$personState = $this->createPerson()->get();
        }

        $sourceState = $this->createSource();
        $this->assertAttributeEquals( "201", "statusCode", $sourceState->getResponse() );

        $reference = new SourceReference();
        $reference->setDescriptionRef($sourceState->getSelfUri());
        $reference->setAttribution( new Attribution( array(
            "changeMessage" => $this->faker->sentence(6)
        )));

        self::$personState->addSourceReferenceObj($reference);
        $newState = self::$personState->loadSourceReferences();
        $persons = $newState->getEntity()->getPersons();
        $newerState = $newState->updateSourceReferences($persons[0]);
        $this->assertAttributeEquals( "204", "statusCode", $newerState->getResponse() );
    }

    public function testUpdatePersonConclusion()
    {
        if( self::$personState == null ){
            self::$personState = $this->createPerson();
        }
        if( self::$personState->getPerson() == null ){
            $uri = self::$personState->getSelfUri();
            self::$personState = $this->collectionState
                ->readPerson($uri);
        }
        $gender = new Gender(array(
            "type" =>GenderType::MALE
        ));
        self::$personState->updateGender($gender);

        $this->assertAttributeEquals( "200", "statusCode", self::$personState->getResponse() );

    }

    public function testUpdatePersonCustomNonEventFact()
    {
        if( self::$personState == null ){
            self::$personState = $this->createPerson();
        }
        if( self::$personState->getPerson() == null ){
            $uri = self::$personState->getSelfUri();
            self::$personState = $this->collectionState
                ->readPerson($uri);
        }
        $fact = PersonBuilder::eagleScout();
        $newState = self::$personState->addFact($fact);

        $this->assertAttributeEquals( "204", "statusCode", $newState->getResponse() );
    }

    /**
     * https://familysearch.org/developers/docs/api/tree/Update_Person_With_Preconditions_usecase
     *
     * @expectedException \Gedcomx\Rs\Client\Exception\GedcomxApplicationException
     * @expectedExceptionCode 412
     */
    public function testUpdatePersonWithPreconditions()
    {
        if( self::$personState == null ){
            self::$personState = $this->createPerson()->get();
        }

        $mangled = str_replace(array(1,3,5,'a','b','d'), array(8,4,3,'Z','X','W'), self::$personState->getResponse()->getEtag());
        $check = new Preconditions();
        $check->setEtag($mangled);
        $check->setLastModified(new \DateTime(self::$personState->getResponse()->getLastModified()));

        $persons = self::$personState->getEntity()->getPersons();
        self::$personState->update($persons[0], $check);
    }

    /**
     * https://familysearch.org/developers/docs/api/tree/Delete_Person_Source_Reference_usecase
     *
     * @throws \Gedcomx\Rs\Client\Exception\GedcomxApplicationException
     */
    public function testDeletePersonSourceReference()
    {
        if( self::$personState == null ){
            self::$personState = $this->createPerson()->get();
        }

        $sourceState = $this->createSource();
        $this->assertAttributeEquals( "201", "statusCode", $sourceState->getResponse() );

        $reference = new SourceReference();
        $reference->setDescriptionRef($sourceState->getSelfUri());

        self::$personState->addSourceReferenceObj($reference);
        $newState = self::$personState->loadSourceReferences();

        /** @var \Gedcomx\Conclusion\Person[] $persons */
        $persons = $newState->getEntity()->getPersons();
        $references = $persons[0]->getSources();
        $newerState = $newState->deleteSourceReference($references[0]);
        $this->assertAttributeEquals( "204", "statusCode", $newerState->getResponse() );
    }

    /**
     * https://familysearch.org/developers/docs/api/tree/Delete_Person_Conclusion_usecase
     */
    public function testDeletePersonConclusion()
    {
        if( self::$personState == null ){
            self::$personState = $this->createPerson();
        }
        if( self::$personState->getPerson() == null ){
            $uri = self::$personState->getSelfUri();
            self::$personState = $this->collectionState
                ->readPerson($uri);
        }
        $name = PersonBuilder::nickName();
        $newPersonState = self::$personState->addName($name);

        $this->assertAttributeEquals( "204", "statusCode", $newPersonState->getResponse() );
        $newPersonState = self::$personState->get();

        /** @var \Gedcomx\Conclusion\Person[] $persons */
        $persons = $newPersonState->getEntity()->getPersons();
        $names = $persons[0]->getNames();
        $deletedState = $newPersonState->deleteName($names[1]);

        $this->assertAttributeEquals( "204", "statusCode", $deletedState->getResponse() );
    }

    /**
     * https://familysearch.org/developers/docs/api/tree/Delete_Person_Conclusion_usecase
     *
     * @expectedException \Gedcomx\Rs\Client\Exception\GedcomxApplicationException
     * @expectedExceptionCode 412
     */
    public function testDeletePersonWithPreconditions()
    {
        if( self::$personState == null ){
            self::$personState = $this->createPerson()->get();
        }

        $mangled = str_replace(array(1,3,5,'a','b','d'), array(8,4,3,'Z','X','W'), self::$personState->getResponse()->getEtag());
        $check = new Preconditions();
        $check->setEtag($mangled);
        $check->setLastModified(new \DateTime(self::$personState->getResponse()->getLastModified()));

        self::$personState->delete($check);
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
     *
     * @expectedException \Gedcomx\Rs\Client\Exception\GedcomxApplicationException
     * @expectedExceptionCode 410
     */
    public function testDeletePerson()
    {
        self::$personState = $this->createPerson();
        $uri = self::$personState->getSelfUri();
        self::$personState = $this->collectionState
            ->readPerson($uri);
        $newState = self::$personState->delete();

        $this->assertAttributeEquals( "204", "statusCode", $newState->getResponse() );

        /** @var \Gedcomx\Conclusion\Person[] $persons */
        $persons = self::$personState->getEntity()->getPersons();
        $id = $persons[0]->getId();
        self::$personState = $this->getPerson($id);
    }

    /**
     * https://familysearch.org/developers/docs/api/tree/Restore_Person_usecase
     */
    public function testRestorePerson()
    {
        //todo: implement testRestorePerson: requires FamilyTree Extensions
        $this->assertTrue(true);
    }

    /*
     * Private helper functions
     */

    private function createPerson()
    {
        $person = PersonBuilder::buildPerson();
        return $this->collectionState
            ->addPerson($person);
    }

    private function getPerson($pid = 'KWW6-H43', array $options = array()){
        $link = $this->collectionState->getLink(Rel::PERSON);
        if ($link === null || $link->getTemplate() === null) {
            return null;
        }
        $uri = array(
            $link->getTemplate(),
            array(
                "pid" => $pid,
                "access_token" => $this->collectionState->getAccessToken()
            )
        );

        $args = array_merge(array($uri), $options);
        return call_user_func_array(array($this->collectionState,"readPerson"), $args);
    }

    private function createSource(){
        $source = SourceBuilder::buildSource();
        $link = $this->collectionState->getLink(Rel::SOURCE_DESCRIPTIONS);
        if ($link === null || $link->getHref() === null) {
            return null;
        }

        return $this->collectionState->addSourceDescription($source);
    }

}