<?php

namespace Gedcomx\tests\Rs\Client;

use Gedcomx\Rs\Client\StateFactory;
use Gedcomx\Tests\ApiTestCase;
use Gedcomx\Tests\PersonBuilder;

class PersonsStateTest extends ApiTestCase {

    public function testCanAddPerson()
    {
        $factory = new StateFactory();
        $this->collectionState($factory);

        $person = PersonBuilder::buildPerson($this->faker);
        $personState = $this->collectionState()->addPerson( $person );

        $this->assertAttributeEquals( "201", "statusCode", $personState->getResponse() );
    }
} 