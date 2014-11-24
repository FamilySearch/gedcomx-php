<?php

namespace Gedcomx\Tests\Extensions\FamilySearch\Rs\Client;

use Gedcomx\Common\Attribution;
use Gedcomx\Common\Note;
use Gedcomx\Common\ResourceReference;
use Gedcomx\Common\TextValue;
use Gedcomx\Extensions\FamilySearch\Platform\Discussions\Discussion;
use Gedcomx\Extensions\FamilySearch\Rs\Client\DiscussionState;
use Gedcomx\Extensions\FamilySearch\Rs\Client\FamilySearchStateFactory;
use Gedcomx\Extensions\FamilySearch\Rs\Client\FamilyTree\FamilyTreeStateFactory;
use Gedcomx\Rs\Client\SourceDescriptionState;
use Gedcomx\Rs\Client\Util\HttpStatus;
use Gedcomx\Source\SourceCitation;
use Gedcomx\Source\SourceDescription;
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
            if ($c->getId() == $comment->getId() & $c->getText() == $newText){
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

    public function testReadSourceDescription()
    {
        $this->collectionState(new FamilyTreeStateFactory());
        $sd = new SourceDescription();
        $citation = new SourceCitation();
        $citation->setValue("\"United States Census, 1900.\" database and digital images, FamilySearch (https://familysearch.org/: accessed 17 Mar 2012), Ethel Hollivet, 1900; citing United States Census Office, Washington, D.C., 1900 Population Census Schedules, Los Angeles, California, population schedule, Los Angeles Ward 6, Enumeration District 58, p. 20B, dwelling 470, family 501, FHL microfilm 1,240,090; citing NARA microfilm publication T623, roll 90.");
        $sd->setCitations(array($citation));
        $title = new TextValue();
        $title->setValue("1900 US Census, Ethel Hollivet");
        $sd->setTitles(array($title));
        $note = new Note();
        $note->setText("Ethel Hollivet (line 75) with husband Albert Hollivet (line 74); also in the dwelling: step-father Joseph E Watkins (line 72), mother Lina Watkins (line 73), and grandmother -- Lina's mother -- Mary Sasnett (line 76).  Albert's mother and brother also appear on this page -- Emma Hollivet (line 68), and Eddie (line 69).");
        $sd->setNotes(array($note));
        $attribution = new Attribution();
        $rr = new ResourceReference();
        $rr->setResource("https://familysearch.org/platform/users/agents/MM6M-8QJ");
        $rr->setResourceId("MM6M-8QJ");
        $attribution->setContributor($rr);
        $attribution->setModified(time());
        $attribution->setChangeMessage("This is the change message");
        $sd->setAttribution($attribution);
        /** @var SourceDescriptionState $state */
        $state = $this->collectionState()->addSourceDescription($sd)->get();

        $this->assertNotNull($state->ifSuccessful());
        $this->assertEquals(HttpStatus::OK, $state->getResponse()->getStatusCode());
        $this->assertNotNull($state->getSourceDescription());
    }
}