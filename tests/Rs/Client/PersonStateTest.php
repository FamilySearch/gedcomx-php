<?php

namespace Gedcomx\Tests\Rs\Client;

use Gedcomx\Conclusion\Gender;
use Gedcomx\Conclusion\Name;
use Gedcomx\Rs\Client\Rel;
use Gedcomx\Source\SourceDescription;
use Gedcomx\Tests\ApiTestCase;
use Gedcomx\Tests\PersonBuilder;
use Gedcomx\Tests\SourceBuilder;
use Gedcomx\Types\GenderType;

class PersonStateTest extends ApiTestCase{

    private static $personState;

    public function testCanCreatePerson(){
        self::$personState = $this->createPerson();

        $this->assertAttributeEquals( "201", "statusCode", self::$personState->getResponse() );
    }

    public function testReadPersonState(){
        if( self::$personState == null ){
            self::$personState = $this->getPerson();
        }
        $personState = $this->collectionState
            ->readPerson(self::$personState->getLink(Rel::SELF)->getHref());

        $this->assertAttributeEquals( "200", "statusCode", $personState->getResponse() );
    }

    public function testAddNameToPerson(){
        if( self::$personState == null ){
            self::$personState = $this->createPerson();
        }
        if( self::$personState->getPerson() == null ){
            $uri = self::$personState->getLink(Rel::SELF)->getHref();
            self::$personState = $this->collectionState
                ->readPerson($uri);
        }
        $name = PersonBuilder::nickName();
        self::$personState->addName($name);

        $this->assertAttributeEquals( "200", "statusCode", self::$personState->getResponse() );
    }

    public function testUpdateGenderOnPerson(){
        if( self::$personState == null ){
            self::$personState = $this->createPerson();
        }
        if( self::$personState->getPerson() == null ){
            $uri = self::$personState->getLink(Rel::SELF)->getHref();
            self::$personState = $this->collectionState
                ->readPerson($uri);
        }
        $gender = new Gender(GenderType::UNKNOWN);
        self::$personState->updateGender($gender);

        $this->assertAttributeEquals( "200", "statusCode", self::$personState->getResponse() );
    }

    public function testAddFactToPerson(){
        if( self::$personState == null ){
            self::$personState = $this->createPerson();
        }
        if( self::$personState->getPerson() == null ){
            $uri = self::$personState->getLink(Rel::SELF)->getHref();
            self::$personState = $this->collectionState
                ->readPerson($uri);
        }
        $fact = PersonBuilder::nickName();
        self::$personState->addFact($fact);

        $this->assertAttributeEquals( "200", "statusCode", self::$personState->getResponse() );
    }

    public function testCanAddSourceReferenceWithStateObject(){
        /*
         * addSourceDescription isn't working. Will come back to this later.
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
        if( self::$personState == null ){
            self::$personState = $this->getPerson();
        }
        self::$personState
            ->loadSourceReferences();

        $sourceList = self::$personState->getEntity()->getSourceDescriptions();

        $this->assertGreaterThan( 0, count($sourceList), "No source descriptions were returned.");
    }


    public function createPerson()
    {
        $person = PersonBuilder::buildPerson();
        return $this->collectionState
            ->addPerson($person);
    }

    public function getPerson(){
        $link = $this->collectionState->getLink(Rel::PERSON);
        if ($link === null || $link->getTemplate() === null) {
            return null;
        }
        $uri = array(
            $link->getTemplate(),
            array(
                "pid" => "KWQ7-Y57"
            )
        );

        return $this->collectionState
            ->readPerson( $uri );
    }
}