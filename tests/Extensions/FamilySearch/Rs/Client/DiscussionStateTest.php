<?php

namespace Gedcomx\Tests\Extensions\FamilySearch\Rs\Client;

use Gedcomx\Extensions\FamilySearch\Platform\Discussions\Comment;
use Gedcomx\Common\Attribution;
use Gedcomx\Common\Note;
use Gedcomx\Common\ResourceReference;
use Gedcomx\Common\TextValue;
use Gedcomx\Extensions\FamilySearch\Platform\Discussions\Discussion;
use Gedcomx\Extensions\FamilySearch\Rs\Client\DiscussionState;
use Gedcomx\Extensions\FamilySearch\Rs\Client\FamilySearchSourceDescriptionState;
use Gedcomx\Extensions\FamilySearch\Rs\Client\FamilySearchStateFactory;
use Gedcomx\Extensions\FamilySearch\Rs\Client\Memories\FamilySearchMemories;
use Gedcomx\Rs\Client\Util\DataSource;
use Gedcomx\Extensions\FamilySearch\Rs\Client\FamilyTree\FamilyTreeStateFactory;
use Gedcomx\Rs\Client\SourceDescriptionState;
use Gedcomx\Rs\Client\Util\HttpStatus;
use Gedcomx\Source\SourceCitation;
use Gedcomx\Source\SourceDescription;
use Gedcomx\Tests\ApiTestCase;
use Gedcomx\Tests\DiscussionBuilder;

class DiscussionStateTest extends ApiTestCase
{

    /**
     * @link https://familysearch.org/developers/docs/api/discussions/Create_Discussion_usecase
     */
    public function testCreateDiscussion()
    {
        $factory = new FamilySearchStateFactory();
        $this->collectionState($factory);


        $userState = $this->collectionState()->readCurrentUser();
        $discussion = DiscussionBuilder::createDiscussion($userState->getUser()->getTreeUserId());

        $newState = $this->collectionState()->addDiscussion($discussion);
        $this->assertAttributeEquals(HttpStatus::CREATED, "statusCode", $newState->getResponse(), $this->buildFailMessage(__METHOD__, $newState));

        $newState->delete();
    }

    /**
     * @link https://familysearch.org/developers/docs/api/discussions/Read_Discussion_usecase
     */
    public function testReadDiscussion()
    {
        $factory = new FamilySearchStateFactory();
        $this->collectionState($factory);

        $userState = $this->collectionState()->readCurrentUser();
        $discussion = DiscussionBuilder::createDiscussion($userState->getUser()->getTreeUserId());

        $newState = $this->collectionState()->addDiscussion($discussion);
        $newState = $newState->get();
        $this->assertAttributeEquals(HttpStatus::OK, "statusCode", $newState->getResponse(), $this->buildFailMessage(__METHOD__, $newState));

        $this->assertNotEmpty($newState->getDiscussion());

        $newState->delete();
    }

    /**
     * @link https://familysearch.org/developers/docs/api/discussions/Update_Discussion_usecase
     */
    public function testUpdateDiscussion()
    {
        $factory = new FamilySearchStateFactory();
        $this->collectionState($factory);

        $userState = $this->collectionState()->readCurrentUser();
        $discussion = DiscussionBuilder::createDiscussion($userState->getUser()->getTreeUserId());

        $newState = $this->collectionState()->addDiscussion($discussion);
        $newState = $newState->get();
        /** @var Discussion $discussion */
        $discussion = $newState->getDiscussion();
        $discussion->setDetails($this->faker->paragraph());

        $updated = $newState->updateDiscussion($discussion);
        $this->assertAttributeEquals(HttpStatus::NO_CONTENT, "statusCode", $updated->getResponse(), $this->buildFailMessage(__METHOD__, $updated));

        $updated->delete();
    }

    /**
     * @link https://familysearch.org/developers/docs/api/discussions/Delete_Discussion_usecase
     */
    public function testDeleteDiscussion()
    {
        $factory = new FamilySearchStateFactory();
        $this->collectionState($factory);

        $userState = $this->collectionState()->readCurrentUser();
        $discussion = DiscussionBuilder::createDiscussion($userState->getUser()->getTreeUserId());

        $newState = $this->collectionState()->addDiscussion($discussion);
        $newState = $newState->get();
        $this->assertAttributeEquals(HttpStatus::OK, "statusCode", $newState->getResponse(), $this->buildFailMessage(__METHOD__, $newState));

        $this->assertNotEmpty($newState->getDiscussion());

        $deleted = $newState->delete();
        $this->assertAttributeEquals(HttpStatus::NO_CONTENT, "statusCode", $deleted->getResponse(), $this->buildFailMessage(__METHOD__, $deleted));
    }

    /**
     * @link https://familysearch.org/developers/docs/api/discussions/Create_Comment_usecase
     */
    public function testCreateComment()
    {
        $factory = new FamilySearchStateFactory();
        $this->collectionState($factory);

        $userState = $this->collectionState()->readCurrentUser();
        $discussion = DiscussionBuilder::createDiscussion($userState->getUser()->getTreeUserId());

        $state = $this->collectionState()->addDiscussion($discussion);
        $state = $state->get();

        $comment = DiscussionBuilder::createComment($userState);
        $state = $state->addComment($comment);
        $this->assertAttributeEquals(HttpStatus::CREATED, "statusCode", $state->getResponse(), $this->buildFailMessage(__METHOD__, $state));

        $state->delete();
    }

    /**
     * @link https://familysearch.org/developers/docs/api/discussions/Read_Comments_usecase
     */
    public function testReadComments()
    {
        $factory = new FamilySearchStateFactory();
        $this->collectionState($factory);

        $userState = $this->collectionState()->readCurrentUser();
        $discussion = DiscussionBuilder::createDiscussion($userState->getUser()->getTreeUserId());
        $state = $this->collectionState()->addDiscussion($discussion);

        $state = $state->get();
        $comment = DiscussionBuilder::createComment($userState);
        $state->addComment($comment);
        $comment = DiscussionBuilder::createComment($userState);
        $state->addComment($comment);
        $state = $state->get();

        $state->loadComments();
        $comments = $state->getDiscussion()->getComments();
        $this->assertEquals(2, count($comments));

        $state->delete();
    }

    /**
     * @link https://familysearch.org/developers/docs/api/discussions/Update_Comment_usecase
     */
    public function testUpdateComment()
    {
        $factory = new FamilySearchStateFactory();
        $this->collectionState($factory);

        $userState = $this->collectionState()->readCurrentUser();
        $discussion = DiscussionBuilder::createDiscussion($userState->getUser()->getTreeUserId());
        /** @var DiscussionState $state */
        $state = $this->collectionState()->addDiscussion($discussion);

        $state = $state->get();
        $comment = DiscussionBuilder::createComment($userState);
        $state->addComment($comment);
        $comment = DiscussionBuilder::createComment($userState);
        $state->addComment($comment);
        $state = $state->get();

        $state->loadComments();
        $comments = $state->getDiscussion()->getComments();
        $comment = $comments[0];
        $newText = $this->faker->paragraph();
        $comment->setText($newText);

        $updated = $state->updateComment($comment);
        $this->assertAttributeEquals(HttpStatus::NO_CONTENT, "statusCode", $updated->getResponse(), $this->buildFailMessage(__METHOD__, $updated));

        $state->loadComments();
        $this->assertEquals(2, count($comments));

        $pass = false;
        $comments = $state->getDiscussion()->getComments();
        foreach ($comments as $c) {
            if ($c->getId() == $comment->getId() & $c->getText() == $newText) {
                $pass = true;
            }
        }
        $this->assertTrue($pass);

        $state->delete();
    }

    /**
     * @link https://familysearch.org/developers/docs/api/discussions/Delete_Comment_usecase
     */
    public function testDeleteComment()
    {
        $factory = new FamilySearchStateFactory();
        $this->collectionState($factory);

        $userState = $this->collectionState()->readCurrentUser();
        $discussion = DiscussionBuilder::createDiscussion($userState->getUser()->getTreeUserId());
        /** @var DiscussionState $state */
        $state = $this->collectionState()->addDiscussion($discussion);

        $state = $state->get();
        $comment = DiscussionBuilder::createComment($userState);
        $state->addComment($comment);
        $comment = DiscussionBuilder::createComment($userState);
        $state->addComment($comment);
        $state = $state->get();

        $state->loadComments();
        $comments = $state->getDiscussion()->getComments();
        $comment = $comments[0];

        $deleted = $state->deleteComment($comment);
        $this->assertAttributeEquals(HttpStatus::NO_CONTENT, "statusCode", $deleted->getResponse(), $this->buildFailMessage(__METHOD__, $deleted));

        $state = $state->get();
        $state->loadComments();
        $comments = $state->getDiscussion()->getComments();
        $this->assertEquals(1, count($comments));

        $state->delete();
    }

    public function testCreateMemoriesComment()
    {
        $factory = new FamilySearchStateFactory();
        /** @var FamilySearchMemories $memories */
        $memories = $factory->newMemoriesState()
            ->authenticateViaOAuth2Password(
                $this->apiCredentials->username,
                $this->apiCredentials->password,
                $this->apiCredentials->apiKey);
        $ds = new DataSource();
        $ds->setTitle("Sample Memory");
        $ds->setFile($this->makeTextFile());
        /** @var FamilySearchSourceDescriptionState $artifact */
        $artifact = $memories->addArtifact($ds)->get();
        $comments = $artifact->readComments();
        $comment = new Comment();
        $comment->setText("Test comment.");
        $state = $comments->addComment($comment);

        $this->assertNotNull($state->ifSuccessful());
        $this->assertEquals(HttpStatus::CREATED, $state->getResponse()->getStatusCode());

        $artifact->delete();
    }

    public function testReadMemoriesComments()
    {
        $factory = new FamilySearchStateFactory();
        /** @var FamilySearchMemories $memories */
        $memories = $factory->newMemoriesState()
            ->authenticateViaOAuth2Password(
                $this->apiCredentials->username,
                $this->apiCredentials->password,
                $this->apiCredentials->apiKey);
        $ds = new DataSource();
        $ds->setTitle("Sample Memory");
        $ds->setFile($this->makeTextFile());
        /** @var FamilySearchSourceDescriptionState $artifact */
        $artifact = $memories->addArtifact($ds)->get();
        /** @var DiscussionState $state */
        $comments = $artifact->readComments();
        $comment = new Comment();
        $comment->setText("Test comment.");
        $comments->addComment($comment);
        $state = $artifact->readComments();

        $this->assertNotNull($state->ifSuccessful());
        $this->assertEquals(HttpStatus::OK, $state->getResponse()->getStatusCode());
        $this->assertNotNull($state->getDiscussion());
        $this->assertNotNull($state->getDiscussion()->getComments());
        $this->assertGreaterThan(0, count($state->getDiscussion()->getComments()));

        $artifact->delete();
    }

    public function testUpdateMemoriesComment()
    {
        $factory = new FamilySearchStateFactory();
        /** @var FamilySearchMemories $memories */
        $memories = $factory->newMemoriesState()
            ->authenticateViaOAuth2Password(
                $this->apiCredentials->username,
                $this->apiCredentials->password,
                $this->apiCredentials->apiKey);
        $ds = new DataSource();
        $ds->setTitle("Sample Memory");
        $ds->setFile($this->makeTextFile());
        /** @var FamilySearchSourceDescriptionState $artifact */
        $artifact = $memories->addArtifact($ds)->get();
        $comments = $artifact->readComments();
        $comment = new Comment();
        $comment->setText("Test comment.");
        $comments->addComment($comment);
        $comments = $artifact->readComments();
        $update = array_shift($comments->getDiscussion()->getComments());
        $update->setText("Updated comment");
        $state = $comments->updateComment($update);

        $this->assertNotNull($state->ifSuccessful());
        $this->assertEquals(HttpStatus::NO_CONTENT, $state->getResponse()->getStatusCode());

        $artifact->delete();
    }

    private function makeTextFile()
    {
        $filename = 'test_' . bin2hex(openssl_random_pseudo_bytes(8)) . ".txt";

        $file = file_put_contents($filename, join(" ", $this->faker->words()));
        if (!$file) {
            $this->fail("Failed to write test file.");
        }


        return $filename;
    }
} 