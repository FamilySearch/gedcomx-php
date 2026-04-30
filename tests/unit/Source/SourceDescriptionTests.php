<?php

namespace Gedcomx\Tests\Unit\Source;

use Gedcomx\Tests\ApiTestCase;
use Gedcomx\Source\SourceDescription;

class SourceDescriptionTests extends ApiTestCase
{
    public function testSourceDescriptionDeserialization()
    {
        $source = new SourceDescription($this->loadJson('source-description.json'));

        $this->assertEquals('S-1', $source->getId());
        $this->assertEquals('http://gedcomx.org/Collection', $source->getResourceType());

        $citations = $source->getCitations();
        $this->assertCount(1, $citations);
        $this->assertStringContainsString('1900 United States Federal Census', $citations[0]->getValue());

        $titles = $source->getTitles();
        $this->assertCount(1, $titles);
        $this->assertEquals('1900 United States Federal Census', $titles[0]->getValue());

        $this->assertEquals(1577836800000, $source->getCreated());
    }

    public function testSourceDescriptionGettersAndSetters()
    {
        $source = new SourceDescription();
        $source->setId('S-2');
        $source->setResourceType('http://gedcomx.org/DigitalArtifact');

        $this->assertEquals('S-2', $source->getId());
        $this->assertEquals('http://gedcomx.org/DigitalArtifact', $source->getResourceType());
    }

    public function testSourceDescriptionWithoutCitations()
    {
        $source = new SourceDescription();
        $source->setId('S-3');

        $this->assertNull($source->getCitations());
    }
}
