<?php 

namespace Gedcomx\Tests\Functional;

use Gedcomx\Extensions\FamilySearch\Rs\Client\FamilySearchStateFactory;
use Gedcomx\Rs\Client\Options\HeaderParameter;
use Gedcomx\Rs\Client\Rel;
use Gedcomx\Rs\Client\StateFactory;
use Gedcomx\Rs\Client\Util\HttpStatus;
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
        $this->assertFalse(strpos($personState->getResponse()->getEffectiveUrl(), Rel::CURRENT_USER_PERSON));
        $this->assertNotNull($personState->getEntity(), "Person entity is null.");
        $this->assertNotEmpty($personState->getPerson(), "No person returned.");
    }

    /**
     * @link https://familysearch.org/developers/docs/api/tree/Read_Current_User_usecase
     */
    public function testReadCurrentUser()
    {
        $factory = new FamilySearchStateFactory();
        $this->collectionState($factory);
        $userState = $this->collectionState()->readCurrentUser();
        $this->assertEquals(
            HttpStatus::OK,
            $userState->getResponse()->getStatusCode(),
            $this->buildFailMessage(__METHOD__, $userState)
        );
        $this->assertNotNull($userState->getEntity(), "User entity is null.");
        $this->assertNotEmpty($userState->getUser(), "No user object returned.");
    }

    /**
     * @link https://familysearch.org/developers/docs/api/tree/Read_Current_User's_History_usecase
     */
    public function testReadCurrentUserHistory()
    {
        $this->markTestSkipped('Skipping for now. Despite posting history and receiving a 200-OK response, the server does not subsequently return this data.');

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
        $factory = new StateFactory();
        $this->collectionState($factory);
        $personState = $this->createPerson()->get();
        $names = $personState->getPerson()->getNames();
        /** @var \Gedcomx\Rs\Client\AgentState $agentState */
        $agentState = $personState->readAttributableContributor($names[0]);

        $this->assertEquals(
            HttpStatus::OK,
            $agentState->getResponse()->getStatusCode(),
            $this->buildFailMessage(__METHOD__, $agentState)
        );
        $this->assertNotNull($agentState->getEntity(), "Agent entity is null.");
        $this->assertNotEmpty($agentState->getAgent(), "Agent object not found.");
    }

    /**
     * @link https://familysearch.org/developers/docs/api/tree/Update_Current_User's_History_usecase
     */
    public function testUpdateCurrentUserHistory()
    {
        $this->markTestSkipped('Skipping for now. Despite posting history and receiving a 200-OK response, the server does not subsequently return this data.');

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