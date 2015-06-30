<?php

namespace Gedcomx\Tests\Functional;

use Gedcomx\Extensions\FamilySearch\Platform\Discussions\Discussion;
use Gedcomx\Extensions\FamilySearch\Platform\Tree\DiscussionReference;
use Gedcomx\Extensions\FamilySearch\Rs\Client\DiscussionState;
use Gedcomx\Extensions\FamilySearch\Rs\Client\FamilySearchSourceDescriptionState;
use Gedcomx\Extensions\FamilySearch\Rs\Client\FamilySearchStateFactory;
use Gedcomx\Extensions\FamilySearch\Rs\Client\Memories\FamilySearchMemories;
use Gedcomx\Rs\Client\Util\DataSource;
use Gedcomx\Extensions\FamilySearch\Rs\Client\FamilyTree\FamilyTreeStateFactory;
use Gedcomx\Rs\Client\Util\HttpStatus;
use Gedcomx\Tests\ApiTestCase;
use Gedcomx\Tests\DiscussionBuilder;
use Gedcomx\Tests\TestBuilder;

class DiscussionsTests extends ApiTestCase
{
    
    /**
     * @vcr DiscussionsTests/testCreateDiscussion.json
     * @link https://familysearch.org/developers/docs/api/discussions/Create_Discussion_usecase
     */
    public function testCreateDiscussion()
    {
        $factory = new FamilySearchStateFactory();
        $this->collectionState($factory);


        $userState = $this->collectionState()->readCurrentUser();
        $discussion = DiscussionBuilder::createDiscussion($userState->getUser()->getTreeUserId());

        $newState = $this->collectionState()->addDiscussion($discussion);
        $this->queueForDelete($newState);

        $this->assertAttributeEquals(HttpStatus::CREATED, "statusCode", $newState->getResponse(), $this->buildFailMessage(__METHOD__, $newState));
    }

    /**
     * @vcr DiscussionsTests/testCreateDiscussionReference.json
     * @link https://familysearch.org/developers/docs/api/tree/Create_Discussion_Reference_usecase
     */
    public function testCreateDiscussionReference(){
        $factory = new FamilyTreeStateFactory();
        $this->collectionState($factory);
        $testSubject = $this->createPerson();

        $userState = $this->collectionState()->readCurrentUser();
        $discussion = DiscussionBuilder::createDiscussion($userState->getUser()->getTreeUserId());

        $discussionState = $this->collectionState()->addDiscussion($discussion);

        $newState = $testSubject->addDiscussionState($discussionState);
        $this->queueForDelete($newState);

        $this->assertAttributeEquals(HttpStatus::CREATED, "statusCode", $newState->getResponse(), $this->buildFailMessage(__METHOD__, $newState) );
    }

    /**
     * @vcr DiscussionsTests/testCreateComment.json
     * @link https://familysearch.org/developers/docs/api/discussions/Create_Comment_usecase
     */
    public function testCreateComment()
    {
        $factory = new FamilySearchStateFactory();
        $this->collectionState($factory);

        $userState = $this->collectionState()->readCurrentUser();
        $discussion = DiscussionBuilder::createDiscussion($userState->getUser()->getTreeUserId());

        $state = $this->collectionState()->addDiscussion($discussion);
        $this->queueForDelete($state);
        $state = $state->get();

        $comment = DiscussionBuilder::createComment($userState);
        $state = $state->addComment($comment);
        $this->assertAttributeEquals(HttpStatus::CREATED, "statusCode", $state->getResponse(), $this->buildFailMessage(__METHOD__, $state));
    }

    /**
     * @vcr DiscussionsTests/testReadDiscussion.json
     * @link https://familysearch.org/developers/docs/api/discussions/Read_Discussion_usecase
     */
    public function testReadDiscussion()
    {
        $factory = new FamilySearchStateFactory();
        $this->collectionState($factory);

        $userState = $this->collectionState()->readCurrentUser();
        $discussion = DiscussionBuilder::createDiscussion($userState->getUser()->getTreeUserId());

        $newState = $this->collectionState()->addDiscussion($discussion);
        $this->queueForDelete($newState);

        $newState = $newState->get();
        $this->assertAttributeEquals(HttpStatus::OK, "statusCode", $newState->getResponse(), $this->buildFailMessage(__METHOD__, $newState));
        $this->assertNotEmpty($newState->getDiscussion());
    }

    /**
     * @vcr DiscussionsTests/testReadDiscussionReference.json
     * @link https://familysearch.org/developers/docs/api/tree/Read_Discussion_References_usecase
     */
    public function testReadDiscussionReference(){
        $factory = new FamilyTreeStateFactory();
        $this->collectionState($factory);
        $testSubject = $this->createPerson()->get();

        $userState = $this->collectionState()->readCurrentUser();
        $discussion = DiscussionBuilder::createDiscussion($userState->getUser()->getTreeUserId());

        $discussionState = $this->collectionState()->addDiscussion($discussion);
        $this->queueForDelete($discussionState);

        $newState = $testSubject->addDiscussionState($discussionState);
        $this->queueForDelete($newState);

        $this->assertAttributeEquals(HttpStatus::CREATED, "statusCode", $newState->getResponse(), $this->buildFailMessage(__METHOD__, $newState) );

        $testSubject->loadDiscussionReferences();

        $found = false;
        foreach ($testSubject->getPerson()->getExtensionElements() as $ext) {
            if ($ext instanceof DiscussionReference) {
                $found = true;
                break;
            }
        }
        $this->assertTrue($found);
    }

    /**
     * @vcr DiscussionsTests/testReadComments.json
     * @link https://familysearch.org/developers/docs/api/discussions/Read_Comments_usecase
     */
    public function testReadComments()
    {
        $factory = new FamilySearchStateFactory();
        $this->collectionState($factory);

        $userState = $this->collectionState()->readCurrentUser();
        $discussion = DiscussionBuilder::createDiscussion($userState->getUser()->getTreeUserId());
        $state = $this->collectionState()->addDiscussion($discussion);
        $this->queueForDelete($state);

        $state = $state->get();
        $comment = DiscussionBuilder::createComment($userState);
        $state->addComment($comment);
        $comment = DiscussionBuilder::createComment($userState);
        $state->addComment($comment);
        $state = $state->get();

        $state->loadComments();
        $comments = $state->getDiscussion()->getComments();
        $this->assertEquals(2, count($comments));
    }

    /**
     * @vcr DiscussionsTests/testUpdateDiscussion.json
     * @link https://familysearch.org/developers/docs/api/discussions/Update_Discussion_usecase
     */
    public function testUpdateDiscussion()
    {
        $factory = new FamilySearchStateFactory();
        $this->collectionState($factory);

        $userState = $this->collectionState()->readCurrentUser();
        $discussion = DiscussionBuilder::createDiscussion($userState->getUser()->getTreeUserId());

        $newState = $this->collectionState()->addDiscussion($discussion);
        $this->queueForDelete($newState);

        $newState = $newState->get();
        /** @var Discussion $discussion */
        $discussion = $newState->getDiscussion();
        $discussion->setDetails(TestBuilder::faker()->paragraph());

        $updated = $newState->update($discussion);
        $this->assertAttributeEquals(HttpStatus::NO_CONTENT, "statusCode", $updated->getResponse(), $this->buildFailMessage(__METHOD__, $updated));
    }

    /**
     * @vcr DiscussionsTests/testUpdateComment.json
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
        $this->queueForDelete($state);

        $state = $state->get();
        $comment = DiscussionBuilder::createComment($userState);
        $state->addComment($comment);
        $comment = DiscussionBuilder::createComment($userState);
        $state->addComment($comment);
        $state = $state->get();

        $state->loadComments();
        $comments = $state->getDiscussion()->getComments();
        $comment = $comments[0];
        $newText = TestBuilder::faker()->paragraph();
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
    }

    /**
     * @vcr DiscussionsTests/testDeleteDiscussion.json
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
     * @vcr DiscussionsTests/testDeleteDiscussionReference.json
     * @link https://familysearch.org/developers/docs/api/tree/Delete_Discussion_Reference_usecase
     */
    public function testDeleteDiscussionReference()
    {
        $factory = new FamilyTreeStateFactory();
        $this->collectionState($factory);
        $testSubject = $this->createPerson();

        $userState = $this->collectionState()->readCurrentUser();
        $discussion = DiscussionBuilder::createDiscussion($userState->getUser()->getTreeUserId());

        $discussionState = $this->collectionState()->addDiscussion($discussion);
        $this->queueForDelete($discussionState);

        $ref = new DiscussionReference();
        $ref->setResource($discussionState->getSelfUri());

        $newState = $testSubject->deleteDiscussionReference($ref);

        $this->assertAttributeEquals(HttpStatus::NO_CONTENT, "statusCode", $newState->getResponse(), $this->buildFailMessage(__METHOD__, $newState) );
    }

    /**
     * @vcr DiscussionsTests/testDeleteComment.json
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
        $this->queueForDelete($state);

        $state = $state->get();
        
        $comment = DiscussionBuilder::createComment($userState);
        $addedCommentState = $state->addComment($comment);
        $this->assertAttributeEquals(HttpStatus::CREATED, "statusCode", $addedCommentState->getResponse(), $this->buildFailMessage(__METHOD__, $addedCommentState));

        $state = $state->get();
        $state->loadComments();
        $comments = $state->getDiscussion()->getComments();
        $this->assertEquals(1, count($comments));

        $deleted = $state->deleteComment($comments[0]);
        $this->assertAttributeEquals(HttpStatus::NO_CONTENT, "statusCode", $deleted->getResponse(), $this->buildFailMessage(__METHOD__, $deleted));
    }
}
