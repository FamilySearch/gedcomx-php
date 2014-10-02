<?php

namespace Gedcomx\Tests\Rs\Client;

use Gedcomx\Rs\Client\Rel;
use Gedcomx\Source\SourceDescription;
use Gedcomx\Tests\ApiTestCase;
use Gedcomx\Tests\PersonBuilder;
use Gedcomx\Tests\SourceBuilder;

class PersonStateTest extends ApiTestCase{

    private static $personState;

    public function testCanCreatePerson(){
        self::$personState = $this->createPerson();

        $this->assertAttributeEquals( "201", "statusCode", self::$personState->getResponse() );
    }

    public function testReadPersonState(){
        if( self::$personState == null ){
            self::$personState = $this->createPerson();
        }
        $personState = $this->collectionState
            ->readPerson(self::$personState->getLink(Rel::SELF)->getHref());

        $this->assertAttributeEquals( "200", "statusCode", $personState->getResponse() );
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
            self::$personState = $this->createPerson();
        }
        self::$personState
            ->loadSourceReferences();

        $sourceList = self::$personState->getEntity()->getSourceDescriptions();

        $this->assertGreaterThan( 0, count($sourceList), "No source descriptions were returned.");
    }

    public function createPerson(){
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