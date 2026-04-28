<?php

namespace Gedcomx\Tests\Unit\Extensions\FamilySearch;

use Gedcomx\Tests\ApiTestCase;
use Gedcomx\Extensions\FamilySearch\Platform\Tree\DiscussionReference;

class DiscussionReferenceTests extends ApiTestCase
{
    public function testDiscussionReferenceGettersAndSetters()
    {
        $ref = new DiscussionReference();
        $ref->setResource('https://familysearch.org/platform/discussions/12345');
        $ref->setResourceId('12345');

        $this->assertEquals('https://familysearch.org/platform/discussions/12345', $ref->getResource());
        $this->assertEquals('12345', $ref->getResourceId());
    }

    public function testDiscussionReferenceWithoutResourceId()
    {
        $ref = new DiscussionReference();
        $ref->setResource('https://familysearch.org/platform/discussions/67890');

        $this->assertEquals('https://familysearch.org/platform/discussions/67890', $ref->getResource());
        $this->assertNull($ref->getResourceId());
    }

    public function testDiscussionReferenceEmpty()
    {
        $ref = new DiscussionReference();

        $this->assertNull($ref->getResource());
    }
}
