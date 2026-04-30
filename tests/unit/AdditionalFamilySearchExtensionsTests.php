<?php

namespace Gedcomx\Tests\Unit;

use Gedcomx\Extensions\FamilySearch\Platform\Discussions\Discussion;
use Gedcomx\Extensions\FamilySearch\Platform\Discussions\Comment;
use Gedcomx\Extensions\FamilySearch\Platform\Tree\DiscussionReference;
use Gedcomx\Extensions\FamilySearch\Platform\Users\User;
use Gedcomx\Tests\ApiTestCase;

/**
 * Tests for additional FamilySearch extension models
 */
class AdditionalFamilySearchExtensionsTests extends ApiTestCase
{
    public function testDiscussionConstruction()
    {
        $discussion = new Discussion();
        $discussion->setId('DISC-1');
        $discussion->setTitle('Question about birth date');

        $this->assertEquals('DISC-1', $discussion->getId());
        $this->assertEquals('Question about birth date', $discussion->getTitle());
    }

    public function testDiscussionFromArray()
    {
        $discussion = new Discussion([
            'id' => 'DISC-1',
            'title' => 'Research question',
            'details' => 'Need help finding birth record'
        ]);

        $this->assertEquals('DISC-1', $discussion->getId());
        $this->assertEquals('Research question', $discussion->getTitle());
        $this->assertEquals('Need help finding birth record', $discussion->getDetails());
    }

    public function testCommentConstruction()
    {
        $comment = new Comment();
        $comment->setId('COMM-1');
        $comment->setText('This looks correct based on the census record.');

        $this->assertEquals('COMM-1', $comment->getId());
        $this->assertEquals('This looks correct based on the census record.', $comment->getText());
    }

    public function testCommentFromArray()
    {
        $comment = new Comment([
            'id' => 'COMM-1',
            'text' => 'Great research!'
        ]);

        $this->assertEquals('COMM-1', $comment->getId());
        $this->assertEquals('Great research!', $comment->getText());
    }

    public function testDiscussionReferenceConstruction()
    {
        $discussionRef = new DiscussionReference();
        $discussionRef->setResource('https://familysearch.org/platform/discussions/12345');

        $this->assertEquals(
            'https://familysearch.org/platform/discussions/12345',
            $discussionRef->getResource()
        );
    }

    public function testDiscussionReferenceFromArray()
    {
        $discussionRef = new DiscussionReference([
            'resource' => '#DISC-1',
            'resourceId' => 'DISC-1'
        ]);

        $this->assertEquals('#DISC-1', $discussionRef->getResource());
    }

    public function testUserConstruction()
    {
        $user = new User();
        $user->setId('U-1');

        $this->assertEquals('U-1', $user->getId());
    }

    public function testUserFromArray()
    {
        $user = new User([
            'id' => 'U-1',
            'contactName' => 'John Smith'
        ]);

        $this->assertEquals('U-1', $user->getId());
        $this->assertEquals('John Smith', $user->getContactName());
    }

    public function testDiscussionJsonRoundTrip()
    {
        $discussion = new Discussion([
            'id' => 'DISC-TEST',
            'title' => 'Test Discussion'
        ]);

        $json = $discussion->toJson();
        $this->assertStringContainsString('DISC-TEST', $json);
        $this->assertStringContainsString('Test Discussion', $json);

        $decoded = json_decode($json, true);
        $discussion2 = new Discussion($decoded);
        $this->assertEquals('DISC-TEST', $discussion2->getId());
    }

    public function testCommentJsonRoundTrip()
    {
        $comment = new Comment([
            'id' => 'COMM-TEST',
            'text' => 'Test comment text'
        ]);

        $json = $comment->toJson();
        $this->assertStringContainsString('COMM-TEST', $json);

        $decoded = json_decode($json, true);
        $comment2 = new Comment($decoded);
        $this->assertEquals('COMM-TEST', $comment2->getId());
    }
}
