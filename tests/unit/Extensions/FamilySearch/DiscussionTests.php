<?php

namespace Gedcomx\Tests\Unit\Extensions\FamilySearch;

use Gedcomx\Tests\ApiTestCase;
use Gedcomx\Extensions\FamilySearch\Platform\Discussions\Discussion;

class DiscussionTests extends ApiTestCase
{
    public function testDiscussionDeserialization()
    {
        $discussion = new Discussion($this->loadJson('discussion.json'));

        $this->assertEquals('DIS-1', $discussion->getId());
        $this->assertEquals('Discussion about John Smith', $discussion->getTitle());
        $this->assertEquals('Need help identifying John Smith born 1850.', $discussion->getDetails());
        $this->assertEquals(1577836800000, $discussion->getCreated());
        $this->assertEquals(3, $discussion->getNumberOfComments());
    }

    public function testDiscussionGettersAndSetters()
    {
        $discussion = new Discussion();
        $discussion->setId('DIS-2');
        $discussion->setTitle('Question about source');
        $discussion->setDetails('Can anyone verify this source?');
        $discussion->setNumberOfComments(5);

        $this->assertEquals('DIS-2', $discussion->getId());
        $this->assertEquals('Question about source', $discussion->getTitle());
        $this->assertEquals('Can anyone verify this source?', $discussion->getDetails());
        $this->assertEquals(5, $discussion->getNumberOfComments());
    }

    public function testDiscussionWithoutComments()
    {
        $discussion = new Discussion();
        $discussion->setId('DIS-3');
        $discussion->setTitle('New Discussion');

        $this->assertNull($discussion->getNumberOfComments());
    }
}
