<?php

namespace Gedcomx\Tests\Rs\Client;

use Gedcomx\Common\Attribution;
use Gedcomx\Conclusion\DateInfo;
use Gedcomx\Conclusion\Fact;
use Gedcomx\Conclusion\PlaceReference;
use Gedcomx\Extensions\FamilySearch\Platform\Tree\ChildAndParentsRelationship;
use Gedcomx\Extensions\FamilySearch\Rs\Client\FamilyTree\FamilyTreeCollectionState;
use Gedcomx\Extensions\FamilySearch\Rs\Client\FamilyTree\FamilyTreeStateFactory;
use Gedcomx\Rs\Client\Options\QueryParameter;
use Gedcomx\Rs\Client\StateFactory;
use Gedcomx\Tests\ApiTestCase;

class AncestryResultsStateTest extends ApiTestCase
{
    public function testReadPersonAncestry()
    {
        $factory = new FamilyTreeStateFactory();
        $collection = $this->collectionState($factory);

        $grandfather = $this->createPerson('male')->get();
        $father = $this->createPerson('male')->get();
        $son = $this->createPerson('male')->get();
        $relationship1 = new ChildAndParentsRelationship();
        $relationship1->setFather($grandfather->getResourceReference());
        $relationship1->setChild($father->getResourceReference());
        $collection->addChildAndParentsRelationship($relationship1);
        $relationship2 = new ChildAndParentsRelationship();
        $relationship2->setFather($father->getResourceReference());
        $relationship2->setChild($son->getResourceReference());
        $collection->addChildAndParentsRelationship($relationship2);
        $son = $collection->readPersonById($son->getPerson()->getId());
        $state = $son->readAncestry();

        $this->assertNotNull($state->ifSuccessful());
        $this->assertEquals((int)$state->getResponse()->getStatusCode(), 200);
        $this->assertNotNull($state->getTree());
        $this->assertNotNull($state->getTree()->getRoot());
        $this->assertNotNull($state->getTree()->getRoot()->getPerson());
        $this->assertNotNull($state->getTree()->getRoot()->getFather());
        $this->assertNotNull($state->getTree()->getRoot()->getFather()->getPerson());
        $this->assertNotNull($state->getTree()->getRoot()->getFather()->getFather());
        $this->assertNotNull($state->getTree()->getRoot()->getFather()->getFather()->getPerson());
        $this->assertEquals($grandfather->getSelfUri(), $state->getTree()->getRoot()->getFather()->getFather()->getPerson()->getLink("self")->getHref());
        $this->assertEquals($father->getSelfUri(), $state->getTree()->getRoot()->getFather()->getPerson()->getLink("self")->getHref());
        $this->assertEquals($son->getSelfUri(), $state->getTree()->getRoot()->getPerson()->getLink("self")->getHref());

        $grandfather->delete();
        $father->delete();
        $son->delete();
    }

    public function testReadPersonAncestryAndAdditionalPersonDetails()
    {
        $factory = new FamilyTreeStateFactory();
        $collection = $this->collectionState($factory);

        $grandfather = $this->createPerson('male');
        $father = $this->createPerson('male');
        $son = $this->createPerson('male')->get();
        $relationship1 = new ChildAndParentsRelationship();
        $relationship1->setFather($grandfather->getResourceReference());
        $relationship1->setChild($father->getResourceReference());
        $collection->addChildAndParentsRelationship($relationship1);
        $relationship2 = new ChildAndParentsRelationship();
        $relationship2->setFather($father->getResourceReference());
        $relationship2->setChild($son->getResourceReference());
        $collection->addChildAndParentsRelationship($relationship2);
        $son = $collection->readPersonById($son->getPerson()->getId());
        $state = $son->readAncestry(new QueryParameter(true, "personDetails", "true"));

        $this->assertNotNull($state->IfSuccessful());
        $this->assertEquals((int)$state->getResponse()->getStatusCode(), 200);
        $this->assertNotNull($state->getTree());
        $this->assertNotNull($state->getTree()->getRoot());
        $this->assertNotNull($state->getTree()->getRoot()->getPerson());
        $this->assertNotNull($state->getTree()->getRoot()->getFather());
        $this->assertNotNull($state->getTree()->getRoot()->getFather()->getPerson());
        $this->assertNotNull($state->getTree()->getRoot()->getFather()->getFather());
        $this->assertNotNull($state->getTree()->getRoot()->getFather()->getFather()->getPerson());
        $this->assertNotNull($state->getTree()->getRoot()->getPerson()->getFacts());
        $this->assertNotNull($state->getTree()->getRoot()->getFather()->getPerson()->getFacts());
        $this->assertNotNull($state->getTree()->getRoot()->getFather()->getFather()->getPerson()->getFacts());
        $this->assertEquals($grandfather->getSelfUri(), $state->getTree()->getRoot()->getFather()->getFather()->getPerson()->getLink("self")->getHref());
        $this->assertEquals($father->getSelfUri(), $state->getTree()->getRoot()->getFather()->getPerson()->getLink("self")->getHref());
        $this->assertEquals($son->getSelfUri(), $state->getTree()->getRoot()->getPerson()->getLink("self")->getHref());

        $grandfather->delete();
        $father->delete();
        $son->delete();
    }

    public function testReadPersonAncestryWithSpecifiedSpouse()
    {
        $factory = new FamilyTreeStateFactory();
        $collection = $this->collectionState($factory);

        $grandfather = $this->createPerson('male');
        $father = $this->createPerson('male');
        $husband = $this->createPerson('male')->get();
        $wife = $this->createPerson('female')->get();
        $husband->addSpouse($wife);
        $relationship1 = new ChildAndParentsRelationship();
        $relationship1->setFather($grandfather->getResourceReference());
        $relationship1->setChild($father->getResourceReference());
        $collection->addChildAndParentsRelationship($relationship1);
        $relationship2 = new ChildAndParentsRelationship();
        $relationship2->setFather($father->getResourceReference());
        $relationship2->setChild($husband->getResourceReference());
        $collection->addChildAndParentsRelationship($relationship2);
        $husband = $collection->readPersonById($husband->getPerson()->getId());
        $state = $husband->readAncestry(new QueryParameter(true, "spouse", $wife->getPerson()->getId()));

        $this->assertNotNull($state->ifSuccessful());
        $this->assertEquals((int)$state->getResponse()->getStatusCode(), 200);
        $this->assertNotNull($state->getTree());
        $this->assertNotNull($state->getTree()->getRoot());
        $this->assertNotNull($state->getTree()->getRoot()->getMother());
        $this->assertNotNull($state->getTree()->getRoot()->getMother()->getPerson());
        $this->assertNotNull($state->getTree()->getRoot()->getFather());
        $this->assertNotNull($state->getTree()->getRoot()->getFather()->getPerson());
        $this->assertNotNull($state->getTree()->getRoot()->getFather()->getFather());
        $this->assertNotNull($state->getTree()->getRoot()->getFather()->getFather()->getPerson());
        $this->assertNotNull($state->getTree()->getRoot()->getFather()->getFather()->getFather());
        $this->assertNotNull($state->getTree()->getRoot()->getFather()->getFather()->getFather()->getPerson());
        $this->assertEquals($grandfather->getSelfUri(), $state->getTree()->getRoot()->getFather()->getFather()->getFather()->getPerson()->getLink("self")->getHref());
        $this->assertEquals($father->getSelfUri(), $state->getTree()->getRoot()->getFather()->getFather()->getPerson()->getLink("self")->getHref());
        $this->assertEquals($husband->getSelfUri(), $state->getTree()->getRoot()->getFather()->getPerson()->getLink("self")->getHref());
        $this->assertEquals($wife->getSelfUri(), $state->getTree()->getRoot()->getMother()->getPerson()->getLink("self")->getHref());

        $grandfather->delete();
        $father->delete();
        $husband->delete();
        $wife->delete();
    }

    public function testReadPersonAncestryWithSpecifiedSpouseAndAdditionalPersonAndMarriageDetails()
    {
        $factory = new FamilyTreeStateFactory();
        $collection = $this->collectionState($factory);

        $grandfather = $this->createPerson('male');
        $father = $this->createPerson('male');
        $husband = $this->createPerson('male')->get();
        $wife = $this->createPerson('female')->get();

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

        $husband->addSpouse($wife)->addFact($fact);
        $relationship1 = new ChildAndParentsRelationship();
        $relationship1->setFather($grandfather->getResourceReference());
        $relationship1->setChild($father->getResourceReference());
        $collection->addChildAndParentsRelationship($relationship1);
        $relationship2 = new ChildAndParentsRelationship();
        $relationship2->setFather($father->getResourceReference());
        $relationship2->setChild($husband->getResourceReference());
        $collection->addChildAndParentsRelationship($relationship2);
        $husband = $collection->readPersonById($husband->getPerson()->getId());
        $state = $husband->readAncestry(
            new QueryParameter(true, "spouse", $wife->getPerson()->getId()),
            new QueryParameter(true, "personDetails", "true"),
            new QueryParameter(true, "marriageDetails", "true"));

        $this->assertNotNull($state->ifSuccessful());
        $this->assertEquals((int)$state->getResponse()->getStatusCode(), 200);
        $this->assertNotNull($state->getTree());
        $this->assertNotNull($state->getTree()->getRoot());
        $this->assertNotNull($state->getTree()->getRoot()->getMother());
        $this->assertNotNull($state->getTree()->getRoot()->getMother()->getPerson());
        $this->assertNotNull($state->getTree()->getRoot()->getMother()->getPerson()->getDisplayExtension());
        $this->assertNotNull($state->getTree()->getRoot()->getMother()->getPerson()->getDisplayExtension()->getMarriageDate());
        $this->assertNotNull($state->getTree()->getRoot()->getFather());
        $this->assertNotNull($state->getTree()->getRoot()->getFather()->getPerson());
        $this->assertNotNull($state->getTree()->getRoot()->getFather()->getFather());
        $this->assertNotNull($state->getTree()->getRoot()->getFather()->getFather()->getPerson());
        $this->assertNotNull($state->getTree()->getRoot()->getFather()->getFather()->getFather());
        $this->assertNotNull($state->getTree()->getRoot()->getFather()->getFather()->getFather()->getPerson());
        $this->assertNotNull($state->getTree()->getRoot()->getFather()->getPerson()->getFacts());
        $this->assertNotNull($state->getTree()->getRoot()->getMother()->getPerson()->getFacts());
        $this->assertEquals($grandfather->getSelfUri(), $state->getTree()->getRoot()->getFather()->getFather()->getFather()->getPerson()->getLink("self")->getHref());
        $this->assertEquals($father->getSelfUri(), $state->getTree()->getRoot()->getFather()->getFather()->getPerson()->getLink("self")->getHref());
        $this->assertEquals($husband->getSelfUri(), $state->getTree()->getRoot()->getFather()->getPerson()->getLink("self")->getHref());
        $this->assertEquals($wife->getSelfUri(), $state->getTree()->getRoot()->getMother()->getPerson()->getLink("self")->getHref());

        $grandfather->delete();
        $father->delete();
        $husband->delete();
        $wife->delete();
    }
}