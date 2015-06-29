<?php

namespace Gedcomx\Tests\Functional;

use Gedcomx\Common\Attribution;
use Gedcomx\Conclusion\DateInfo;
use Gedcomx\Conclusion\Fact;
use Gedcomx\Conclusion\PlaceReference;
use Gedcomx\Extensions\FamilySearch\Platform\Tree\ChildAndParentsRelationship;
use Gedcomx\Extensions\FamilySearch\Rs\Client\FamilyTree\ChangeHistoryState;
use Gedcomx\Extensions\FamilySearch\Rs\Client\FamilyTree\ChildAndParentsRelationshipState;
use Gedcomx\Extensions\FamilySearch\Rs\Client\FamilyTree\FamilyTreePersonState;
use Gedcomx\Extensions\FamilySearch\Rs\Client\FamilyTree\FamilyTreeRelationshipState;
use Gedcomx\Extensions\FamilySearch\Rs\Client\FamilyTree\FamilyTreeStateFactory;
use Gedcomx\Extensions\FamilySearch\Rs\Client\Util\ChangeEntry;
use Gedcomx\Rs\Client\Options\QueryParameter;
use Gedcomx\Rs\Client\Util\HttpStatus;
use Gedcomx\Tests\ApiTestCase;

class ChangeHistoryTests extends ApiTestCase
{
    
    /**
     * @vcr ChangeHistory/testReadPersonChangeHistory.json
     */
    public function testReadPersonChangeHistory()
    {
        $factory = new FamilyTreeStateFactory();
        $this->collectionState($factory);

        $person = $this->createPerson();
        $this->assertEquals(HttpStatus::CREATED, $person->getResponse()->getStatusCode());
        $person = $person->get();
        $this->assertEquals(HttpStatus::OK, $person->getResponse()->getStatusCode());
        $state = $person->readChangeHistory();

        $this->assertNotNull($state->ifSuccessful());
        $this->assertEquals((int)$state->getResponse()->getStatusCode(), 200);
        $this->assertNotNull($state->getPage());
        $this->assertNotNull($state->getPage()->getEntries());
        $this->assertGreaterThanOrEqual(1, count($state->getPage()->getEntries()));
    }

    /**
     * @vcr ChangeHistory/testReadPersonChangeHistoryFirstPage.json
     */
    public function testReadPersonChangeHistoryFirstPage()
    {
        $factory = new FamilyTreeStateFactory();
        $this->collectionState($factory);

        $person = $this->createPerson();
        $this->assertEquals(HttpStatus::CREATED, $person->getResponse()->getStatusCode());
        $person = $person->get();
        $this->assertEquals(HttpStatus::OK, $person->getResponse()->getStatusCode());
        $state = $person->readChangeHistory(QueryParameter::count(10));

        $this->assertNotNull($state->ifSuccessful());
        $this->assertEquals((int)$state->getResponse()->getStatusCode(), 200);
        $this->assertNotNull($state->getPage());
        $this->assertNotNull($state->getPage()->getEntries());
        $this->assertGreaterThanOrEqual(1, count($state->getPage()->getEntries()));
    }

    /**
     * @vcr ChangeHistory/testReadCoupleRelationshipChangeHistory.json
     */
    public function testReadCoupleRelationshipChangeHistory()
    {
        $factory = new FamilyTreeStateFactory();
        $this->collectionState($factory);

        /** @var FamilyTreePersonState $husband */
        $husband = $this->createPerson('male')->get();
        $wife = $this->createPerson('female');
        /** @var FamilyTreeRelationshipState $relationship */
        $relationship = $husband->addSpouse($wife)->get();
        $this->queueForDelete($relationship);

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
    }

    /**
     * @vcr ChangeHistory/testReadChildAndParentsRelationshipChangeHistory.json
     */
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
        $this->queueForDelete($relationship);

        /** @var ChangeHistoryState $state */
        $state = $relationship->readChangeHistory();

        $this->assertNotNull($state->ifSuccessful());
        $this->assertEquals((int)$state->getResponse()->getStatusCode(), 200);
        $this->assertNotNull($state->getEntity());
        $this->assertNotNull($state->getEntity()->getEntries());
        $this->assertGreaterThan(0, $state->getEntity()->getEntries());
    }

    /**
     * @vcr ChangeHistory/testRestoreChangeAction.json
     */
    public function testRestoreChangeAction()
    {
        $factory = new FamilyTreeStateFactory();
        $this->collectionState($factory);

        /** @var FamilyTreePersonState $person */
        $person = $this->createPerson('male');
        $this->assertEquals(HttpStatus::CREATED, $person->getResponse()->getStatusCode());
        $person = $person->get();
        $this->assertEquals(HttpStatus::OK, $person->getResponse()->getStatusCode());
        $this->assertNotNull($person->getPerson());
        $this->assertNotNull($person->getPerson()->getFacts());
        $this->assertGreaterThan(0, $person->getPerson()->getFacts());
        $facts = $person->getPerson()->getFacts();
        $person->deleteFact(array_shift($facts));
        $changes = $person->readChangeHistory();
        $this->assertEquals(HttpStatus::OK, $changes->getResponse()->getStatusCode());
        $deleted = null;
        /** @var ChangeEntry $entry */
        foreach ($changes->getPage()->getEntries() as $entry) {
            if ($entry->getOperation() !== null && $entry->getOperation() == "http://familysearch.org/v1/Delete") {
                $deleted = $entry;
                break;
            }
        }
        $this->assertNotNull($deleted);
        $restore = null;
        /** @var ChangeEntry $entry */
        foreach ($changes->getPage()->getEntries() as $entry) {
            if ($entry->getObjectType() != null && $entry->getObjectType() === $deleted->getObjectType() && $entry->getObjectModifier() != null & $entry->getObjectModifier() == $deleted->getObjectModifier() && $entry->getOperation() != null & $entry->getOperation() != "http://familysearch.org/v1/Delete") {
                $restore = $entry;
                break;
            }
        }
        $this->assertNotNull($restore);
        $this->assertNotNull($restore->getEntry());
        $state = $changes->restoreChange($restore->getEntry());

        $this->assertNotNull($state->ifSuccessful());
        $this->assertEquals(HttpStatus::NO_CONTENT, $state->getResponse()->getStatusCode());
    }
}
