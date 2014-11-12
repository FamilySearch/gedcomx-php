<?php

namespace Gedcomx\Tests\Rs\Client;

use Gedcomx\Common\Attribution;
use Gedcomx\Conclusion\DateInfo;
use Gedcomx\Conclusion\Fact;
use Gedcomx\Conclusion\PlaceReference;
use Gedcomx\Extensions\FamilySearch\Platform\Tree\ChildAndParentsRelationship;
use Gedcomx\Extensions\FamilySearch\Rs\Client\FamilyTree\FamilyTreeStateFactory;
use Gedcomx\Tests\ApiTestCase;
use Gedcomx\Rs\Client\Options\QueryParameter;

class DescendancyResultsStateTests extends ApiTestCase
{
    public function testReadPersonDescendancy()
    {
        $factory = new FamilyTreeStateFactory();
        $collection = $this->collectionState($factory);

        $father = $this->createPerson('male')->get();
        $son = $this->createPerson('male');
        $relationship1 = new ChildAndParentsRelationship();
        $relationship1->setFather($father->getResourceReference());
        $relationship1->setChild($son->getResourceReference());
        $collection->addChildAndParentsRelationship($relationship1);
        $state = $father->readDescendancy();

        $this->assertNotNull($state->ifSuccessful());
        $this->assertEquals((int)$state->getResponse()->getStatusCode(), 200);
        $this->assertNotNull($state->getTree());
        $this->assertNotNull($state->getTree()->getRoot());
        $this->assertNotNull($state->getTree()->getRoot()->getPerson());
        $this->assertNotNull($state->getTree()->getRoot()->getChildren());
        $this->assertEquals(1, count($state->getTree()->getRoot()->getChildren()));
        $this->assertNotNull($state->getTree()->getRoot()->Children[0]->getPerson());
        $this->assertEquals($father->getPerson()->getId(), $state->getTree()->getRoot()->getPerson()->getId());
        $this->assertEquals($son->getHeader("X-ENTITY-ID")->__toString(), $state->getTree()->getRoot()->Children[0]->getPerson()->getId());

        $father->delete();
        $son->delete();
    }

    public function testReadPersonDescendancyAndAdditionalPersonAndMarriageDetails()
    {
        $factory = new FamilyTreeStateFactory();
        $collection = $this->collectionState($factory);

        $father = $this->createPerson('male')->get();
        $mother = $this->createPerson('female');
        $son = $this->createPerson('male');

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

        $father->addSpouse($mother)->addFact($fact);
        $relationship = new ChildAndParentsRelationship();
        $relationship->setFather($father->getResourceReference());
        $relationship->setMother($mother->getResourceReference());
        $relationship->setChild($son->getResourceReference());
        $collection->addChildAndParentsRelationship($relationship);
        $state = $father->readDescendancy(
            new QueryParameter(true, "personDetails", "true"),
            new QueryParameter(true, "marriageDetails", "true"));

        $this->assertNotNull($state->ifSuccessful());
        $this->assertEquals((int)$state->getResponse()->getStatusCode(), 200);
        $this->assertNotNull($state->getTree());
        $this->assertNotNull($state->getTree()->getRoot());
        $this->assertNotNull($state->getTree()->getRoot()->getPerson());
        $this->assertNotNull($state->getTree()->getRoot()->getSpouse());
        $this->assertNotNull($state->getTree()->getRoot()->getChildren());
        $this->assertNotNull($state->getTree()->getRoot()->getSpouse()->getDisplayExtension());
        $this->assertNotNull($state->getTree()->getRoot()->getSpouse()->getDisplayExtension()->getMarriageDate());
        $this->assertNotNull($state->getTree()->getRoot()->getPerson()->getFacts());
        $this->assertNotNull($state->getTree()->getRoot()->getSpouse()->getFacts());
        $this->assertEquals(1, count($state->getTree()->getRoot()->getChildren()));
        $this->assertNotNull($state->getTree()->getRoot()->Children[0]->getPerson());
        $this->assertEquals($father->getPerson()->getId(), $state->getTree()->getRoot()->getPerson()->getId());
        $this->assertEquals($mother->getHeader("X-ENTITY-ID")->__toString(), $state->getTree()->getRoot()->getSpouse()->getId());
        $this->assertEquals($son->getHeader("X-ENTITY-ID")->__toString(), $state->getTree()->getRoot()->Children[0]->getPerson()->getId());

        $father->delete();
        $mother->delete();
        $son->delete();
    }

    public function testReadPersonDescendancyWithSpecifiedSpouse()
    {
        $factory = new FamilyTreeStateFactory();
        $collection = $this->collectionState($factory);

        $father = $this->createPerson('male')->get();
        $mother = $this->createPerson('female');
        $son = $this->createPerson('male');

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

        $father->addSpouse($mother)->addFact($fact);
        $relationship = new ChildAndParentsRelationship();
        $relationship->setFather($father->getResourceReference());
        $relationship->setMother($mother->getResourceReference());
        $relationship->setChild($son->getResourceReference());
        $collection->addChildAndParentsRelationship($relationship);
        $state = $father->readDescendancy(new QueryParameter(true, "spouse", $mother->getHeader("X-ENTITY-ID")->__toString()));

        $this->assertNotNull($state->IfSuccessful());
        $this->assertEquals((int)$state->getResponse()->getStatusCode(), 200);
        $this->assertNotNull($state->getTree());
        $this->assertNotNull($state->getTree()->getRoot());
        $this->assertNotNull($state->getTree()->getRoot()->getPerson());
        $this->assertNotNull($state->getTree()->getRoot()->getSpouse());
        $this->assertNotNull($state->getTree()->getRoot()->getChildren());
        $this->assertEquals(1, count($state->getTree()->getRoot()->getChildren()));
        $this->assertNotNull($state->getTree()->getRoot()->Children[0]->getPerson());
        $this->assertEquals($father->getPerson()->getId(), $state->getTree()->getRoot()->getPerson()->getId());
        $this->assertEquals($mother->getHeader("X-ENTITY-ID")->__toString(), $state->getTree()->getRoot()->getSpouse()->getId());
        $this->assertEquals($son->getHeader("X-ENTITY-ID")->__toString(), $state->getTree()->getRoot()->Children[0]->getPerson()->getId());

        $father->delete();
        $mother->delete();
        $son->delete();
    }

    public function testReadPersonDescendancyWithSpecifiedSpouseAndAdditionalPersonAndMarriageDetails()
    {
        $factory = new FamilyTreeStateFactory();
        $collection = $this->collectionState($factory);

        $father = $this->createPerson('male')->get();
        $mother = $this->createPerson('female');
        $son = $this->createPerson('male');

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

        $father->addSpouse($mother)->addFact($fact);
        $relationship = new ChildAndParentsRelationship();
        $relationship->setFather($father->getResourceReference());
        $relationship->setMother($mother->getResourceReference());
        $relationship->setChild($son->getResourceReference());
        $collection->addChildAndParentsRelationship($relationship);
        $state = $father->readDescendancy(
            new QueryParameter(true, "spouse", $mother->getHeader("X-ENTITY-ID")->__toString()),
            new QueryParameter(true, "personDetails", "true"),
            new QueryParameter(true, "marriageDetails", "true"));

        $this->assertNotNull($state->ifSuccessful());
        $this->assertEquals((int)$state->getResponse()->getStatusCode(), 200);
        $this->assertNotNull($state->getTree());
        $this->assertNotNull($state->getTree()->getRoot());
        $this->assertNotNull($state->getTree()->getRoot()->getPerson());
        $this->assertNotNull($state->getTree()->getRoot()->getSpouse());
        $this->assertNotNull($state->getTree()->getRoot()->getChildren());
        $this->assertNotNull($state->getTree()->getRoot()->getSpouse()->getDisplayExtension());
        $this->assertNotNull($state->getTree()->getRoot()->getSpouse()->getDisplayExtension()->getMarriageDate());
        $this->assertNotNull($state->getTree()->getRoot()->getPerson()->getFacts());
        $this->assertNotNull($state->getTree()->getRoot()->getSpouse()->getFacts());
        $this->assertEquals(1, count($state->getTree()->getRoot()->getChildren()));
        $this->assertNotNull($state->getTree()->getRoot()->Children[0]->getPerson());
        $this->assertEquals($father->getPerson()->getId(), $state->getTree()->getRoot()->getPerson()->getId());
        $this->assertEquals($mother->getHeader("X-ENTITY-ID")->__toString(), $state->getTree()->getRoot()->getSpouse()->getId());
        $this->assertEquals($son->getHeader("X-ENTITY-ID")->__toString(), $state->getTree()->getRoot()->Children[0]->getPerson()->getId());

        $father->delete();
        $mother->delete();
        $son->delete();
    }
}