<?php

namespace Gedcomx\Tests\Unit\Conclusion;

use Gedcomx\Conclusion\EventRole;
use Gedcomx\Tests\ApiTestCase;

class EventRoleTests extends ApiTestCase
{
    public function testEventRoleDeserialization()
    {
        $json = $this->loadJson('event-role.json');
        $eventRole = new EventRole($json);

        $this->assertEquals('http://gedcomx.org/Witness', $eventRole->getType());
        $this->assertNotNull($eventRole->getPerson());
        $this->assertEquals('Witnessed the marriage ceremony', $eventRole->getDetails());
    }

    public function testEventRoleGettersAndSetters()
    {
        $eventRole = new EventRole();
        $eventRole->setType('http://gedcomx.org/Principal');
        $eventRole->setDetails('Groom in the marriage');

        $this->assertEquals('http://gedcomx.org/Principal', $eventRole->getType());
        $this->assertEquals('Groom in the marriage', $eventRole->getDetails());
    }

    public function testEventRoleWithoutDetails()
    {
        $eventRole = new EventRole([
            'type' => 'http://gedcomx.org/Official'
        ]);

        $this->assertEquals('http://gedcomx.org/Official', $eventRole->getType());
        $this->assertNull($eventRole->getDetails());
    }
}
