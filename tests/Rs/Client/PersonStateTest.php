<?php


namespace Gedcomx\Tests\Rs\Client;


use Gedcomx\Tests\ApiTestCase;

class PersonStateTest extends ApiTestCase{

    public function testReadPersonState(){
        $personState = $this->collectionState
            ->readPerson('KWQK-4ZW');

        $this->assertNotNull($personState);
    }
} 