<?php

namespace Gedcomx\tests\Extensions\FamilySearch\Rs\Client\FamilyTree;

use Gedcomx\Common\Attribution;
use Gedcomx\Conclusion\DateInfo;
use Gedcomx\Conclusion\Fact;
use Gedcomx\Conclusion\PlaceReference;
use Gedcomx\Conclusion\Relationship;
use Gedcomx\Extensions\FamilySearch\Platform\Tree\ChildAndParentsRelationship;
use Gedcomx\Extensions\FamilySearch\Rs\Client\FamilyTree\ChangeHistoryState;
use Gedcomx\Extensions\FamilySearch\Rs\Client\FamilyTree\ChildAndParentsRelationshipState;
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

    public function testReadChildAndParentsRelationshipChangeHistory()
    {
        $factory = new FamilyTreeStateFactory();
        $collection = $this->collectionState($factory);

        /** @var FamilyTreePersonState $father */
        $father = $this->createPerson('male')->get();
        /** @var FamilyTreePersonState $mother */
        $mother = $this->createPerson('female');
        /** @var FamilyTreePersonState $son */
        $son = $this->createPerson('male');
        $chap = new ChildAndParentsRelationship();
        $chap->setFather($father->getResourceReference());
        $chap->setMother($mother->getResourceReference());
        $chap->setChild($son->getResourceReference());
        /** @var ChildAndParentsRelationshipState $relationship */
        $relationship = $collection->addChildAndParentsRelationship($chap)->get();
        /** @var ChangeHistoryState $state */
        $state = $relationship->readChangeHistory();

        $this->assertNotNull($state->ifSuccessful());
        $this->assertEquals((int)$state->getResponse()->getStatusCode(), 200);
        $this->assertNotNull($state->getEntity());
        $this->assertNotNull($state->getEntity()->getEntries());
        $this->assertGreaterThan(0, $state->getEntity()->getEntries());

        $father->delete();
        $mother->delete();
        $son->delete();
    }
}