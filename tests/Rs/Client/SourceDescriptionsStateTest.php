<?php

namespace Gedcomx\Tests\Rs\Client;

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
use Gedcomx\Rs\Client\Rel;
use Gedcomx\Rs\Client\StateFactory;
use Gedcomx\Rs\Client\Util\HttpStatus;
use Gedcomx\Source\SourceCitation;
use Gedcomx\Source\SourceDescription;
use Gedcomx\Tests\ApiTestCase;
use Gedcomx\Tests\SourceBuilder;

class SourceDescriptionsStateTest extends ApiTestCase
{

    /**
     * @link https://familysearch.org/developers/docs/api/sources/Create_Source_Description_usecase
     */
    public function testCreateSourceDescriptionCRUD()
    {
        $this->collectionState(new StateFactory());
        /** @var SourceDescription $source */
        $source = SourceBuilder::newSource();
        $link = $this->collectionState()->getLink(Rel::SOURCE_DESCRIPTIONS);
        if ($link === null || $link->getHref() === null) {
            return null;
        }

        $sourceState = $this->collectionState()->addSourceDescription($source);
        $this->assertAttributeEquals(HttpStatus::CREATED, "statusCode", $sourceState->getResponse(), $this->buildFailMessage(__METHOD__ . "(CREATE)", $sourceState));
    }

    /**
     * @link https://familysearch.org/developers/docs/api/sources/Create_User-Uploaded_Source_usecase
     */
    public function testCreateUserUploadedSource()
    {
        $this->collectionState(new StateFactory());
        /** @var SourceDescription $source */
        $source = SourceBuilder::newSource();
        //todo: create a memory once the Memories code has been implemented
        $source->setAbout('https://sandbox.familysearch.org/platform/memories/memories/103820');
    }

    public function testReadAllSourcesOfAllUserDefinedCollectionsOfASpecificUser()
    {
        $factory = new FamilySearchStateFactory();
        /** @var FamilySearchCollectionState $collection */
        $collection = $this->collectionState($factory, "https://sandbox.familysearch.org/platform/collections/sources");
        /** @var CollectionsState $subcollections */
        $subcollections = $collection->readSubcollections()->get();
        $rootUserCollection = null;
        /** @var Collection $c */
        foreach ($subcollections->getEntity()->getCollections() as $c) {
            if (!$c->getTitle()) {
                $rootUserCollection = $c;
                break;
            }
        }
        // Get the root collection
        $subcollection = $subcollections->readCollection($rootUserCollection);
        $state = $subcollection->readSourceDescriptions();

        $this->assertNotNull($state->ifSuccessful());
        $this->assertEquals(HttpStatus::OK, $state->getResponse()->getStatusCode());
        $this->assertNotNull($state->getEntity()->getCollections());
        $this->assertGreaterThan(0, count($subcollection->getEntity()->getCollections()));
    }

    public function testDeleteSourceDescriptionsFromAUserDefinedCollection()
    {
        $factory = new FamilySearchStateFactory();
        /** @var FamilySearchCollectionState $collection */
        $collection = $this->collectionState($factory, "https://sandbox.familysearch.org/platform/collections/sources");

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
        $contributor = new ResourceReference();
        $contributor->setResource("https://familysearch.org/platform/users/agents/MM6M-8QJ");
        $contributor->setResourceId("MM6M-8QJ");
        $attribution->setContributor($contributor);
        $attribution->setModified(time());
        $attribution->setChangeMessage("This is the change message");
        $sd->SetAttribution($attribution);

        $description = $collection->addSourceDescription($sd);
        $state = $description->delete();

        $this->assertNotNull($state->ifSuccessful());
        $this->assertEquals(HttpStatus::NO_CONTENT, $state->getResponse()->getStatusCode());
    }

    public function testReadAPageOfTheSourcesInAUserDefinedCollection()
    {
        $factory = new FamilySearchStateFactory();
        /** @var FamilySearchCollectionState $collection */
        $collection = $this->collectionState($factory, "https://sandbox.familysearch.org/platform/collections/sources");
        /** @var CollectionsState $subcollections */
        $subcollections = $collection->readSubcollections()->get();

        $c = array_shift($subcollections->getCollections());
        $subcollection = $subcollections->readCollection($c);
        $state = $subcollection->readSourceDescriptions();

        $this->assertNotNull($state->ifSuccessful());
        $this->assertEquals(HttpStatus::OK, $state->getResponse()->getStatusCode());
    }

    public function testMoveSourcesToAUserDefinedCollection()
    {
        $factory = new FamilySearchStateFactory();
        /** @var FamilySearchCollectionState $collection */
        $collection = $this->collectionState($factory, "https://sandbox.familysearch.org/platform/collections/sources");

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
        $contributor = new ResourceReference();
        $contributor->setResource("https://familysearch.org/platform/users/agents/MM6M-8QJ");
        $contributor->setResourceId("MM6M-8QJ");
        $attribution->setContributor($contributor);
        $attribution->setModified(time());
        $attribution->setChangeMessage("This is the change message");
        $sd->SetAttribution($attribution);

        /** @var FamilySearchSourceDescriptionState $description */
        $description = $collection->addSourceDescription($sd)->get();
        /** @var CollectionState $subcollection */
        $c = new Collection();
        $c->setTitle($this->faker->sha1);
        $subcollection = $collection->addCollection($c)->get();
        /** @var FamilySearchSourceDescriptionState $state */
        $state = $description->moveToCollection($subcollection);

        $this->assertNotNull($state->ifSuccessful());
        $this->assertEquals(HttpStatus::NO_CONTENT, $state->getResponse()->getStatusCode());

        $description->delete();
        $subcollection->delete();
    }
}