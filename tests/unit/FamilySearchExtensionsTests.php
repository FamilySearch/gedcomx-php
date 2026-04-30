<?php

namespace Gedcomx\Tests\Unit;

use Gedcomx\Extensions\FamilySearch\Platform\Tree\ChildAndParentsRelationship;
use Gedcomx\Extensions\FamilySearch\FamilySearchPlatform;
use Gedcomx\Tests\ApiTestCase;
use Gedcomx\Common\ResourceReference;

/**
 * Comprehensive tests for FamilySearch extension models
 * Tests construction, getters/setters, and serialization/deserialization
 */
class FamilySearchExtensionsTests extends ApiTestCase
{
    public function testChildAndParentsRelationshipConstruction()
    {
        $relationship = new ChildAndParentsRelationship();
        $relationship->setId('R-1');

        $father = new ResourceReference();
        $father->setResource('#P-FATHER');
        $relationship->setFather($father);

        $mother = new ResourceReference();
        $mother->setResource('#P-MOTHER');
        $relationship->setMother($mother);

        $child = new ResourceReference();
        $child->setResource('#P-CHILD');
        $relationship->setChild($child);

        $this->assertEquals('R-1', $relationship->getId());
        $this->assertEquals('#P-FATHER', $relationship->getFather()->getResource());
        $this->assertEquals('#P-MOTHER', $relationship->getMother()->getResource());
        $this->assertEquals('#P-CHILD', $relationship->getChild()->getResource());
    }

    public function testChildAndParentsRelationshipFromArray()
    {
        $relationship = new ChildAndParentsRelationship([
            'id' => 'CAPR-1',
            'father' => [
                'resource' => 'https://familysearch.org/platform/tree/persons/P-F',
                'resourceId' => 'P-F'
            ],
            'mother' => [
                'resource' => 'https://familysearch.org/platform/tree/persons/P-M',
                'resourceId' => 'P-M'
            ],
            'child' => [
                'resource' => 'https://familysearch.org/platform/tree/persons/P-C',
                'resourceId' => 'P-C'
            ]
        ]);

        $this->assertEquals('CAPR-1', $relationship->getId());
        $this->assertNotNull($relationship->getFather());
        $this->assertNotNull($relationship->getMother());
        $this->assertNotNull($relationship->getChild());
    }

    public function testFamilySearchPlatformConstruction()
    {
        $platform = new FamilySearchPlatform();

        $capr = new ChildAndParentsRelationship([
            'id' => 'CAPR-1',
            'child' => ['resource' => '#P-C']
        ]);

        $platform->setChildAndParentsRelationships([$capr]);

        $this->assertCount(1, $platform->getChildAndParentsRelationships());
        $this->assertEquals('CAPR-1', $platform->getChildAndParentsRelationships()[0]->getId());
    }

    public function testChildAndParentsRelationshipJsonRoundTrip()
    {
        $relationship = new ChildAndParentsRelationship([
            'id' => 'CAPR-JSON',
            'father' => ['resource' => '#P-F'],
            'mother' => ['resource' => '#P-M'],
            'child' => ['resource' => '#P-C']
        ]);

        $json = $relationship->toJson();
        $this->assertStringContainsString('CAPR-JSON', $json);
        $this->assertStringContainsString('#P-F', $json);

        // Test round-trip
        $decoded = json_decode($json, true);
        $relationship2 = new ChildAndParentsRelationship($decoded);
        $this->assertEquals('CAPR-JSON', $relationship2->getId());
    }

    public function testFamilySearchPlatformJsonRoundTrip()
    {
        $platform = new FamilySearchPlatform();

        $capr = new ChildAndParentsRelationship([
            'id' => 'CAPR-1',
            'child' => ['resource' => '#P-CHILD']
        ]);

        $platform->setChildAndParentsRelationships([$capr]);

        $json = $platform->toJson();
        $this->assertStringContainsString('CAPR-1', $json);

        // Test round-trip
        $decoded = json_decode($json, true);
        $platform2 = new FamilySearchPlatform($decoded);
        $this->assertCount(1, $platform2->getChildAndParentsRelationships());
    }

    public function testChildAndParentsRelationshipResourceReferences()
    {
        $relationship = new ChildAndParentsRelationship();

        $father = new ResourceReference(['resource' => 'https://test.com/P-F', 'resourceId' => 'P-F']);
        $mother = new ResourceReference(['resource' => 'https://test.com/P-M', 'resourceId' => 'P-M']);
        $child = new ResourceReference(['resource' => 'https://test.com/P-C', 'resourceId' => 'P-C']);

        $relationship->setFather($father);
        $relationship->setMother($mother);
        $relationship->setChild($child);

        $this->assertEquals('https://test.com/P-F', $relationship->getFather()->getResource());
        $this->assertEquals('https://test.com/P-M', $relationship->getMother()->getResource());
        $this->assertEquals('https://test.com/P-C', $relationship->getChild()->getResource());
    }
}
