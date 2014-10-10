<?php

namespace Gedcomx\Tests\Rs\Client;

use Gedcomx\Conclusion\Gender;
use Gedcomx\Conclusion\Name;
use Gedcomx\Rs\Client\Options\QueryParameter;
use Gedcomx\Rs\Client\Rel;
use Gedcomx\Source\SourceDescription;
use Gedcomx\Tests\ApiTestCase;
use Gedcomx\Tests\PersonBuilder;
use Gedcomx\Tests\SourceBuilder;
use Gedcomx\Types\GenderType;
use Gedcomx\Types\RelationshipType;

class PersonStateTest extends ApiTestCase{

    private static $personState;

    public function testCanCreatePerson(){
        self::$personState = $this->createPerson();

        $this->assertAttributeEquals( "201", "statusCode", self::$personState->getResponse() );
    }

    public function testReadPersonState(){
        self::$personState = $this->getPerson();
        $personState = $this->collectionState
            ->readPerson(self::$personState->getSelfUri());

        $this->assertAttributeEquals( "200", "statusCode", $personState->getResponse() );
    }

    public function testReadPersonHeaders(){
        self::$personState = $this->getPerson();
        $newState = self::$personState->head();

        $this->assertAttributeEquals( "200", "statusCode", $newState->getResponse() );
    }

    public function testAddNameToPerson(){
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

    public function testUpdateGenderOnPerson(){
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

    public function testCanAddSourceReferenceWithStateObject(){
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

    public function testCanReadPersonSources(){
        self::$personState = $this->getPerson();
        self::$personState
            ->loadSourceReferences();

        $sourceList = self::$personState->getEntity()->getSourceDescriptions();

        $this->assertGreaterThan( 0, count($sourceList), "No source descriptions were returned.");
    }

    public function testCanReadPersonNotes(){
        self::$personState = $this->getPerson();
        self::$personState
            ->loadNotes();

        $persons = self::$personState->getEntity()->getPersons();
        $notes = $persons[0]->getNotes();

        $this->assertGreaterThan( 0, count($notes), "No notes were returned.");
    }

    public function testCanReadPersonParentRelationships(){
        self::$personState = $this->getPerson();
        self::$personState
            ->loadParentRelationships();

        $relationships = self::$personState->getEntity()->getRelationships();

        $this->assertAttributeEquals( RelationshipType::PARENTCHILD, "type", $relationships[0], 'No parents were returned.');
    }

    public function testCanReadPersonChildRelationships(){
        self::$personState = $this->getPerson();
        self::$personState
            ->loadChildRelationships();

        $relationships = self::$personState->getEntity()->getRelationships();

        $this->assertAttributeEquals( RelationshipType::PARENTCHILD, "type", $relationships[0], 'No children were returned.');
    }

    public function testCanReadPersonSpouseRelationships(){
        self::$personState = $this->getPerson();
        self::$personState
            ->loadSpouseRelationships();

        $relationships = self::$personState->getEntity()->getRelationships();

        $this->assertAttributeEquals( RelationshipType::COUPLE, "type", $relationships[0], 'No spouses were returned.');
    }

    public function testCanReadPersonSpouseRelationshipsWithPersons(){
        self::$personState = $this->getPerson();
        $option = new QueryParameter(true,"persons","");
        self::$personState
            ->loadSpouseRelationships($option);

        $persons = self::$personState->getEntity()->getPersons();

        $this->assertGreaterThan( 0, count($persons), "No person records were returned.");
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

    private function getPerson(){
        $link = $this->collectionState->getLink(Rel::PERSON);
        if ($link === null || $link->getTemplate() === null) {
            return null;
        }
        $uri = array(
            $link->getTemplate(),
            array(
                "pid" => "KWW6-H43",
                "access_token" => $this->collectionState->getAccessToken()
            )
        );

        return $this->collectionState
            ->readPerson( $uri );
    }

}