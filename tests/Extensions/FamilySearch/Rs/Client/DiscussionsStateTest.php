<?php

namespace Gedcomx\Tests\Extensions\FamilySearch\Rs\Client;

use Gedcomx\Extensions\FamilySearch\Rs\Client\FamilySearchStateFactory;
use Gedcomx\Rs\Client\Util\HttpStatus;
use Gedcomx\Tests\ApiTestCase;
use Gedcomx\Tests\DiscussionBuilder;

class DiscussionsStateTest extends ApiTestCase {

    /*
     * https://familysearch.org/developers/docs/api/discussions/Create_Discussion_usecase
     */
    public function testCreateDiscussion(){
        $factory = new FamilySearchStateFactory();
        $this->collectionState($factory);

        $userState = $this->collectionState()->readCurrentUser();
        $discussion = DiscussionBuilder::createDiscussion($userState->getUser()->getTreeUserId());

        $newState = $this->collectionState()->addDiscussion($discussion);

        $this->assertAttributeEquals(HttpStatus::CREATED, "statusCode", $newState->getResponse(), $this->buildFailMessage(__METHOD__, $newState));
    }

}