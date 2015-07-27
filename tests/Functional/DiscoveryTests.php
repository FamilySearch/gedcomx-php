<?php

namespace Gedcomx\Tests\Functional;

use Gedcomx\Extensions\FamilySearch\Rs\Client\FamilySearchCollectionState;
use Gedcomx\Extensions\FamilySearch\Rs\Client\FamilyTree\FamilyTreeStateFactory;
use Gedcomx\Rs\Client\Rel;
use Gedcomx\Rs\Client\StateFactory;
use Gedcomx\Rs\Client\Util\HttpStatus;
use Gedcomx\Tests\ApiTestCase;

class DiscoveryTests extends ApiTestCase
{
    /**
     * @vcr DiscoveryTests/testReadRootCollection.json
     * @link https://familysearch.org/developers/docs/api/tree/Read_Root_Collection_usecase
     */
    public function testReadRootCollection()
    {
        $factory = new StateFactory();
        $collection = $factory->newDiscoveryState();
        $this->assertEquals(
            HttpStatus::OK,
            $collection->getStatus(),
            $this->buildFailMessage(__METHOD__, $collection)
        );
    }

    /**
     * @vcr DiscoveryTests/testReadFamilySearchCollections.json
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
     * @vcr DiscoveryTests/testReadControlledVocabulary.json
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
            $dateState->getStatus(),
            $this->buildFailMessage(__METHOD__."(Read date collection)", $dateState)
        );
    }

    /**
     * @vcr DiscoveryTests/testFamilySearchFamilyTree.json
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
            $dateState->getStatus(),
            $this->buildFailMessage(__METHOD__."(Read date collection)", $dateState)
        );
    }

    /**
     * @vcr DiscoveryTests/testReadDateAuthority.json
     * @link https://familysearch.org/developers/docs/api/discussions/Read_Date_Authority_usecase
     */
    public function testReadDateAuthority()
    {
        $factory = new FamilyTreeStateFactory();
        $collection = $factory->newDiscoveryState();
        $subsState = $collection->readSubcollections();
        $this->assertNotNull($subsState);
        $collections = $subsState->getCollections();
        $link = null;
        foreach ($collections as $record) {
            if ($record->getId() == "FSDA") {
                $link = $record->getLink(Rel::SELF);
                break;
            }
        }
        $this->assertNotEmpty($link, 'Date Authority link not found');
        /** @var FamilySearchCollectionState $dateState */
        $dateState = $factory->newCollectionState($link->getHref());
        $this->assertEquals(
            HttpStatus::OK,
            $dateState->getStatus(),
            $this->buildFailMessage(__METHOD__."(Read date collection)", $dateState)
        );
        $normalized = $dateState->normalizeDate("26 Nov 1934");
        $this->assertEquals(
            'gedcomx-date:+1934-11-26',
            $normalized->getFormal(),
            "Formalized date format incorrect: " . $normalized->getFormal()
        );
        $extensions = $normalized->getNormalizedExtensions();
        $this->assertEquals(
            '26 November 1934',
            $extensions[0]->getValue(),
            "Normalized date format incorrect: " . $extensions[0]->getValue()
        );
    }

    /**
     * @vcr DiscoveryTests/testReadDiscussionsCollection.json
     * @link https://familysearch.org/developers/docs/api/discussions/Read_Discussions_Collection_usecase
     */
    public function testReadDiscussionsCollection()
    {
        $factory = new StateFactory();
        $collection = $factory->newDiscoveryState();
        $subsState = $collection->readSubcollections();
        $this->assertNotNull($subsState);
        $collections = $subsState->getCollections();
        $this->assertNotEmpty($collections);
        $link = null;
        foreach ($collections as $record) {
            if ($record->getId() == "FSDF") {
                $link = $record->getLink(Rel::SELF);
                break;
            }
        }
        $this->assertNotEmpty($link, 'Discussion link not found');
        $dateState = $factory->newCollectionState($link->getHref());
        $this->assertEquals(
            HttpStatus::OK,
            $dateState->getStatus(),
            $this->buildFailMessage(__METHOD__."(Read discussions collection)", $dateState)
        );
    }

    /**
     * @vcr DiscoveryTests/testReadMemoriesCollection.json
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
            $dateState->getStatus(),
            $this->buildFailMessage(__METHOD__."(Read date collection)", $dateState)
        );
    }

    /**
     * @vcr DiscoveryTests/testReadUserDefinedSourcesCollection.json
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
            $dateState->getStatus(),
            $this->buildFailMessage(__METHOD__."(Read date collection)", $dateState)
        );
    }

    /**
     * @vcr DiscoveryTests/testReadPlaceAuthority.json
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
            $dateState->getStatus(),
            $this->buildFailMessage(__METHOD__."(Read place collection)", $dateState)
        );
    }

    /**
     * @vcr DiscoveryTests/testReadFamilySearchHistoricalRecordsArchive.json
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
            $dateState->getStatus(),
            $this->buildFailMessage(__METHOD__."(Read place collection)", $dateState)
        );
    }

}
