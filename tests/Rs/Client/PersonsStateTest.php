<?php

namespace Gedcomx\tests\Rs\Client;

use Gedcomx\Tests\ApiTestCase;
use Gedcomx\Tests\PersonBuilder;

class PersonsStateTest extends ApiTestCase {

    public function testCanAddPerson()
    {
        $person = PersonBuilder::buildPerson();
        $personState = $this->collectionState
            ->addPerson( $person );

        $this->assertAttributeEquals( "201", "statusCode", $personState->getResponse() );
    }
} 