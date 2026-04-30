<?php

namespace Gedcomx\Tests\Unit\Extensions\FamilySearch;

use Gedcomx\Tests\ApiTestCase;
use Gedcomx\Extensions\FamilySearch\Platform\Discussions\Comment;

class CommentTests extends ApiTestCase
{
    public function testCommentDeserialization()
    {
        $comment = new Comment($this->loadJson('comment.json'));

        $this->assertEquals('C-1', $comment->getId());
        $this->assertEquals('I found a possible match in the 1870 census.', $comment->getText());
        $this->assertEquals(1577836800000, $comment->getCreated());

        $contributor = $comment->getContributor();
        $this->assertNotNull($contributor);
        $this->assertEquals('#contributor1', $contributor->getResource());
    }

    public function testCommentGettersAndSetters()
    {
        $comment = new Comment();
        $comment->setId('C-2');
        $comment->setText('Great find! This matches my research.');

        $this->assertEquals('C-2', $comment->getId());
        $this->assertEquals('Great find! This matches my research.', $comment->getText());
    }

    public function testCommentWithoutContributor()
    {
        $comment = new Comment();
        $comment->setId('C-3');
        $comment->setText('Anonymous comment');

        $this->assertNull($comment->getContributor());
    }
}
