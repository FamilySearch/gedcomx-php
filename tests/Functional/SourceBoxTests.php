<?php 

namespace Gedcomx\Tests\Functional;

use Gedcomx\Common\Attribution;
use Gedcomx\Common\Note;
use Gedcomx\Common\ResourceReference;
use Gedcomx\Common\TextValue;
use Gedcomx\Extensions\FamilySearch\Rs\Client\FamilySearchCollectionState;
use Gedcomx\Extensions\FamilySearch\Rs\Client\FamilySearchSourceDescriptionState;
use Gedcomx\Extensions\FamilySearch\Rs\Client\FamilySearchStateFactory;
use Gedcomx\Records\Collection;
use Gedcomx\Rs\Client\CollectionsState;
use Gedcomx\Rs\Client\CollectionState;
use Gedcomx\Rs\Client\GedcomxApplicationState;
use Gedcomx\Rs\Client\Util\HttpStatus;
use Gedcomx\Source\SourceCitation;
use Gedcomx\Source\SourceDescription;
use Gedcomx\Tests\ApiTestCase;
use Gedcomx\Tests\SourceBuilder;
use Gedcomx\Tests\TestBuilder;

class SourceBoxTests extends ApiTestCase
{
    /**
     * @vcr SourceBoxTests/testCreateUserDefinedCollection.json
     * @link https://familysearch.org/developers/docs/api/sources/Create_User-Defined_Collection_usecase
     */
    public function testCreateUserDefinedCollection()
    {
        $factory = new FamilySearchStateFactory();
        /** @var FamilySearchCollectionState $collection */
        $collection = $this->collectionState($factory, "https://sandbox.familysearch.org/platform/collections/sources");
        $c = new Collection();
        $c->setTitle(TestBuilder::faker()->sha1);
        $state = $collection->addCollection($c);
        $this->queueForDelete($state);

        $this->assertNotNull($state->ifSuccessful());
        $this->assertEquals(HttpStatus::CREATED, $state->getStatus());

        $subcollections = $collection->readSubcollections();
        $this->assertEquals(HttpStatus::OK, $subcollections->getStatus());
        $collectionList = $subcollections->getCollections();
        $this->assertNotEmpty($collectionList);
        $found = false;
        foreach($collectionList as $collectionItem){
            $found = $collectionItem->getTitle() == $c->getTitle();
            if ($found) break;
        }

        $this->assertTrue($found);
    }

    /**
     * @vcr SourceBoxTests/testReadAPageOfTheSourcesInAUserDefinedCollection.json
     * @link https://familysearch.org/developers/docs/api/sources/Read_A_Page_of_the_Sources_in_a_User-Defined_Collection_usecase
     */
    public function testReadAPageOfTheSourcesInAUserDefinedCollection()
    {
        $factory = new FamilySearchStateFactory();
        /** @var FamilySearchCollectionState $collection */
        $collection = $this->collectionState($factory, "https://sandbox.familysearch.org/platform/collections/sources");
        $sds = $this->createSource();
        $this->assertEquals(HttpStatus::CREATED, $sds->getStatus());
        $sub = $collection->readSubcollections();
        $this->assertEquals(HttpStatus::OK, $sub->getStatus());
        /** @var CollectionsState $subcollections */
        $subcollections = $sub->get();
        $this->assertEquals(HttpStatus::OK, $subcollections->getStatus());

        $collectionList = $subcollections->getCollections();
        $c = array_pop($collectionList);
        $subcollection = $subcollections->readCollection($c);
        $state = $subcollection->readSourceDescriptions();

        $this->assertNotNull($state->ifSuccessful());
        $this->assertEquals(HttpStatus::OK, $state->getStatus());
        $this->assertNotNull($state->getEntity());
        $this->assertNotEmpty($state->getEntity()->getSourceDescriptions());
    }

    /**
     * @vcr SourceBoxTests/testReadASpecificUsersSetOfUserDefinedCollections.json
     * @link https://familysearch.org/developers/docs/api/sources/Read_A_Specific_User%27s_Set_of_User-Defined_Collections_usecase
     */
    public function testReadASpecificUsersSetOfUserDefinedCollections()
    {
        $factory = new FamilySearchStateFactory();
        /** @var FamilySearchCollectionState $collection */
        $collection = $this->collectionState($factory, "https://sandbox.familysearch.org/platform/collections/sources");
        /** @var CollectionsState $subcollections */
        $subcollections = $collection->readSubcollections();

        $this->assertNotNull($subcollections->ifSuccessful());
        $this->assertEquals(HttpStatus::OK, $subcollections->getStatus());
        $this->assertNotNull($subcollections->getCollections());
        $this->assertGreaterThan(0, count($subcollections->getCollections()));
    }

    /**
     * @vcr SourceBoxTests/testReadAllSourcesOfAllUserDefinedCollectionsOfASpecificUser.json
     * @link https://familysearch.org/developers/docs/api/sources/Read_All_Sources_of_All_User-Defined_Collections_of_a_Specific_User_usecase
     */
    public function testReadAllSourcesOfAllUserDefinedCollectionsOfASpecificUser()
    {
        $factory = new FamilySearchStateFactory();
        /** @var FamilySearchCollectionState $collection */
        $collection = $this->collectionState($factory, "https://sandbox.familysearch.org/platform/collections/sources");
        $subcollections = $collection->readSubcollections();
        $this->assertEquals(HttpStatus::OK, $subcollections->getStatus());

        // Get the root collection
        $rootUserCollection = $this->getRootCollection($collection);
        $this->assertNotNull($rootUserCollection);
        $this->assertEquals(HttpStatus::OK, $rootUserCollection->getStatus());
        $state = $rootUserCollection->readSourceDescriptions();

        $this->assertNotNull($state->ifSuccessful());
        $this->assertEquals(HttpStatus::OK, $state->getStatus());
        $this->assertNotNull($state->getEntity()->getCollections());
        $this->assertGreaterThan(0, count($rootUserCollection->getEntity()->getCollections()));

        /** @var FamilySearchSourceDescriptionState $sd */
        $c = new Collection();
        $c->setTitle(TestBuilder::faker()->sha1);
        $subcollection = $collection->addCollection($c);
        $this->queueForDelete($subcollection);
        $this->assertEquals(HttpStatus::CREATED, $subcollection->getStatus());
        $subcollection = $subcollection->get();
        $this->assertEquals(HttpStatus::OK, $subcollection->getStatus());

        $sd = $this->createSource();
        $this->assertEquals(HttpStatus::CREATED, $sd->getStatus());
        $sd = $sd->get();
        $this->assertEquals(HttpStatus::OK, $sd->getStatus());
        $test = $sd->moveToCollection($subcollection);
        $this->assertEquals(HttpStatus::NO_CONTENT, $test->getStatus());

        $allSDs = $collection->readSourceDescriptions()->getEntity()->getSourceDescriptions();
        $found = $this->findInCollection($sd->getSourceDescription(), $rootUserCollection->readSourceDescriptions()->getEntity()->getSourceDescriptions());
        $this->assertFalse($found);
        $found = $this->findInCollection($sd->getSourceDescription(), $subcollection->readSourceDescriptions()->getEntity()->getSourceDescriptions());
        $this->assertTrue($found);
        $found = $this->findInCollection($sd->getSourceDescription(), $allSDs);
        $this->assertTrue($found);
        $sd->delete(); // Ensure this is deleted before the user collection is deleted
    }

    /**
     * @vcr SourceBoxTests/testReadUserDefinedCollection.json
     * @link https://familysearch.org/developers/docs/api/sources/Read_User-Defined_Collection_usecase
     */
    public function testReadUserDefinedCollection()
    {
        $factory = new FamilySearchStateFactory();
        /** @var FamilySearchCollectionState $collection */
        $collection = $this->collectionState($factory, "https://sandbox.familysearch.org/platform/collections/sources");
        $c = new Collection();
        $c->setTitle(TestBuilder::faker()->sha1);
        $state = $collection->addCollection($c);
        $this->queueForDelete($state);
        $this->assertEquals(HttpStatus::CREATED, $state->getStatus());
        $state = $state->get();
        $this->assertEquals(HttpStatus::OK, $state->getResponse()->getSTatusCode());

        $this->assertNotNull($state->ifSuccessful());
        $this->assertEquals(HttpStatus::OK, $state->getStatus());
    }

    /**
     * @vcr SourceBoxTests/testUpdateUserDefinedCollection.json
     * @link https://familysearch.org/developers/docs/api/sources/Update_User-Defined_Collection_usecase
     */
    public function testUpdateUserDefinedCollection()
    {
        $factory = new FamilySearchStateFactory();
        /** @var FamilySearchCollectionState $collection */
        $collection = $this->collectionState($factory, "https://sandbox.familysearch.org/platform/collections/sources");
        $c = new Collection();
        $c->setTitle(TestBuilder::faker()->sha1);
        /** @var CollectionState $subcollection */
        $subcollection = $collection->addCollection($c);
        $this->queueForDelete($subcollection);
        $this->assertEquals(HttpStatus::CREATED, $subcollection->getStatus());
        $subcollection = $subcollection->get();
        $this->assertEquals(HttpStatus::OK, $subcollection->getStatus());
        $newTitle = TestBuilder::faker()->sha1;
        $subcollection->getCollection()->setTitle($newTitle);
        $state = $subcollection->update($subcollection->getCollection());

        $this->assertNotNull($state->ifSuccessful());
        $this->assertEquals(HttpStatus::NO_CONTENT, $state->getStatus());
        $collectionTest = $subcollection->getCollection();
        $this->assertNotNull($collectionTest);
        // Read the subcollection based off the ID (title change has no impact on reloading this)
        $subcollection = $collection->readSubcollections()->get()->readCollection($collectionTest, $this->createCacheBreakerQueryParam());
        $this->assertEquals($newTitle, $subcollection->getCollection()->getTitle());
    }

    /**
     * @vcr SourceBoxTests/testDeleteSourceDescriptionsFromAUserDefinedCollection.json
     * @link https://familysearch.org/developers/docs/api/sources/Delete_Source_Descriptions_from_a_User-Defined_Collection_usecase
     */
    public function testDeleteSourceDescriptionsFromAUserDefinedCollection()
    {
        $factory = new FamilySearchStateFactory();
        /** @var FamilySearchCollectionState $collection */
        $collection = $this->collectionState($factory, "https://sandbox.familysearch.org/platform/collections/sources");

        $sd = SourceBuilder::newSource();
        $description = $collection->addSourceDescription($sd);
        $this->assertEquals(HttpStatus::CREATED, $description->getStatus());
        $state = $description->delete();

        $this->assertNotNull($state->ifSuccessful());
        $this->assertEquals(HttpStatus::NO_CONTENT, $state->getStatus());
        $description = $description->get();
        $this->assertEquals(HttpStatus::NOT_FOUND, $description->getStatus());
    }

    /**
     * @vcr SourceBoxTests/testDeleteUserDefinedCollection.json
     * @link https://familysearch.org/developers/docs/api/sources/Delete_User-Defined_Collection_usecase
     */
    public function testDeleteUserDefinedCollection()
    {
        $factory = new FamilySearchStateFactory();
        /** @var FamilySearchCollectionState $collection */
        $collection = $this->collectionState($factory, "https://sandbox.familysearch.org/platform/collections/sources");
        $c = new Collection();
        $c->setTitle(TestBuilder::faker()->sha1);
        $subcollection = $collection->addCollection($c);
        $this->assertEquals(HttpStatus::CREATED, $subcollection->getStatus());
        $subcollection = $subcollection->get();
        $this->assertEquals(HttpStatus::OK, $subcollection->getStatus());
        /** @var GedcomxApplicationState $state */
        $state = $subcollection->delete();

        $this->assertNotNull($state->ifSuccessful());
        $this->assertEquals(HttpStatus::NO_CONTENT, $state->getStatus());
        $subcollection = $subcollection->get($this->createCacheBreakerQueryParam());
        $this->assertEquals(HttpStatus::NOT_FOUND, $subcollection->getStatus());
    }

    /**
     * @vcr SourceBoxTests/testMoveSourcesToAUserDefinedCollection.json
     * @link https://familysearch.org/developers/docs/api/sources/Move_Sources_to_a_User-Defined_Collection_usecase
     */
    public function testMoveSourcesToAUserDefinedCollection()
    {
        $factory = new FamilySearchStateFactory();
        /** @var FamilySearchCollectionState $collection */
        $collection = $this->collectionState($factory, "https://sandbox.familysearch.org/platform/collections/sources");

        $sd = SourceBuilder::newSource();

        /** @var FamilySearchSourceDescriptionState $description */
        $description = $collection->addSourceDescription($sd);
        $this->queueForDelete($description);
        $this->assertEquals(HttpStatus::CREATED, $description->getStatus());
        $description = $description->get();
        $this->assertEquals(HttpStatus::OK, $description->getStatus());

        /** @var CollectionState $subcollection */
        $c = new Collection();
        $c->setTitle(TestBuilder::faker()->sha1);
        $subcollection = $collection->addCollection($c)->get();
        $this->queueForDelete($subcollection);

        /** @var FamilySearchSourceDescriptionState $state */
        $state = $description->moveToCollection($subcollection);

        $this->assertNotNull($state->ifSuccessful());
        $this->assertEquals(HttpStatus::NO_CONTENT, $state->getStatus());

        // Ensure it doesn't exist in the old collection
        $rootCollection = $this->getRootCollection($collection);
        $found = $this->findInCollection($description->getSourceDescription(), $rootCollection->readSourceDescriptions()->getEntity()->getSourceDescriptions());
        $this->assertFalse($found);

        // Ensure it exists in the new collection
        $found = $this->findInCollection($description->getSourceDescription(), $subcollection->readSourceDescriptions()->getEntity()->getSourceDescriptions());
        $this->assertTrue($found);
    }

    private function getRootCollection(FamilySearchCollectionState $collection){
        $subcollections = $collection->readSubcollections();
        $rootUserCollection = null;
        /** @var Collection $c */
        foreach ($subcollections->getEntity()->getCollections() as $c) {
            if (!$c->getTitle()) {
                $rootUserCollection = $c;
                break;
            }
        }

        $this->assertNotNull($rootUserCollection);
        // Get the root collection
        $subcollection = $subcollections->readCollection($rootUserCollection);
        $this->assertEquals(HttpStatus::OK, $subcollection->getStatus());
        return $subcollection;
    }

    /**
     * @param \Gedcomx\Source\SourceDescription   $needle
     * @param \Gedcomx\Source\SourceDescription[] $haystack
     *
     * @return bool
     */
    private function findInCollection($needle, $haystack){
        $found = false;

        foreach($haystack as $sourceDescription){
            if ($sourceDescription->getId() == $needle->getId()){
                $found = true;
                break;
            }
        }

        return $found;
    }
}
