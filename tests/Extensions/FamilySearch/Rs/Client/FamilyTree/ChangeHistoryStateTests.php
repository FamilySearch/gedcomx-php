<?php

namespace Gedcomx\tests\Extensions\FamilySearch\Rs\Client\FamilyTree;

use Gedcomx\Extensions\FamilySearch\Rs\Client\FamilyTree\FamilyTreeStateFactory;
use Gedcomx\Tests\ApiTestCase;

class ChangeHistoryStateTests extends ApiTestCase
{
    public function testReadPersonChangeHistory()
    {
        $factory = new FamilyTreeStateFactory();
        $this->collectionState($factory);

        $person = $this->createPerson()->get();
        $state = $person->readChangeHistory();

        $this->assertNotNull($state->ifSuccessful());
        $this->assertEquals((int)$state->getResponse()->getStatusCode(), 200);
        $this->assertNotNull($state->getPage());
        $this->assertNotNull($state->getPage()->getEntries());
        $this->assertGreaterThanOrEqual(1, count($state->getPage()->getEntries()));

        $person->delete();
    }
}