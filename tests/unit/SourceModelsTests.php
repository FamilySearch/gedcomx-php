<?php

namespace Gedcomx\Tests\Unit;

use Gedcomx\Source\SourceDescription;
use Gedcomx\Source\SourceCitation;
use Gedcomx\Source\SourceReference;
use Gedcomx\Source\CitationField;
use Gedcomx\Tests\ApiTestCase;
use Gedcomx\Common\ResourceReference;

/**
 * Tests for GEDCOM X source models
 */
class SourceModelsTests extends ApiTestCase
{
    public function testSourceDescriptionConstruction()
    {
        $source = new SourceDescription();
        $source->setId('S-1');
        $source->setResourceType('http://gedcomx.org/Collection');

        $this->assertEquals('S-1', $source->getId());
        $this->assertEquals('http://gedcomx.org/Collection', $source->getResourceType());
    }

    public function testSourceDescriptionWithCitations()
    {
        $source = new SourceDescription([
            'id' => 'S-1',
            'citations' => [
                [
                    'value' => '"United States Census, 1940," database with images...'
                ]
            ]
        ]);

        $this->assertEquals('S-1', $source->getId());
        $this->assertCount(1, $source->getCitations());
    }

    public function testSourceCitationConstruction()
    {
        $citation = new SourceCitation();
        $citation->setValue('FamilySearch Family Tree...');

        $this->assertEquals('FamilySearch Family Tree...', $citation->getValue());
    }

    public function testSourceCitationWithFields()
    {
        $field = new CitationField();
        $field->setName('Author');
        $field->setValue('John Doe');

        $citation = new SourceCitation();
        $citation->setFields([$field]);

        $this->assertCount(1, $citation->getFields());
        $this->assertEquals('Author', $citation->getFields()[0]->getName());
    }

    public function testSourceReferenceConstruction()
    {
        $sourceRef = new SourceReference();
        $sourceRef->setDescriptionRef('#S-1');

        $this->assertEquals('#S-1', $sourceRef->getDescriptionRef());
    }

    public function testSourceReferenceWithAttribution()
    {
        $sourceRef = new SourceReference();
        $sourceRef->setDescriptionRef('#S-1');

        $this->assertEquals('#S-1', $sourceRef->getDescriptionRef());
    }

    public function testSourceDescriptionJsonRoundTrip()
    {
        $source = new SourceDescription([
            'id' => 'S-TEST',
            'resourceType' => 'http://gedcomx.org/PhysicalArtifact',
            'citations' => [
                ['value' => 'Test citation']
            ]
        ]);

        $json = $source->toJson();
        $this->assertStringContainsString('S-TEST', $json);
        $this->assertStringContainsString('Test citation', $json);

        $decoded = json_decode($json, true);
        $source2 = new SourceDescription($decoded);
        $this->assertEquals('S-TEST', $source2->getId());
    }
}
