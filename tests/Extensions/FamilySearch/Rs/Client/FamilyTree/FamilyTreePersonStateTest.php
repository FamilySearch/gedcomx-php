<?php

namespace Gedcomx\tests\Extensions\FamilySearch\Rs\Client\FamilyTree;

use Gedcomx\Extensions\FamilySearch\Rs\Client\FamilyTree\FamilyTreeStateFactory;
use Gedcomx\Rs\Client\Options\QueryParameter;
use Gedcomx\Tests\ApiTestCase;
use Gedcomx\Tests\PersonBuilder;

class FamilyTreePersonStateTest extends ApiTestCase
{
    public function testReadPersonPossibleDuplicates()
    {
        $factory = new FamilyTreeStateFactory();
        $this->collectionState($factory);

        $p = PersonBuilder::buildPerson(null);
        $person1 = $this->collectionState()->addPerson($p);
        $person2 = $this->collectionState()->addPerson($p)->get();
        sleep(30); // This is to ensure the matching system on the server has time to recognize the two new duplicates
        $state = $person2->readMatches();

        $this->assertNotNull($state->ifSuccessful());
        $this->assertEquals((int)$state->getResponse()->getStatusCode(), 200);
        $this->assertGreaterThan(0, count($state->getResults()->getEntries()));

        $person1->delete();
        $person2->delete();
    }

    public function testReadPersonRecordMatches()
    {
        $factory = new FamilyTreeStateFactory();
        $this->collectionState($factory);

        $person = $this->createPerson()->get();
        $query = new QueryParameter(true, "collection", "https://familysearch.org/platform/collections/records");
        $state = $person->readMatches($query);

        $this->assertNotNull($state->ifSuccessful());
        $this->assertEquals((int)$state->getResponse()->getStatusCode(), 204);
        $this->assertNull($state->getResults());

        $person->delete();
    }

    public function testReadAllMatchStatusTypesPersonRecordMatches()
    {
        $factory = new FamilyTreeStateFactory();
        $this->collectionState($factory);

        $p = PersonBuilder::buildPerson(null);
        $person1 = $this->collectionState()->addPerson($p);
        $person2 = $this->collectionState()->addPerson($p)->get();
        sleep(30); // This is to ensure the matching system on the server has time to recognize the two new duplicates
        $statuses = new QueryParameter(true, "status", array("pending", "accepted", "rejected"));
        $state = $person2->readMatches($statuses);

        $this->assertNotNull($state->ifSuccessful());
        $this->assertEquals((int)$state->getResponse()->getStatusCode(), 200);
        $this->assertGreaterThan(0, count($state->getResults()->getEntries()));

        $person1->delete();
        $person2->delete();
    }

    public function testReadHigherConfidencePersonAcceptedRecordMatches()
    {
        $factory = new FamilyTreeStateFactory();
        $this->collectionState($factory);

        $p = PersonBuilder::buildPerson(null);
        $person1 = $this->collectionState()->addPerson($p);
        $person2 = $this->collectionState()->addPerson($p)->get();
        sleep(30); // This is to ensure the matching system on the server has time to recognize the two new duplicates
        $statuses = new QueryParameter(true, "status", "accepted");
        $confidence = new QueryParameter(true, "confidence", "4");
        $state = $person2->readMatches($statuses, $confidence);

        $this->assertNotNull($state->ifSuccessful());
        $this->assertEquals((int)$state->getResponse()->getStatusCode(), 200);
        $this->assertGreaterThan(0, count($state->getResults()->getEntries()));

        $person1->delete();
        $person2->delete();
    }

    public function testUpdateMatchStatusForPersonRecordMatches()
    {
        $factory = new FamilyTreeStateFactory();
        $this->collectionState($factory);

        $collection = new QueryParameter(true, "collection", "https://familysearch.org/platform/collections/records");
        $p = PersonBuilder::buildPerson(null);
        $person1 = $this->collectionState()->addPerson($p);
        $person2 = $this->collectionState()->addPerson($p)->get();
        sleep(30); // This is to ensure the matching system on the server has time to recognize the two new duplicates
        $matches = $person2->readMatches();
        $accepted = new QueryParameter(true, "status", "accepted");

        $state = $matches->updateMatchStatus(array_shift($matches->getResults()->getEntries()), $accepted, $collection);

        $this->assertNotNull($state->ifSuccessful());
        $this->assertEquals((int)$state->getResponse()->getStatusCode(), 204);

        $person1->delete();
        $person2->delete();
    }
}