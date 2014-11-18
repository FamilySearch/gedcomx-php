<?php

namespace Gedcomx\Tests\Rs\Client;

use Gedcomx\Extensions\FamilySearch\Rs\Client\FamilySearchStateFactory;
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
     * @link https://familysearch.org/developers/docs/api/tree/Read_Root_Collection_usecase
     */
    public function testReadRootCollection()
    {
        $factory = new StateFactory();
        $collection = $factory->newDiscoveryState();
        $this->assertEquals(
            HttpStatus::OK,
            $collection->getResponse()->getStatusCode(),
            $this->buildFailMessage(__METHOD__, $collection)
        );
    }

    /**
     * @link https://familysearch.org/developers/docs/api/tree/Read_FamilySearch_Collections_usecase
     */
    public function testReadFamilySearchCollections()
    {
        $factory = new StateFactory();
        $collection = $factory->newDiscoveryState();
        $subsState = $collection->readSubcollections();
        $this->assertNotEmpty($subsState->getCollections());
    }

    /**
     * @link https://familysearch.org/developers/docs/api/cv/Read_Controlled_Vocabulary_usecase
     */
    public function testReadControlledVocabulary()
    {
        $factory = new StateFactory();
        $collection = $factory->newDiscoveryState();
        $subsState = $collection->readSubcollections();
        $collections = $subsState->getCollections();
        $link = null;
        foreach ($collections as $record) {
            if ($record->getId() == "FSCV") {
                $link = $record->getLink(Rel::SELF);
                break;
            }
        }
        $this->assertNotEmpty($link, 'Date Authority link not found');
        $dateState = $factory->newCollectionState($link->getHref());
        $this->assertEquals(
            HttpStatus::OK,
            $dateState->getResponse()->getStatusCode(),
            $this->buildFailMessage(__METHOD__."(Read date collection)", $dateState)
        );
    }

    /**
     * @link https://familysearch.org/developers/docs/api/cv/Read_Controlled_Vocabulary_usecase
     */
    public function testFamilySearchFamilyTree()
    {
        $factory = new StateFactory();
        $collection = $factory->newDiscoveryState();
        $subsState = $collection->readSubcollections();
        $collections = $subsState->getCollections();
        $link = null;
        foreach ($collections as $record) {
            if ($record->getId() == "FSFT") {
                $link = $record->getLink(Rel::SELF);
                break;
            }
        }
        $this->assertNotEmpty($link, 'Date Authority link not found');
        $dateState = $factory->newCollectionState($link->getHref());
        $this->assertEquals(
            HttpStatus::OK,
            $dateState->getResponse()->getStatusCode(),
            $this->buildFailMessage(__METHOD__."(Read date collection)", $dateState)
        );
    }

    /**
     * @link https://familysearch.org/developers/docs/api/discussions/Read_Date_Authority_usecase
     */
    public function testReadDateAuthority()
    {
        $factory = new StateFactory();
        $collection = $factory->newDiscoveryState();
        $subsState = $collection->readSubcollections();
        $collections = $subsState->getCollections();
        $link = null;
        foreach ($collections as $record) {
            if ($record->getId() == "FSDA") {
               $link = $record->getLink(Rel::SELF);
                break;
            }
        }
        $this->assertNotEmpty($link, 'Date Authority link not found');
        $dateState = $factory->newCollectionState($link->getHref());
        $this->assertEquals(
            HttpStatus::OK,
            $dateState->getResponse()->getStatusCode(),
            $this->buildFailMessage(__METHOD__."(Read date collection)", $dateState)
        );
    }

    /**
     * @link https://familysearch.org/developers/docs/api/discussions/Read_Discussions_Collection_usecase
     */
    public function testReadDiscussionsCollection()
    {
        $factory = new StateFactory();
        $collection = $factory->newDiscoveryState();
        $subsState = $collection->readSubcollections();
        $collections = $subsState->getCollections();
        $link = null;
        foreach ($collections as $record) {
            if ($record->getId() == "FSDF") {
               $link = $record->getLink(Rel::SELF);
                break;
            }
        }
        $this->assertNotEmpty($link, 'Date Authority link not found');
        $dateState = $factory->newCollectionState($link->getHref());
        $this->assertEquals(
            HttpStatus::OK,
            $dateState->getResponse()->getStatusCode(),
            $this->buildFailMessage(__METHOD__."(Read date collection)", $dateState)
        );
    }

    /**
     * @link https://familysearch.org/developers/docs/api/memories/Read_Memories_Collection_usecase
     */
    public function testReadMemoriesCollection()
    {
        $factory = new StateFactory();
        $collection = $factory->newDiscoveryState();
        $subsState = $collection->readSubcollections();
        $collections = $subsState->getCollections();
        $link = null;
        foreach ($collections as $record) {
            if ($record->getId() == "FSMEM") {
               $link = $record->getLink(Rel::SELF);
                break;
            }
        }
        $this->assertNotEmpty($link, 'Date Authority link not found');
        $dateState = $factory->newCollectionState($link->getHref());
        $this->assertEquals(
            HttpStatus::OK,
            $dateState->getResponse()->getStatusCode(),
            $this->buildFailMessage(__METHOD__."(Read date collection)", $dateState)
        );
    }

    /**
     * @link https://familysearch.org/developers/docs/api/memories/Read_User-Defined_Sources_Collection_usecase
     */
    public function testReadUserDefinedSourcesCollection()
    {
        $factory = new StateFactory();
        $collection = $factory->newDiscoveryState();
        $subsState = $collection->readSubcollections();
        $collections = $subsState->getCollections();
        $link = null;
        foreach ($collections as $record) {
            if ($record->getId() == "FSUDS") {
               $link = $record->getLink(Rel::SELF);
                break;
            }
        }
        $this->assertNotEmpty($link, 'Date Authority link not found');
        $dateState = $factory->newCollectionState($link->getHref());
        $this->assertEquals(
            HttpStatus::OK,
            $dateState->getResponse()->getStatusCode(),
            $this->buildFailMessage(__METHOD__."(Read date collection)", $dateState)
        );
    }

    /**
     * @link https://familysearch.org/developers/docs/api/discussions/Read_Place_Authority_usecase
     */
    public function testReadPlaceAuthority()
    {
        $factory = new StateFactory();
        $collection = $factory->newDiscoveryState();
        $subsState = $collection->readSubcollections();
        $collections = $subsState->getCollections();
        $link = null;
        foreach ($collections as $record) {
            if ($record->getId() == "FSPA") {
               $link = $record->getLink(Rel::SELF);
                break;
            }
        }
        $this->assertNotEmpty($link, 'Place Authority link not found');
        $dateState = $factory->newCollectionState($link->getHref());
        $this->assertEquals(
            HttpStatus::OK,
            $dateState->getResponse()->getStatusCode(),
            $this->buildFailMessage(__METHOD__."(Read place collection)", $dateState)
        );
    }

    /**
     * @link https://familysearch.org/developers/docs/api/memories/Read_FamilySearch_Historical_Records_Archive_usecase
     */
    public function testReadFamilySearchHistoricalRecordsArchive()
    {
        $factory = new StateFactory();
        $collection = $factory->newDiscoveryState();
        $subsState = $collection->readSubcollections();
        $collections = $subsState->getCollections();
        $link = null;
        foreach ($collections as $record) {
            if ($record->getId() == "FSHRA") {
               $link = $record->getLink(Rel::SELF);
                break;
            }
        }
        $this->assertNotEmpty($link, 'Place Authority link not found');
        $dateState = $factory->newCollectionState($link->getHref());
        $this->assertEquals(
            HttpStatus::OK,
            $dateState->getResponse()->getStatusCode(),
            $this->buildFailMessage(__METHOD__."(Read place collection)", $dateState)
        );
    }
}