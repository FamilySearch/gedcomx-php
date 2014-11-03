<?php

namespace Gedcomx\Tests\Extensions\FamilySearch\Rs\Client;

use Gedcomx\Extensions\FamilySearch\Platform\Discussions\Discussion;
use Gedcomx\Extensions\FamilySearch\Rs\Client\FamilySearchStateFactory;
use Gedcomx\Rs\Client\Util\HttpStatus;
use Gedcomx\Tests\ApiTestCase;
use Gedcomx\Tests\DiscussionBuilder;

class DiscussionStateTest extends ApiTestCase{

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

    }

    /**
     * @link https://familysearch.org/developers/docs/api/discussions/Read_Comments_usecase
     */
    public function testReadComments()
    {

    }

    /**
     * @link https://familysearch.org/developers/docs/api/discussions/Read_Discussion_usecase
     */
    public function testReadComment()
    {

    }

    /**
     * @link https://familysearch.org/developers/docs/api/discussions/Update_Comment_usecase
     */
    public function testUpdateComment()
    {

    }

    /**
     * @link https://familysearch.org/developers/docs/api/discussions/Delete_Comment_usecase
     */
    public function testDeleteComment()
    {

    }
} 