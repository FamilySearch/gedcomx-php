<?php

namespace Gedcomx\Tests\Unit\Conclusion;

use Gedcomx\Conclusion\PlaceReference;
use Gedcomx\Tests\ApiTestCase;

class PlaceReferenceTests extends ApiTestCase
{
    public function testPlaceReferenceDeserialization()
    {
        $json = $this->loadJson('place-reference.json');
        $placeRef = new PlaceReference($json);

        $this->assertEquals('New York, New York, United States', $placeRef->getOriginal());
        $this->assertEquals('#place-123', $placeRef->getDescriptionRef());
        $this->assertCount(1, $placeRef->getNormalizedExtensions());
    }

    public function testPlaceReferenceGettersAndSetters()
    {
        $placeRef = new PlaceReference();
        $placeRef->setOriginal('London, England');
        $placeRef->setDescriptionRef('#place-456');

        $this->assertEquals('London, England', $placeRef->getOriginal());
        $this->assertEquals('#place-456', $placeRef->getDescriptionRef());
    }

    public function testPlaceReferenceWithoutNormalized()
    {
        $placeRef = new PlaceReference([
            'original' => 'Paris, France'
        ]);

        $this->assertEquals('Paris, France', $placeRef->getOriginal());
        $this->assertEmpty($placeRef->getNormalizedExtensions());
    }
}
