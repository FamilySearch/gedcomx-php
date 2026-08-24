<?php

namespace Gedcomx\Tests\Unit;

use Gedcomx\Extensions\FamilySearch\Platform\Search\Facet;
use Gedcomx\Tests\ApiTestCase;

class SearchTest extends ApiTestCase
{
    public function testFacetConstruction()
    {
        $facet = new Facet();
        $facet->setDisplayName('Birth Year');
        $facet->setDisplayCount('100');
        $facet->setParams('birthYear:1850');
        $facet->setCount(100);

        $this->assertEquals('Birth Year', $facet->getDisplayName());
        $this->assertEquals('100', $facet->getDisplayCount());
        $this->assertEquals('birthYear:1850', $facet->getParams());
        $this->assertEquals(100, $facet->getCount());
    }

    public function testFacetWithNestedFacets()
    {
        $parentFacet = new Facet();
        $parentFacet->setDisplayName('Birth Location');
        $parentFacet->setCount(500);

        $childFacet1 = new Facet();
        $childFacet1->setDisplayName('England');
        $childFacet1->setCount(250);

        $childFacet2 = new Facet();
        $childFacet2->setDisplayName('United States');
        $childFacet2->setCount(200);

        $parentFacet->setFacets([$childFacet1, $childFacet2]);

        $this->assertCount(2, $parentFacet->getFacets());
        $this->assertEquals('England', $parentFacet->getFacets()[0]->getDisplayName());
        $this->assertEquals(250, $parentFacet->getFacets()[0]->getCount());
    }

    public function testFacetRecursiveStructure()
    {
        // Test three levels deep
        $level1 = new Facet();
        $level1->setDisplayName('Location');

        $level2 = new Facet();
        $level2->setDisplayName('Europe');

        $level3 = new Facet();
        $level3->setDisplayName('United Kingdom');

        $level2->setFacets([$level3]);
        $level1->setFacets([$level2]);

        $this->assertCount(1, $level1->getFacets());
        $this->assertCount(1, $level1->getFacets()[0]->getFacets());
        $this->assertEquals('United Kingdom', $level1->getFacets()[0]->getFacets()[0]->getDisplayName());
    }

    public function testFacetsIteratorWithNullFacets()
    {
        $facet = new Facet();
        $iterator = $facet->facets();

        $this->assertInstanceOf(\ArrayIterator::class, $iterator);
        $this->assertCount(0, $iterator);
    }

    public function testFacetsIteratorWithFacets()
    {
        $facet = new Facet();

        $child1 = new Facet();
        $child1->setDisplayName('Child 1');

        $child2 = new Facet();
        $child2->setDisplayName('Child 2');

        $child3 = new Facet();
        $child3->setDisplayName('Child 3');

        $facet->setFacets([$child1, $child2, $child3]);

        $iterator = $facet->facets();
        $this->assertInstanceOf(\ArrayIterator::class, $iterator);
        $this->assertCount(3, $iterator);

        $names = [];
        foreach ($iterator as $f) {
            $names[] = $f->getDisplayName();
        }

        $this->assertContains('Child 1', $names);
        $this->assertContains('Child 2', $names);
        $this->assertContains('Child 3', $names);
    }

    public function testFacetJsonRoundTrip()
    {
        $facet = new Facet([
            'displayName' => 'Death Year',
            'displayCount' => '50',
            'params' => 'deathYear:1900',
            'count' => 50
        ]);

        $json = $facet->toJson();
        $this->assertStringContainsString('Death Year', $json);
        $this->assertStringContainsString('deathYear:1900', $json);

        $decoded = json_decode($json, true);
        $facet2 = new Facet($decoded);

        $this->assertEquals('Death Year', $facet2->getDisplayName());
        $this->assertEquals('50', $facet2->getDisplayCount());
        $this->assertEquals(50, $facet2->getCount());
    }

    public function testFacetJsonRoundTripWithNestedFacets()
    {
        $parent = new Facet([
            'displayName' => 'Parent',
            'count' => 100,
            'facets' => [
                [
                    'displayName' => 'Child 1',
                    'count' => 60
                ],
                [
                    'displayName' => 'Child 2',
                    'count' => 40
                ]
            ]
        ]);

        $json = $parent->toJson();
        $decoded = json_decode($json, true);
        $parent2 = new Facet($decoded);

        $this->assertEquals('Parent', $parent2->getDisplayName());
        $this->assertCount(2, $parent2->getFacets());
        $this->assertEquals('Child 1', $parent2->getFacets()[0]->getDisplayName());
        $this->assertEquals(60, $parent2->getFacets()[0]->getCount());
    }

    public function testFacetCountIsInteger()
    {
        $facet = new Facet([
            'count' => 42
        ]);

        $this->assertIsInt($facet->getCount());
        $this->assertEquals(42, $facet->getCount());
    }

    public function testFacetWithAllProperties()
    {
        $facet = new Facet();
        $facet->setDisplayName('Test Facet');
        $facet->setDisplayCount('123');
        $facet->setParams('test:params');
        $facet->setCount(123);

        $child = new Facet();
        $child->setDisplayName('Child Facet');
        $facet->setFacets([$child]);

        $array = $facet->toArray();

        $this->assertArrayHasKey('displayName', $array);
        $this->assertArrayHasKey('displayCount', $array);
        $this->assertArrayHasKey('params', $array);
        $this->assertArrayHasKey('count', $array);
        $this->assertArrayHasKey('facets', $array);
        $this->assertCount(1, $array['facets']);
    }
}
