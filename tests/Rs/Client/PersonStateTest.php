<?php

namespace Gedcomx\Tests\Rs\Client;

use Gedcomx\Conclusion\Gender;
use Gedcomx\Rs\Client\Options\QueryParameter;
use Gedcomx\Rs\Client\Rel;
use Gedcomx\Source\SourceDescription;
use Gedcomx\Tests\ApiTestCase;
use Gedcomx\Tests\PersonBuilder;
use Gedcomx\Tests\SourceBuilder;
use Gedcomx\Types\GenderType;
use Gedcomx\Types\RelationshipType;

class PersonStateTest extends ApiTestCase{

    /**
     * @var \Gedcomx\Rs\Client\PersonState $personState
     */
    private static $personState;

    /*
     * Example requests from https://familysearch.org/developers/docs/api/tree/Person_resource
     */
    public function testCreatePerson(){
        self::$personState = $this->createPerson();

        $this->assertAttributeEquals( "201", "statusCode", self::$personState->getResponse() );
    }
    
    public function testCreatePersonSourceReference(){
        //todo
    }
    
    public function testCreatePersonConclusion(){
        //todo
    }
    
    public function testCreateDiscussionReference(){
        //todo
    }

    public function testCreateNote(){
        //todo
    }

    /**
     * https://familysearch.org/developers/docs/api/tree/Read_Merged_Person_usecase
     *
     * @expectedException \Gedcomx\Rs\Client\Exception\GedcomxApplicationException
     * @expectedExceptionCode 301
     */
    public function testReadMergedPerson(){
        // KWWD-X35 was merged with KWWD-CMF
        self::$personState = $this->getPerson('KWWD-X35');
    }

    /**
     * https://familysearch.org/developers/docs/api/tree/Read_Deleted_Person_usecase
     *
     * @expectedException \Gedcomx\Rs\Client\Exception\GedcomxApplicationException
     * @expectedExceptionCode 410
     */
    public function testReadDeletedPerson(){
        self::$personState = $this->getPerson('KWQ7-Y57');
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

        $sourceList = self::$personState->getEntity()->getSourceDescriptions();

        $this->assertGreaterThan( 0, count($sourceList), "No source descriptions were returned.");
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

        $relationships = self::$personState->getEntity()->getRelationships();

        $this->assertAttributeEquals( RelationshipType::PARENTCHILD, "type", $relationships[0], 'No children were returned.');
    }

    /**
     * https://familysearch.org/developers/docs/api/tree/Read_Relationships_to_Parents_usecase
     */
    public function testReadRelationshipsToParents(){
        self::$personState = $this->getPerson();
        self::$personState
            ->loadParentRelationships();

        $relationships = self::$personState->getEntity()->getRelationships();

        $this->assertAttributeEquals( RelationshipType::PARENTCHILD, "type", $relationships[0], 'No parents were returned.');
    }

    /**
     * https://familysearch.org/developers/docs/api/tree/Read_Relationships_To_Spouses_usecase
     */
    public function testReadRelationshipsToSpouses(){
        self::$personState = $this->getPerson();
        self::$personState
            ->loadSpouseRelationships();

        $relationships = self::$personState->getEntity()->getRelationships();

        $this->assertAttributeEquals( RelationshipType::COUPLE, "type", $relationships[0], 'No spouses were returned.');
    }

    /**
     * https://familysearch.org/developers/docs/api/tree/Read_Relationships_To_Spouses_with_Persons_usecase
     */
    public function testReadRelationshipsToSpousesWithPersons(){
        self::$personState = $this->getPerson();
        $option = new QueryParameter(true,"persons","");
        self::$personState
            ->loadSpouseRelationships($option);

        $persons = self::$personState->getEntity()->getPersons();

        $this->assertGreaterThan( 0, count($persons), "No person records were returned.");
    }

    /**
     * https://familysearch.org/developers/docs/api/tree/Read_Discussion_References_usecase
     */
    public function testReadDiscussionReferences(){
        self::$personState = $this->getPerson();
        self::$personState
            ->loadDiscussionReferences();

        /*
         * Just want to make sure we got here without any errors for now.
         */
        $this->assertTrue(true);
    }

    public function testReadPersonChildren(){
        if( self::$personState == null ){
            self::$personState = $this->getPerson();
        }
        $childrenState = self::$personState
            ->readChildren();

        $children = $childrenState->getEntity()->getPersons();

        $this->assertGreaterThan( 0, count($children), "No person records were returned.");
    }
    
    public function testReadNotFoundPerson(){
        //todo
    }
    
    public function testReadNotModifiedPerson(){
        
    }

    public function testReadPersonNotes(){
        self::$personState = $this->getPerson();
        self::$personState
            ->loadNotes();

        $persons = self::$personState->getEntity()->getPersons();
        $notes = $persons[0]->getNotes();

        $this->assertGreaterThan( 0, count($notes), "No notes were returned.");
    }
    
    public function testReadParentsOfPerson()
    {
        //todo
    }
    
    public function testReadSpousesOfPerson()
    {
        //todo
    }

    public function testHeadPerson()
    {
        self::$personState = $this->getPerson();
        $newState = self::$personState->head();

        $this->assertAttributeEquals( "200", "statusCode", $newState->getResponse() );
    }

    public function testUpdatePersonSourceReference()
    {
        //todo
    }

    public function testUpdatePersonConclusion()
    {
        //todo
    }

    public function testUpdatePersonCustomNonEventFact()
    {
        //todo
    }

    public function testUpdatePersonWithPreconditions()
    {
        //todo
    }

    public function testDeletePerson()
    {
        //todo
    }

    public function testDeletePersonSourceReference()
    {
        //todo
    }

    public function testDeletePersonConclusion()
    {
        //todo
    }

    public function testDeletePersonWithPreconditions()
    {
        //todo
    }

    public function testDeleteDiscussionReference()
    {
        //todo
    }

    public function testRestorePerson()
    {
        //todo
    }
    /*
     * PersonState method tests not included above
     */

    // PersonState::updateGender - see testUpdatePersonConcl
    public function testAddName(){
        if( self::$personState == null ){
            self::$personState = $this->createPerson();
        }
        if( self::$personState->getPerson() == null ){
            $uri = self::$personState->getSelfUri();
            self::$personState = $this->collectionState
                ->readPerson($uri);
        }
        $name = PersonBuilder::nickName($this->faker);
        $newPersonState = self::$personState->addName($name);

        $this->assertAttributeEquals( "204", "statusCode", $newPersonState->getResponse() );
    }

    public function testUpdateGender(){
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
        self::$personState->addFact($fact);

        $this->assertAttributeEquals( "200", "statusCode", self::$personState->getResponse() );
    }

    public function testAddSourceReferenceWithStateObject(){
        /*
         * addSourceDescription isn't working. Will come back to this later.
         *
        if( self::$personState == null ){
            self::$personState = $this->createPerson();
        }
        $source = SourceBuilder::buildSource();
        $sourceState = $this->collectionState
            ->addSourceDescription($source);

        self::$personState->addSourceReference($sourceState);

        $this->assertAttributeEquals( "201", "statusCode", self::$personState->getResponse() );
         */
    }

    /*
     * Private helper functions
     */

    private function createPerson()
    {
        $person = PersonBuilder::buildPerson($this->faker);
        return $this->collectionState
            ->addPerson($person);
    }

    private function getPerson($pid = 'KWW6-H43'){
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

        return $this->collectionState
            ->readPerson( $uri );
    }

}