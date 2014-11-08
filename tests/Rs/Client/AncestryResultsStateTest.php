<?php

namespace Gedcomx\Tests\Rs\Client;

use Gedcomx\Extensions\FamilySearch\Platform\Tree\ChildAndParentsRelationship;
use Gedcomx\Extensions\FamilySearch\Rs\Client\FamilyTree\FamilyTreeCollectionState;
use Gedcomx\Extensions\FamilySearch\Rs\Client\FamilyTree\FamilyTreeStateFactory;
use Gedcomx\Rs\Client\StateFactory;
use Gedcomx\Tests\ApiTestCase;

class AncestryResultsStateTest extends ApiTestCase{
    public function testReadPersonAncestry(){
        //$factory = new StateFactory();
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
}