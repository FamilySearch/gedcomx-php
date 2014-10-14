<?php

namespace Gedcomx\Tests\Rs\Client;

use Gedcomx\Conclusion\Gender;
use Gedcomx\Rs\Client\Options\HeaderParameter;
use Gedcomx\Rs\Client\Options\QueryParameter;
use Gedcomx\Rs\Client\Rel;
use Gedcomx\Source\SourceDescription;
use Gedcomx\Tests\ApiTestCase;
use Gedcomx\Tests\NoteBuilder;
use Gedcomx\Tests\PersonBuilder;
use Gedcomx\Tests\SourceBuilder;
use Gedcomx\Types\GenderType;
use Gedcomx\Types\RelationshipType;

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

    public function testCreatePersonSourceReference(){
        //todo
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
        self::$personState->addFact($fact);

        $this->assertAttributeEquals( "200", "statusCode", self::$personState->getResponse() );
    }


    public function testCreateDiscussionReference(){
        //todo
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
        // KWWD-X35 was merged with KWWD-CMF
        self::$personState = $this->getPerson('KWWD-CMF');

        $this->assertAttributeEquals( "301", "statusCode", self::$personState->getResponse() );

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
     * todo
     * https://familysearch.org/developers/docs/api/tree/Read_Discussion_References_usecase
     *
     * Requires Family Search Extensions to work properly. Will come back to it.
     *
    public function testReadDiscussionReferences(){
    }
     */

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

    public function testUpdatePersonSourceReference()
    {
        //todo
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
        $name = PersonBuilder::nickName();
        $newPersonState = self::$personState->addName($name);

        $this->assertAttributeEquals( "204", "statusCode", $newPersonState->getResponse() );
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

}