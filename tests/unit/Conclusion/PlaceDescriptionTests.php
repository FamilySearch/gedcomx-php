<?php

namespace Gedcomx\Tests\Unit\Conclusion;

use Gedcomx\Tests\ApiTestCase;
use Gedcomx\Conclusion\PlaceDescription;

class PlaceDescriptionTests extends ApiTestCase
{
    public function testPlaceDescriptionDeserialization()
    {
        $place = new PlaceDescription($this->loadJson('place-description.json'));

        $this->assertEquals('P-1', $place->getId());
        $this->assertEquals('http://gedcomx.org/City', $place->getType());
        $this->assertEquals(40.7128, $place->getLatitude());
        $this->assertEquals(-74.0060, $place->getLongitude());

        $names = $place->getNames();
        $this->assertCount(1, $names);
        $this->assertEquals('New York, New York, United States', $names[0]->getValue());
    }

    public function testPlaceDescriptionGettersAndSetters()
    {
        $place = new PlaceDescription();
        $place->setId('P-2');
        $place->setType('http://gedcomx.org/State');
        $place->setLatitude(42.3601);
        $place->setLongitude(-71.0589);

        $this->assertEquals('P-2', $place->getId());
        $this->assertEquals('http://gedcomx.org/State', $place->getType());
        $this->assertEquals(42.3601, $place->getLatitude());
        $this->assertEquals(-71.0589, $place->getLongitude());
    }

    public function testPlaceDescriptionWithoutCoordinates()
    {
        $place = new PlaceDescription();
        $place->setType('http://gedcomx.org/Country');

        $this->assertNull($place->getLatitude());
        $this->assertNull($place->getLongitude());
    }
}
