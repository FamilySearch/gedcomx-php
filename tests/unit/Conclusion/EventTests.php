<?php

namespace Gedcomx\Tests\Unit\Conclusion;

use Gedcomx\Tests\ApiTestCase;
use Gedcomx\Conclusion\Event;

class EventTests extends ApiTestCase
{
    public function testEventDeserialization()
    {
        $event = new Event($this->loadJson('event.json'));

        $this->assertEquals('http://gedcomx.org/Marriage', $event->getType());

        $date = $event->getDate();
        $this->assertNotNull($date);
        $this->assertEquals('15 June 1925', $date->getOriginal());
        $this->assertEquals('+1925-06-15', $date->getFormal());

        $place = $event->getPlace();
        $this->assertNotNull($place);
        $this->assertEquals('Boston, Massachusetts, United States', $place->getOriginal());

        $roles = $event->getRoles();
        $this->assertCount(2, $roles);
        $this->assertEquals('http://gedcomx.org/Principal', $roles[0]->getType());
    }

    public function testEventGettersAndSetters()
    {
        $event = new Event();
        $event->setType('http://gedcomx.org/Birth');

        $this->assertEquals('http://gedcomx.org/Birth', $event->getType());
    }

    public function testEventWithoutRoles()
    {
        $event = new Event();
        $event->setType('http://gedcomx.org/Baptism');

        $this->assertNull($event->getRoles());
    }
}
