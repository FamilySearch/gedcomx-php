<?php

namespace Gedcomx\tests\Extensions\FamilySearch\Rs\Client;

use Gedcomx\Extensions\FamilySearch\Rs\Client\FamilySearchStateFactory;
use Gedcomx\Rs\Client\Util\HttpStatus;
use Gedcomx\Tests\ApiTestCase;

class FamilySearchCollectionStateTest extends ApiTestCase
{
    /**
     * @link https://familysearch.org/developers/docs/api/tree/Read_Current_User_usecase
     */
    public function testReadCurrentUser()
    {
        $factory = new FamilySearchStateFactory();
        $this->collectionState($factory);
        $userState = $this->collectionState()->readCurrentUser();
        $this->assertEquals(HttpStatus::OK, $userState->getResponse()->getStatusCode());
    }

    /**
     * @link https://familysearch.org/developers/docs/api/tree/Read_Current_User_usecase
     */
    public function testReadCurrentUserHistory()
    {
        $factory = new FamilySearchStateFactory();
        $this->collectionState($factory);
        $historyState = $this->collectionState()->readCurrentUserHistory();
        $this->assertEquals(
            HttpStatus::OK,
            $historyState->getResponse()->getStatusCode(),
            $this->buildFailMessage(__METHOD__, $historyState)
        );
        $this->assertNotEmpty($historyState->getUserHistory());
    }

    /**
     * @link https://familysearch.org/developers/docs/api/tree/Read_Current_User_usecase
     */
    public function testUpdateCurrentUserHistory()
    {
        $factory = new FamilySearchStateFactory();
        $this->collectionState($factory);
        $historyState = $this->collectionState()->readCurrentUserHistory();


        $this->assertEquals(
            HttpStatus::NO_CONTENT,
            $historyState->getResponse()->getStatusCode(),
            $this->buildFailMessage(__METHOD__, $historyState)
        );
    }
}