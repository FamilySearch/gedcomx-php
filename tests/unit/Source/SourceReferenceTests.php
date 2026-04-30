<?php

namespace Gedcomx\Tests\Unit\Source;

use Gedcomx\Tests\ApiTestCase;
use Gedcomx\Source\SourceReference;

class SourceReferenceTests extends ApiTestCase
{
    public function testSourceReferenceGettersAndSetters()
    {
        $sourceRef = new SourceReference();
        $sourceRef->setDescriptionRef('#source1');

        $this->assertEquals('#source1', $sourceRef->getDescriptionRef());
    }

    public function testSourceReferenceWithoutDescription()
    {
        $sourceRef = new SourceReference();

        $this->assertNull($sourceRef->getDescriptionRef());
    }

    public function testSourceReferenceWithAttribution()
    {
        $sourceRef = new SourceReference();
        $sourceRef->setDescriptionRef('#source2');

        $this->assertEquals('#source2', $sourceRef->getDescriptionRef());
        $this->assertNull($sourceRef->getAttribution());
    }
}
