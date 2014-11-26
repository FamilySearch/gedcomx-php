<?php 

namespace Gedcomx\Tests\Functional;

use Gedcomx\Tests\ApiTestCase;

class UserTests extends ApiTestCase
{
    /**
     * @link https://familysearch.org/developers/docs/api/tree/Read_Current_Tree_Person_usecase
     */
    public function testReadCurrentTreePerson()
    {
        $collection = $this->collectionState(new StateFactory());
        $personState = $collection->readPersonForCurrentUser();
        /*
         * readPersonForCurrentUser will return a 303 redirect by default.
         * assert the URL on the person state is not the original request URL.
         */
        $this->assertFalse(strpos($personState->getResponse()->getEffectiveUrl(),Rel::CURRENT_USER_PERSON));
    }

    /**
     * @link https://familysearch.org/developers/docs/api/tree/Read_Current_Tree_Person_Expecting_200_Response_usecase
     */
    public function testReadCurrentTreePersonExpecting200()
    {
        $collection = $this->collectionState(new StateFactory());
        $expect200 = new HeaderParameter(true,"Expect",200);
        $personState = $collection->readPersonForCurrentUser($expect200);
        $this->assertEquals(HttpStatus::OK, $personState->getResponse()->getStatusCode());
    }

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
     * @link https://familysearch.org/developers/docs/api/tree/Read_User_usecase
     */
    public function testReadUser()
    {
        $this->markTestIncomplete('Not yet implemented.');
    }

    /**
     * @link https://familysearch.org/developers/docs/api/tree/Read_Current_User_usecase
     */
    public function testUpdateCurrentUserHistory()
    {
        $factory = new FamilySearchStateFactory();
        $this->collectionState($factory);
        $historyState = $this->collectionState()->readCurrentUserHistory();
        $stateTwo = $historyState->post($historyState->getEntity());

        $this->assertEquals(
            HttpStatus::NO_CONTENT,
            $stateTwo->getResponse()->getStatusCode(),
            $this->buildFailMessage(__METHOD__, $stateTwo)
        );
    }
}