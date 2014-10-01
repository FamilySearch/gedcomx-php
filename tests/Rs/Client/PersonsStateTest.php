<?php

namespace Gedcomx\tests\Rs\Client;

use Gedcomx\Tests\ApiTestCase;
use Gedcomx\Tests\PersonBuilder;

class PersonsStateTest extends ApiTestCase {

    public function testCanAddPerson()
    {
        $person = PersonBuilder::createPerson();
        $this->collectionState
            ->addPerson( $person );
    }
} 