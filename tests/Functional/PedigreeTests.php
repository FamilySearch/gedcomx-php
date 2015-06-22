<?php 

namespace Gedcomx\Tests\Functional;

use Gedcomx\Common\Attribution;
use Gedcomx\Conclusion\DateInfo;
use Gedcomx\Conclusion\Fact;
use Gedcomx\Conclusion\PlaceReference;
use Gedcomx\Extensions\FamilySearch\Platform\Tree\ChildAndParentsRelationship;
use Gedcomx\Extensions\FamilySearch\Rs\Client\FamilyTree\FamilyTreeStateFactory;
use Gedcomx\Rs\Client\Util\HttpStatus;
use Gedcomx\Tests\ApiTestCase;
use Gedcomx\Rs\Client\Options\QueryParameter;
use Gedcomx\Tests\TestBuilder;

class PedigreeTests extends ApiTestCase
{
    public function setUp(){
        parent::setUp();
        $this->faker->seed(58641336547);
        TestBuilder::seed(58641336547);
    }
    
    /**
     * @vcr PedigreeTests/testReadPersonAncestry
     * @link https://familysearch.org/developers/docs/api/tree/Read_Person_Ancestry_usecase
     */
    public function testReadPersonAncestry()
    {
        $factory = new FamilyTreeStateFactory();
        $collection = $this->collectionState($factory);

        $grandfather = $this->createPerson('male');
        $this->assertEquals(HttpStatus::CREATED, $grandfather->getResponse()->getStatusCode());
        $grandfather = $grandfather->get();
        $this->assertEquals(HttpStatus::OK, $grandfather->getResponse()->getStatusCode());
        $father = $this->createPerson('male');
        $this->assertEquals(HttpStatus::CREATED, $father->getResponse()->getStatusCode());
        $father = $father->get();
        $this->assertEquals(HttpStatus::OK, $father->getResponse()->getStatusCode());
        $son = $this->createPerson('male');
        $this->assertEquals(HttpStatus::CREATED, $son->getResponse()->getStatusCode());
        $son = $son->get();
        $this->assertEquals(HttpStatus::OK, $son->getResponse()->getStatusCode());
        $relationship1 = new ChildAndParentsRelationship();
        $relationship1->setFather($grandfather->getResourceReference());
        $relationship1->setChild($father->getResourceReference());
        $r1 = $collection->addChildAndParentsRelationship($relationship1);
        $this->queueForDelete($r1);
        $this->assertEquals(HttpStatus::CREATED, $r1->getResponse()->getStatusCode());

        $relationship2 = new ChildAndParentsRelationship();
        $relationship2->setFather($father->getResourceReference());
        $relationship2->setChild($son->getResourceReference());
        $r2 = $collection->addChildAndParentsRelationship($relationship2);
        $this->queueForDelete($r2);
        $this->assertEquals(HttpStatus::CREATED, $r2->getResponse()->getStatusCode());

        $son = $collection->readPersonById($son->getPerson()->getId());
        $this->assertEquals(HttpStatus::OK, $son->getResponse()->getStatusCode());
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
    }

    /**
     * @vcr PedigreeTests/testReadPersonAncestryAndAdditionalPersonDetails
     * @link https://familysearch.org/developers/docs/api/tree/Read_Person_Ancestry_and_additional_person_details_usecase
     */
    public function testReadPersonAncestryAndAdditionalPersonDetails()
    {
        $factory = new FamilyTreeStateFactory();
        $collection = $this->collectionState($factory);

        $grandfather = $this->createPerson('male');
        $this->assertEquals(HttpStatus::CREATED, $grandfather->getResponse()->getStatusCode());
        $father = $this->createPerson('male');
        $this->assertEquals(HttpStatus::CREATED, $father->getResponse()->getStatusCode());
        $son = $this->createPerson('male');
        $this->assertEquals(HttpStatus::CREATED, $son->getResponse()->getStatusCode());
        $son = $son->get();
        $this->assertEquals(HttpStatus::OK, $son->getResponse()->getStatusCode());
        $relationship1 = new ChildAndParentsRelationship();
        $relationship1->setFather($grandfather->getResourceReference());
        $relationship1->setChild($father->getResourceReference());
        $r1 = $collection->addChildAndParentsRelationship($relationship1);
        $this->queueForDelete($r1);
        $this->assertEquals(HttpStatus::CREATED, $r1->getResponse()->getStatusCode());

        $relationship2 = new ChildAndParentsRelationship();
        $relationship2->setFather($father->getResourceReference());
        $relationship2->setChild($son->getResourceReference());
        $r2 = $collection->addChildAndParentsRelationship($relationship2);
        $this->queueForDelete($r2);
        $this->assertEquals(HttpStatus::CREATED, $r2->getResponse()->getStatusCode());

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
    }

    /**
     * @vcr PedigreeTests/testReadPersonAncestryWithSpecifiedSpouse
     * @link https://familysearch.org/developers/docs/api/tree/Read_Person_Ancestry_with_Specified_Spouse_usecase
     */
    public function testReadPersonAncestryWithSpecifiedSpouse()
    {
        $factory = new FamilyTreeStateFactory();
        $collection = $this->collectionState($factory);

        $grandfather = $this->createPerson('male');
        $this->assertEquals(HttpStatus::CREATED, $grandfather->getResponse()->getStatusCode());
        $father = $this->createPerson('male');
        $this->assertEquals(HttpStatus::CREATED, $father->getResponse()->getStatusCode());
        $husband = $this->createPerson('male');
        $this->assertEquals(HttpStatus::CREATED, $husband->getResponse()->getStatusCode());
        $husband = $husband->get();
        $this->assertEquals(HttpStatus::OK, $husband->getResponse()->getStatusCode());
        $wife = $this->createPerson('female');
        $this->assertEquals(HttpStatus::CREATED, $wife->getResponse()->getStatusCode());
        $wife = $wife->get();
        $this->assertEquals(HttpStatus::OK, $wife->getResponse()->getStatusCode());
        $husband->addSpouse($wife);
        $relationship1 = new ChildAndParentsRelationship();
        $relationship1->setFather($grandfather->getResourceReference());
        $relationship1->setChild($father->getResourceReference());
        $r1 = $collection->addChildAndParentsRelationship($relationship1);
        $this->queueForDelete($r1);
        $this->assertEquals(HttpStatus::CREATED, $r1->getResponse()->getStatusCode());

        $relationship2 = new ChildAndParentsRelationship();
        $relationship2->setFather($father->getResourceReference());
        $relationship2->setChild($husband->getResourceReference());
        $r2 = $collection->addChildAndParentsRelationship($relationship2);
        $this->queueForDelete($r2);
        $this->assertEquals(HttpStatus::CREATED, $r2->getResponse()->getStatusCode());

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
    }

    /**
     * @vcr PedigreeTests/testReadPersonAncestryWithSpecifiedSpouseAndAdditionalPersonAndMarriageDetails
     * @link https://familysearch.org/developers/docs/api/tree/Read_Person_Ancestry_with_Specified_Spouse_and_additional_person_and_marriage_details_usecase
     */
    public function testReadPersonAncestryWithSpecifiedSpouseAndAdditionalPersonAndMarriageDetails()
    {
        $factory = new FamilyTreeStateFactory();
        $collection = $this->collectionState($factory);

        $grandfather = $this->createPerson('male');
        $this->assertEquals(HttpStatus::CREATED, $grandfather->getResponse()->getStatusCode());
        $father = $this->createPerson('male');
        $this->assertEquals(HttpStatus::CREATED, $father->getResponse()->getStatusCode());
        $husband = $this->createPerson('male');
        $this->assertEquals(HttpStatus::CREATED, $husband->getResponse()->getStatusCode());
        $husband = $husband->get();
        $this->assertEquals(HttpStatus::OK, $husband->getResponse()->getStatusCode());
        $wife = $this->createPerson('female');
        $this->assertEquals(HttpStatus::CREATED, $wife->getResponse()->getStatusCode());
        $wife = $wife->get();
        $this->assertEquals(HttpStatus::OK, $wife->getResponse()->getStatusCode());

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
        $r1 = $collection->addChildAndParentsRelationship($relationship1);
        $this->queueForDelete($r1);
        $this->assertEquals(HttpStatus::CREATED, $r1->getResponse()->getStatusCode());

        $relationship2 = new ChildAndParentsRelationship();
        $relationship2->setFather($father->getResourceReference());
        $relationship2->setChild($husband->getResourceReference());
        $r2 = $collection->addChildAndParentsRelationship($relationship2);
        $this->queueForDelete($r2);
        $this->assertEquals(HttpStatus::CREATED, $r2->getResponse()->getStatusCode());

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
    }

    /**
     * @vcr PedigreeTests/testReadPersonDescendancy
     * @link https://familysearch.org/developers/docs/api/tree/Read_Person_Descendancy_usecase
     * @throws \Gedcomx\Rs\Client\Exception\GedcomxApplicationException
     */
    public function testReadPersonDescendancy()
    {
        $factory = new FamilyTreeStateFactory();
        $collection = $this->collectionState($factory);

        $father = $this->createPerson('male');
        $this->assertEquals(HttpStatus::CREATED, $father->getResponse()->getStatusCode());
        $father = $father->get();
        $this->assertEquals(HttpStatus::OK, $father->getResponse()->getStatusCode());
        $son = $this->createPerson('male');
        $this->assertEquals(HttpStatus::CREATED, $son->getResponse()->getStatusCode());
        $relationship1 = new ChildAndParentsRelationship();
        $relationship1->setFather($father->getResourceReference());
        $relationship1->setChild($son->getResourceReference());
        $r1 = $collection->addChildAndParentsRelationship($relationship1);
        $this->queueForDelete($r1);
        $this->assertEquals(HttpStatus::CREATED, $r1->getResponse()->getStatusCode());
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
    }

    /**
     * @vcr PedigreeTests/testReadPersonDescendancyAndAdditionalPersonAndMarriageDetails
     * @link https://familysearch.org/developers/docs/api/tree/Read_Person_Descendancy_and_additional_person_and_marriage_details_usecase
     * @throws \Gedcomx\Rs\Client\Exception\GedcomxApplicationException
     */
    public function testReadPersonDescendancyAndAdditionalPersonAndMarriageDetails()
    {
        $factory = new FamilyTreeStateFactory();
        $collection = $this->collectionState($factory);

        $father = $this->createPerson('male');
        $this->assertEquals(HttpStatus::CREATED, $father->getResponse()->getStatusCode());
        $father = $father->get();
        $this->assertEquals(HttpStatus::OK, $father->getResponse()->getStatusCode());
        $mother = $this->createPerson('female');
        $this->assertEquals(HttpStatus::CREATED, $mother->getResponse()->getStatusCode());
        $son = $this->createPerson('male');
        $this->assertEquals(HttpStatus::CREATED, $son->getResponse()->getStatusCode());

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
        $r1 = $collection->addChildAndParentsRelationship($relationship);
        $this->queueForDelete($r1);
        $this->assertEquals(HttpStatus::CREATED, $r1->getResponse()->getStatusCode());

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
    }

    /**
     * @vcr PedigreeTests/testReadPersonDescendancyWithSpecifiedSpouse
     * @link https://familysearch.org/developers/docs/api/tree/Read_Person_Descendancy_with_Specified_Spouse_usecase
     * @throws \Gedcomx\Rs\Client\Exception\GedcomxApplicationException
     */
    public function testReadPersonDescendancyWithSpecifiedSpouse()
    {
        $factory = new FamilyTreeStateFactory();
        $collection = $this->collectionState($factory);

        $father = $this->createPerson('male');
        $this->assertEquals(HttpStatus::CREATED, $father->getResponse()->getStatusCode());
        $father = $father->get();
        $this->assertEquals(HttpStatus::OK, $father->getResponse()->getStatusCode());
        $mother = $this->createPerson('female');
        $this->assertEquals(HttpStatus::CREATED, $mother->getResponse()->getStatusCode());
        $son = $this->createPerson('male');
        $this->assertEquals(HttpStatus::CREATED, $son->getResponse()->getStatusCode());

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
        $r1 = $collection->addChildAndParentsRelationship($relationship);
        $this->queueForDelete($r1);
        $this->assertEquals(HttpStatus::CREATED, $r1->getResponse()->getStatusCode());
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
    }

    /**
     * @vcr PedigreeTests/testReadPersonDescendancyWithSpecifiedSpouseAndAdditionalPersonAndMarriageDetails
     * @link https://familysearch.org/developers/docs/api/tree/Read_Person_Descendancy_with_Specified_Spouse_and_additional_person_and_marriage_details_usecase
     * @throws \Gedcomx\Rs\Client\Exception\GedcomxApplicationException
     */
    public function testReadPersonDescendancyWithSpecifiedSpouseAndAdditionalPersonAndMarriageDetails()
    {
        $factory = new FamilyTreeStateFactory();
        $collection = $this->collectionState($factory);

        $father = $this->createPerson('male');
        $this->assertEquals(HttpStatus::CREATED, $father->getResponse()->getStatusCode());
        $father = $father->get();
        $this->assertEquals(HttpStatus::OK, $father->getResponse()->getStatusCode());
        $mother = $this->createPerson('female');
        $this->assertEquals(HttpStatus::CREATED, $mother->getResponse()->getStatusCode());
        $son = $this->createPerson('male');
        $this->assertEquals(HttpStatus::CREATED, $son->getResponse()->getStatusCode());

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
        $r1 = $collection->addChildAndParentsRelationship($relationship);
        $this->queueForDelete($r1);
        $this->assertEquals(HttpStatus::CREATED, $r1->getResponse()->getStatusCode());

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
    }
}