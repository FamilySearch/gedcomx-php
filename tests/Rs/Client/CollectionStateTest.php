<?php

namespace Gedcomx\Tests\Rs\Client;

use Gedcomx\Rs\Client\Options\HeaderParameter;
use Gedcomx\Rs\Client\Rel;
use Gedcomx\Rs\Client\StateFactory;
use Gedcomx\Rs\Client\Util\HttpStatus;
use Gedcomx\Tests\ApiTestCase;

class CollectionStateTest extends ApiTestCase
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
     * @link https://familysearch.org/developers/docs/api/discussions/Read_Date_Authority_usecase
     */
    public function testReadDateAuthority()
    {
        $factory = new StateFactory();
        $this->collectionState($factory);
        $subsState = $this->collectionState()->readSubcollections();

        $dateState = $factory->newCollectionState();
    }
}