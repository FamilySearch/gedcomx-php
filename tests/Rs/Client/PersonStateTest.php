<?php


namespace Gedcomx\Tests\Rs\Client;


use Gedcomx\Tests\ApiTestCase;
use Gedcomx\Tests\PersonBuilder;

class PersonStateTest extends ApiTestCase{

    private $pid = "KWQK-4ZW";

    public function testReadPersonState(){
        $personState = $this->collectionState
            ->readPerson($this->pid);

        $this->assertNotNull($personState);
    }

    public function testCanCreatePerson(){
        $person = PersonBuilder::createPerson();
        $personState = $this->collectionState
            ->addPerson( $person );

        $this->assertAttributeEquals( "201", "statusCode", $personState->getResponse() );
    }
}