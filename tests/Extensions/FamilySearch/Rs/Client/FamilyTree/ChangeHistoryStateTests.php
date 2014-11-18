<?php

namespace Gedcomx\tests\Extensions\FamilySearch\Rs\Client\FamilyTree;

use Gedcomx\Common\Attribution;
use Gedcomx\Conclusion\DateInfo;
use Gedcomx\Conclusion\Fact;
use Gedcomx\Conclusion\PlaceReference;
use Gedcomx\Extensions\FamilySearch\Rs\Client\FamilyTree\FamilyTreePersonState;
use Gedcomx\Extensions\FamilySearch\Rs\Client\FamilyTree\FamilyTreeRelationshipState;
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

    public function testReadPersonChangeHistoryFirstPage()
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

    public function testReadCoupleRelationshipChangeHistory()
    {
        $factory = new FamilyTreeStateFactory();
        $this->collectionState($factory);

        /** @var FamilyTreePersonState $husband */
        $husband = $this->createPerson('male')->get();
        $wife = $this->createPerson('female');
        /** @var FamilyTreeRelationshipState $relationship */
        $relationship = $husband->addSpouse($wife)->get();

        $fact = new Fact();
        $attribution = new Attribution();
        $attribution->setChangeMessage("Change message");
        $fact->setType("http://gedcomx.org/Marriage");
        $fact->setAttribution($attribution);
        $date = new DateInfo();
        $date->setOriginal("3 Apr 1930");
        $date->setFormal("+1930");
        $fact->setDate($date);
        $place = new PlaceReference();
        $place->setOriginal("Moscow, Russia");
        $fact->setPlace($place);

        $relationship->addFact($fact);
        $state = $relationship->readChangeHistory();

        $this->assertNotNull($state->ifSuccessful());
        $this->assertEquals((int)$state->getResponse()->getStatusCode(), 200);
        $this->assertNotNull($state->getPage());
        $this->assertNotNull($state->getPage()->getEntries());
        $this->assertGreaterThan(0, count($state->getPage()->getEntries()));

        $husband->delete();
        $wife->delete();
    }
}