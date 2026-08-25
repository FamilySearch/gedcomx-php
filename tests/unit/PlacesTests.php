<?php

namespace Gedcomx\Tests\Unit;

use Gedcomx\Common\ResourceReference;
use Gedcomx\Extensions\FamilySearch\Platform\Places\FamilySearchPlaceType;
use Gedcomx\Extensions\FamilySearch\Platform\Places\FeedbackInfo;
use Gedcomx\Extensions\FamilySearch\Platform\Places\PlaceDescriptionInfo;
use Gedcomx\Extensions\FamilySearch\Platform\Places\PlaceAttribute;
use Gedcomx\Tests\ApiTestCase;

class PlacesTest extends ApiTestCase
{
    public function testFamilySearchPlaceTypeEnum()
    {
        $this->assertEquals('http://familysearch.org/v1/Place', FamilySearchPlaceType::PLACE);
        $this->assertEquals('http://familysearch.org/v1/PlaceGroup', FamilySearchPlaceType::PLACE_GROUP);
        $this->assertEquals('http://familysearch.org/v1/OTHER', FamilySearchPlaceType::OTHER);
    }

    public function testFeedbackInfoConstruction()
    {
        $feedback = new FeedbackInfo();
        $feedback->setResolution('http://example.com/resolution');
        $feedback->setStatus('http://example.com/status');
        $feedback->setDetails('Test feedback details');

        $place = new ResourceReference();
        $place->setResource('https://familysearch.org/platform/places/12345');
        $feedback->setPlace($place);

        $this->assertEquals('http://example.com/resolution', $feedback->getResolution());
        $this->assertEquals('http://example.com/status', $feedback->getStatus());
        $this->assertEquals('Test feedback details', $feedback->getDetails());
        $this->assertNotNull($feedback->getPlace());
    }

    public function testFeedbackInfoJsonRoundTrip()
    {
        $feedback = new FeedbackInfo([
            'resolution' => 'http://example.com/resolution',
            'status' => 'http://example.com/pending',
            'details' => 'Location is incorrect'
        ]);

        $json = $feedback->toJson();
        $this->assertStringContainsString('Location is incorrect', $json);

        $decoded = json_decode($json, true);
        $feedback2 = new FeedbackInfo($decoded);

        $this->assertEquals('Location is incorrect', $feedback2->getDetails());
        $this->assertEquals('http://example.com/pending', $feedback2->getStatus());
    }

    public function testPlaceDescriptionInfoConstruction()
    {
        $info = new PlaceDescriptionInfo();
        $info->setZoomLevel(10);
        $info->setRelatedType('http://example.com/related');
        $info->setRelatedSubType('http://example.com/subtype');

        $this->assertEquals(10, $info->getZoomLevel());
        $this->assertEquals('http://example.com/related', $info->getRelatedType());
        $this->assertEquals('http://example.com/subtype', $info->getRelatedSubType());
    }

    public function testPlaceDescriptionInfoJsonRoundTrip()
    {
        $info = new PlaceDescriptionInfo([
            'zoomLevel' => 15,
            'relatedType' => 'http://gedcomx.org/PlaceType',
            'relatedSubType' => 'http://gedcomx.org/SubType'
        ]);

        $json = $info->toJson();
        $decoded = json_decode($json, true);
        $info2 = new PlaceDescriptionInfo($decoded);

        $this->assertEquals(15, $info2->getZoomLevel());
        $this->assertEquals('http://gedcomx.org/PlaceType', $info2->getRelatedType());
    }

    public function testPlaceAttributeConstruction()
    {
        $attr = new PlaceAttribute();
        $attr->setTypeName('population');
        $attr->setValue('50000');
        $attr->setYear(2020);

        $this->assertEquals('population', $attr->getTypeName());
        $this->assertEquals('50000', $attr->getValue());
        $this->assertEquals(2020, $attr->getYear());
    }

    public function testPlaceAttributeGettersSetters()
    {
        $attr = new PlaceAttribute();

        $attr->setTypeName('test');
        $attr->setValue('value');
        $attr->setYear(2024);
        $attr->setLocale('en-US');

        $this->assertEquals('test', $attr->getTypeName());
        $this->assertEquals('value', $attr->getValue());
        $this->assertEquals(2024, $attr->getYear());
        $this->assertEquals('en-US', $attr->getLocale());
    }

    public function testPlaceAttributeWithNullValues()
    {
        $attr = new PlaceAttribute();

        $this->assertNull($attr->getTypeName());
        $this->assertNull($attr->getValue());
        $this->assertNull($attr->getYear());
        $this->assertNull($attr->getLocale());
    }
}
