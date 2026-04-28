<?php

namespace Gedcomx\Tests\Unit\Conclusion;

use Gedcomx\Tests\ApiTestCase;
use Gedcomx\Conclusion\Fact;

class FactTests extends ApiTestCase
{
    public function testFactDeserialization()
    {
        $fact = new Fact($this->loadJson('fact.json'));

        $this->assertEquals('http://gedcomx.org/Birth', $fact->getType());
        $this->assertEquals('Birth event details', $fact->getValue());

        $date = $fact->getDate();
        $this->assertNotNull($date);
        $this->assertEquals('1 January 1900', $date->getOriginal());
        $this->assertEquals('+1900-01-01', $date->getFormal());

        $place = $fact->getPlace();
        $this->assertNotNull($place);
        $this->assertEquals('New York, New York, United States', $place->getOriginal());
    }

    public function testFactGettersAndSetters()
    {
        $fact = new Fact();
        $fact->setType('http://gedcomx.org/Death');
        $fact->setValue('Death occurred in hospital');

        $this->assertEquals('http://gedcomx.org/Death', $fact->getType());
        $this->assertEquals('Death occurred in hospital', $fact->getValue());
    }

    public function testFactWithoutDate()
    {
        $fact = new Fact();
        $fact->setType('http://gedcomx.org/Residence');

        $this->assertNull($fact->getDate());
    }
}
