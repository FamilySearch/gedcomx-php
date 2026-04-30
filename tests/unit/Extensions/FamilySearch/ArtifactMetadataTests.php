<?php

namespace Gedcomx\Tests\Unit\Extensions\FamilySearch;

use Gedcomx\Extensions\FamilySearch\Platform\Artifacts\ArtifactMetadata;
use Gedcomx\Tests\ApiTestCase;

class ArtifactMetadataTests extends ApiTestCase
{
    public function testArtifactMetadataConstruction()
    {
        $metadata = new ArtifactMetadata();

        $this->assertInstanceOf(ArtifactMetadata::class, $metadata);
    }

    public function testArtifactMetadataGettersAndSetters()
    {
        $metadata = new ArtifactMetadata();
        $metadata->setFilename('test-document.pdf');

        $this->assertEquals('test-document.pdf', $metadata->getFilename());
    }

    public function testArtifactMetadataWithEmptyData()
    {
        $metadata = new ArtifactMetadata([]);

        $this->assertInstanceOf(ArtifactMetadata::class, $metadata);
    }
}
