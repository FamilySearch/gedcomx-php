<?php

namespace Gedcomx\Tests\Functional;

use Gedcomx\Conclusion\Gender;
use Gedcomx\Conclusion\Person;
use Gedcomx\Conclusion\Relationship;
use Gedcomx\Extensions\FamilySearch\Platform\Tree\ChildAndParentsRelationship;
use Gedcomx\Extensions\FamilySearch\Platform\Tree\DiscussionReference;
use Gedcomx\Extensions\FamilySearch\Platform\Tree\Merge;
use Gedcomx\Extensions\FamilySearch\Rs\Client\DiscussionState;
use Gedcomx\Extensions\FamilySearch\Rs\Client\FamilyTree\ChildAndParentsRelationshipState;
use Gedcomx\Extensions\FamilySearch\Rs\Client\FamilyTree\FamilyTreePersonState;
use Gedcomx\Extensions\FamilySearch\Rs\Client\FamilyTree\FamilyTreeStateFactory;
use Gedcomx\Extensions\FamilySearch\Rs\Client\Rel;
use Gedcomx\Extensions\FamilySearch\Types\FactType;
use Gedcomx\Gedcomx;
use Gedcomx\Rs\Client\Options\HeaderParameter;
use Gedcomx\Rs\Client\Options\Preconditions;
use Gedcomx\Rs\Client\Options\QueryParameter;
use Gedcomx\Rs\Client\PersonChildrenState;
use Gedcomx\Rs\Client\PersonParentsState;
use Gedcomx\Rs\Client\PersonState;
use Gedcomx\Rs\Client\RelationshipState;
use Gedcomx\Rs\Client\StateFactory;
use Gedcomx\Rs\Client\Util\DataSource;
use Gedcomx\Rs\Client\Util\HttpStatus;
use Gedcomx\Tests\ApiTestCase;
use Gedcomx\Tests\ArtifactBuilder;
use Gedcomx\Tests\DiscussionBuilder;
use Gedcomx\Tests\FactBuilder;
use Gedcomx\Tests\PersonBuilder;
use Gedcomx\Types\GenderType;

class PersonTests extends ApiTestCase
{
    /**
     * @link https://familysearch.org/developers/docs/api/tree/Create_Person_usecase
     */
    public function testCreatePerson()
    {
        $factory = new StateFactory();
        $this->collectionState($factory);
        $person = $this->createPerson();

        $this->assertEquals(
            HttpStatus::CREATED,
            $person->getResponse()->getStatusCode(),
            $this->buildFailMessage(__METHOD__, $person)
        );
    }

    /**
     * @link https://familysearch.org/developers/docs/api/tree/Create_Person_Source_Reference_usecase
     * @see SourcesTests::testCreatePersonSourceReference
     */

    /*
     * @link https://familysearch.org/developers/docs/api/tree/Create_Person_Conclusion_usecase
     */
    public function testCreatePersonConclusion()
    {
        $factory = new StateFactory();
        $this->collectionState($factory);
        $person = $this->createPerson();

        $fact = FactBuilder::militaryService();
        $newState = $person->addFact($fact);

        $this->assertEquals(
            HttpStatus::NO_CONTENT,
            $newState->getResponse()->getStatusCode(),
            $this->buildFailMessage(__METHOD__,$newState)
        );
    }

    /**
     * @link https://familysearch.org/developers/docs/api/tree/Create_Person_Life_Sketch_usecase
     */
    public function testCreatePersonLifeSketch()
    {
        $factory = new StateFactory();
        $this->collectionState($factory);
        $person = $this->createPerson();

        $fact = FactBuilder::lifeSketch();
        $newState = $person->addFact($fact);

        $this->assertEquals(
            HttpStatus::NO_CONTENT,
            $newState->getResponse()->getStatusCode(),
            $this->buildFailMessage(__METHOD__,$newState)
        );
    }

    /**
     * @link https://familysearch.org/developers/docs/api/tree/Create_Discussion_Reference_usecase
     */
    public function testCreateDiscussionReference(){
        $factory = new FamilyTreeStateFactory();
        $this->collectionState($factory);
        /** @var FamilyTreePersonState $person */
        $person = $this->createPerson();

        $userState = $this->collectionState()->readCurrentUser();
        $discussion = DiscussionBuilder::createDiscussion($userState->getUser()->getTreeUserId());

        $discussionState = $this->collectionState()->addDiscussion($discussion);
        $this->queueForDelete($discussionState);

        /** @var \Gedcomx\Rs\Client\PersonState $newState */
        $newState = $person->addDiscussionState($discussionState);

        $this->assertEquals(
            HttpStatus::CREATED,
            $newState->getResponse()->getStatusCode(),
            $this->buildFailMessage(__METHOD__, $newState)
        );
    }

    /**
     * @link https://familysearch.org/developers/docs/api/tree/Create_Note_usecase
     * @see NotesTests::testCreateNote
     */

    /**
     * @link https://familysearch.org/developers/docs/api/tree/Read_Deleted_Person_usecase
     * @see this::testDeleteAndRestorePerson
     */

    /**
     * @link https://familysearch.org/developers/docs/api/tree/Read_Person_usecase
     */
    public function testReadPerson(){
        $factory = new StateFactory();
        $this->collectionState($factory);

        /** @var \Gedcomx\Rs\Client\PersonState $newState */
        $personState = $this->createPerson()->get();

        $this->assertEquals(
            HttpStatus::OK,
            $personState->getResponse()->getStatusCode(),
            $this->buildFailMessage(__METHOD__, $personState)
        );
    }

    /**
     * @link https://familysearch.org/developers/docs/api/tree/Read_Person_Merge_Analysis_usecase
     */
    public function testReadMergeAnalysis()
    {
        $factory = new FamilyTreeStateFactory();
        $collection = $this->collectionState($factory);

        $person = PersonBuilder::buildPerson('male');
        $stateOne = $collection->addPerson($person)->get();
        $stateTwo = $collection->addPerson($person)->get();
        $this->queueForDelete($stateTwo, $stateOne);

        /** @var \Gedcomx\Extensions\FamilySearch\Rs\Client\PersonMergeState $analysis */
        $analysis = $stateOne->readMergeAnalysis($stateTwo);
        $this->assertEquals(
            HttpStatus::OK,
            $analysis->getResponse()->getStatusCode(),
            $this->buildFailMessage(__METHOD__, $analysis)
        );
        $this->assertNotEmpty($analysis->getAnalysis());
    }

    /**
     * @link https://familysearch.org/developers/docs/api/tree/Read_Person_Merge_Constraint_(Can_Merge_Any_Order)_usecase
     */
    public function testReadPersonMergeConstraintAnyOrder()
    {
        $factory = new FamilyTreeStateFactory();
        $collection = $this->collectionState($factory);

        $person = PersonBuilder::buildPerson('male');
        $person1 = $collection->addPerson($person)->get();
        $person2 = $collection->addPerson($person)->get();
        $this->queueForDelete($person1, $person2);

        /** @var \Gedcomx\Extensions\FamilySearch\Rs\Client\PersonMergeState $state */
        $state = $person1->readMergeOptions($person2);
        $this->assertEquals(
            HttpStatus::OK,
            $state->getResponse()->getStatusCode(),
            $this->buildFailMessage(__METHOD__, $state)
        );
        $this->assertArrayHasKey(Rel::MERGE_MIRROR, $state->getLinks(), $this->buildFailMessage(__METHOD__, $state));
    }

    /**
     * @link https://familysearch.org/developers/docs/api/tree/Read_Person_Merge_Constraint_(Can_Merge_Other_Order_Only)_usecase
     */
    public function testReadPersonMergeConstraintOtherOrder()
    {
        $factory = new FamilyTreeStateFactory();
        $collection = $this->collectionState($factory);

        $male = PersonBuilder::buildPerson('male');
        $female = PersonBuilder::buildPerson('female');
        $person1 = $collection->addPerson($male)->get();
        $person2 = $collection->addPerson($female)->get();
        $this->queueForDelete($person1, $person2);

        /** @var \Gedcomx\Extensions\FamilySearch\Rs\Client\PersonMergeState $state */
        $state = $person1->readMergeOptions($person2);
        $this->assertEquals(
            HttpStatus::OK,
            $state->getResponse()->getStatusCode(),
            $this->buildFailMessage(__METHOD__, $state)
        );
        $this->assertFalse($state->isAllowed(), $this->buildFailMessage(__METHOD__, $state));
    }


    /**
     * @link https://familysearch.org/developers/docs/api/tree/Read_Person_Source_References_usecase
     * @see SourcesTests::testReadPersonSourceReferences
     */

    /**
     * @link https://familysearch.org/developers/docs/api/tree/Read_Person_Sources_usecase
     * @see SourcesTests::testReadPersonSources
     */

    /**
     * @link https://familysearch.org/developers/docs/api/tree/Read_Person_Change_Summary_usecase
     */
    public function testPersonChangeSummary()
    {
        $this->markTestSkipped("Change summary will not be tested.");
    }

    /**
     * @link https://familysearch.org/developers/docs/api/tree/Read_Person_Memories_usecase
     */
    public function testReadPersonMemories()
    {
        $factory = new FamilyTreeStateFactory();
        $this->collectionState($factory);
        /** @var FamilyTreePersonState $person */
        $person = $this->createPerson();
        $this->assertEquals(HttpStatus::CREATED, $person->getResponse()->getStatusCode());
        $person = $person->get();
        $this->assertEquals(HttpStatus::OK, $person->getResponse()->getStatusCode());

        $filename = ArtifactBuilder::makeTextFile();
        $artifact = new DataSource();
        $artifact->setFile($filename);
        $a1 = $person->addArtifact($artifact);
        $this->queueForDelete($a1);
        $this->assertEquals(HttpStatus::CREATED, $a1->getResponse()->getStatusCode());

        $memories = $person->readArtifacts();

        $this->assertEquals(
            HttpStatus::OK,
            $memories->getResponse()->getStatusCode(),
            $this->buildFailMessage(__METHOD__, $memories)
        );
        $this->assertNotNull($memories->getEntity());
        $this->assertNotNull($memories->getEntity()->getSourceDescriptions());
        $this->assertGreaterThan(0, count($memories->getEntity()->getSourceDescriptions()));
    }

    /**
     * @link https://familysearch.org/developers/docs/api/tree/Read_Person_Memories_By_Type._usecase
     */
    public  function testReadPersonMemoriesByType()
    {
        $factory = new FamilyTreeStateFactory();
        $this->collectionState($factory);
        /** @var FamilyTreePersonState $person */
        $person = $this->createPerson();
        $this->assertEquals(HttpStatus::CREATED, $person->getResponse()->getStatusCode());
        $person = $person->get();
        $this->assertEquals(HttpStatus::OK, $person->getResponse()->getStatusCode());

        $filename = ArtifactBuilder::makeTextFile();
        $artifact = new DataSource();
        $artifact->setFile($filename);
        $a1 = $person->addArtifact($artifact);
        $this->queueForDelete($a1);
        $this->assertEquals(HttpStatus::CREATED, $a1->getResponse()->getStatusCode());

        $option = new QueryParameter(true, "type", "photo");
        $memories = $person->readArtifacts($option);
        $this->assertEquals(
            HttpStatus::NO_CONTENT,
            $memories->getResponse()->getStatusCode(),
            $this->buildFailMessage(__METHOD__, $memories)
        );
        $this->assertEmpty($memories->getEntity());

        $option = new QueryParameter(true, "type", "story");
        $memories = $person->readArtifacts($option);
        $this->assertEquals(
            HttpStatus::OK,
            $memories->getResponse()->getStatusCode(),
            $this->buildFailMessage(__METHOD__, $memories)
        );
        $this->assertNotEmpty($memories->getEntity()->getSourceDescriptions());

        $person->delete();
    }

    /**
     * @link https://familysearch.org/developers/docs/api/tree/Read_Person_Portraits_usecase
     */
    public function testReadPersonPortrait()
    {
        $factory = new FamilyTreeStateFactory();
        $this->collectionState($factory);
        $memories = $factory->newMemoriesState();
        $memories = $this->authorize($memories);

        $filename =  ArtifactBuilder::makeImage();
        $artifact = new DataSource();
        $artifact->setFile($filename);

        /** @var FamilyTreePersonState $person */
        $person = $this->createPerson();
        $this->assertEquals(HttpStatus::CREATED, $person->getResponse()->getStatusCode());
        $person = $person->get();
        $this->assertEquals(HttpStatus::OK, $person->getResponse()->getStatusCode());

        $description = $this->createSource()->get()->getSourceDescription();
        /** @var \Gedcomx\Rs\Client\SourceDescriptionState $upload */
        $upload = $memories->addArtifact($artifact, $description)->get();
        $this->queueForDelete($upload);

        $persona = $upload->addPersonPersona(PersonBuilder::buildPerson('male'))->get();
        $this->queueForDelete($persona);

        $person->addPersonaPersonState($persona);

        sleep(5); // Need to wait before the portrait details are available for testing
        $response = $person->readPortrait();

        $this->assertNotNull($response);
        $this->assertEquals(HttpStatus::OK, $response->getStatusCode());
        $this->assertGreaterThan(0, $response->getRedirectCount());
    }

    /**
     * @link https://familysearch.org/developers/docs/api/tree/Read_Person_Portrait_With_Default_usecase
     */
    public function testReadPortraitWithDefault()
    {
        $factory = new FamilyTreeStateFactory();
        $this->collectionState($factory);

        $person = $this->createPerson();
        $this->assertEquals(HttpStatus::CREATED, $person->getResponse()->getStatusCode());
        $person = $person->get();
        $this->assertEquals(HttpStatus::OK, $person->getResponse()->getStatusCode());
        $defaultImage = new QueryParameter(true, "default","http://i.imgur.com/d9J0gYA.jpg");

        /** @var \Guzzle\Http\Message\Response $response */
        $response = $person->readPortrait($defaultImage);

        $this->assertGreaterThan(0, $response->getRedirectCount(), "Redirect does not appear to have worked.");
        $this->assertEquals(
            "http://i.imgur.com/d9J0gYA.jpg",
            $response->getEffectiveUrl(),
            "Portrait default image URL incorrect: " . $response->getEffectiveUrl()
        );
    }

    /**
     * @link https://familysearch.org/developers/docs/api/tree/Read_Person_Portraits_usecase
     */
    public function testReadPersonPortraits()
    {
        $factory = new FamilyTreeStateFactory();
        $this->collectionState($factory);
        $memories = $factory->newMemoriesState();
        $memories = $this->authorize($memories);

        $filename =  ArtifactBuilder::makeImage();
        $artifact = new DataSource();
        $artifact->setFile($filename);

        /** @var FamilyTreePersonState $person */
        $person = $this->createPerson();
        $this->assertEquals(HttpStatus::CREATED, $person->getResponse()->getStatusCode());
        $person = $person->get();
        $this->assertEquals(HttpStatus::OK, $person->getResponse()->getStatusCode());

        $description = $this->createSource()->get()->getSourceDescription();
        /** @var \Gedcomx\Rs\Client\SourceDescriptionState $upload */
        $upload = $memories->addArtifact($artifact, $description)->get();
        $this->queueForDelete($upload);

        $persona = $upload->addPersonPersona(PersonBuilder::buildPerson('male'))->get();
        $this->queueForDelete($persona);

        $person->addPersonaPersonState($persona);

        $portraits = $person->readPortraits();

        $this->assertEquals(
            HttpStatus::OK,
            $portraits->getResponse()->getStatusCode(),
            $this->buildFailMessage(__METHOD__, $portraits)
        );

        $this->assertNotNull($portraits->getEntity());
        $this->assertNotNull($portraits->getEntity()->getSourceDescriptions());
        $this->assertGreaterThan(0, count($portraits->getEntity()->getSourceDescriptions()));
    }

    /**
     * @link https://familysearch.org/developers/docs/api/tree/Read_Person_With_Relationships_usecase
     */
    public function testReadPersonWithRelationships()
    {
        $factory = new FamilyTreeStateFactory();
        $collection = $this->collectionState($factory);

        /** @var FamilyTreePersonState $person */
        $person = $this->createPerson('male');
        $this->assertEquals(
            HttpStatus::CREATED,
            $person->getResponse()->getStatusCode(),
            $this->buildFailMessage(__METHOD__, $person)
        );
        $child1 = $this->createPerson();
        $this->assertEquals(
            HttpStatus::CREATED,
            $child1->getResponse()->getStatusCode(),
            $this->buildFailMessage(__METHOD__, $child1)
        );
        $child2 = $this->createPerson();
        $this->assertEquals(
            HttpStatus::CREATED,
            $child2->getResponse()->getStatusCode(),
            $this->buildFailMessage(__METHOD__, $child2)
        );
        $childRel1 = $collection->addChildAndParents($child1,$person);
        $this->assertEquals(
            HttpStatus::CREATED,
            $childRel1->getResponse()->getStatusCode(),
            $this->buildFailMessage(__METHOD__, $childRel1)
        );
        $childRel2 = $collection->addChildAndParents($child2,$person);
        $this->assertEquals(
            HttpStatus::CREATED,
            $childRel2->getResponse()->getStatusCode(),
            $this->buildFailMessage(__METHOD__, $childRel2)
        );

        $this->queueForDelete($childRel1, $childRel2);

        $person = $person->get();
        $this->assertEquals(
            HttpStatus::OK,
            $person->getResponse()->getStatusCode(),
            $this->buildFailMessage(__METHOD__, $person)
        );

        $person = $collection->readPersonWithRelationshipsById($person->getPerson()->getId());
        $this->assertNotNull($person->getEntity(), "Read relationships failed. Entity is null.");

        $thePerson = $person->getPerson();
        $ftRelationships = $person->getChildAndParentsRelationships();
        $relationships = $person->getRelationships();

        $data_check = $thePerson instanceof Person
            && count($ftRelationships) > 0
            && $ftRelationships[0] instanceof ChildAndParentsRelationship
            && count($relationships) > 0
            && $relationships[0] instanceof Relationship;

        $this->assertTrue($data_check, "Expected number and type of data objects was not found.");
    }

    /**
     * @link https://familysearch.org/developers/docs/api/tree/Read_Relationships_to_Children_usecase
     */
    public function testReadRelationshipsToChildren()
    {
        $factory = new FamilyTreeStateFactory();
        $collection = $this->collectionState($factory);

        $person = $this->createPerson('male');
        $this->assertEquals(
            HttpStatus::CREATED,
            $person->getResponse()->getStatusCode(),
            $this->buildFailMessage(__METHOD__."(person)", $person)
        );
        $child1 = $this->createPerson();
        $this->assertEquals(
            HttpStatus::CREATED,
            $child1->getResponse()->getStatusCode(),
            $this->buildFailMessage(__METHOD__."(child1)", $child1)
        );
        $child2 = $this->createPerson();
        $this->assertEquals(
            HttpStatus::CREATED,
            $child2->getResponse()->getStatusCode(),
            $this->buildFailMessage(__METHOD__."(child2)", $child2)
        );
        $childRel1 = $collection->addChildAndParents($child1,$person);
        $this->assertEquals(
            HttpStatus::CREATED,
            $childRel1->getResponse()->getStatusCode(),
            $this->buildFailMessage(__METHOD__."(childRel1)", $childRel1)
        );
        $childRel2 = $collection->addChildAndParents($child2,$person);
        $this->assertEquals(
            HttpStatus::CREATED,
            $childRel2->getResponse()->getStatusCode(),
            $this->buildFailMessage(__METHOD__."(childRel2)", $childRel2)
        );

        $this->queueForDelete($childRel1, $childRel2);

        $person = $person->get();
        $this->assertEquals(
            HttpStatus::OK,
            $person->getResponse()->getStatusCode(),
            $this->buildFailMessage(__METHOD__."(load)", $person)
        );

        $person->loadChildRelationships();
        $this->assertNotNull($person->getEntity(), "Load child relationships failed. Entity is null.");
        $this->assertNotEmpty($person->getRelationshipsToChildren(), "No child relationships found." );
    }

    /**
     * @link https://familysearch.org/developers/docs/api/tree/Read_Relationships_to_Parents_usecase
     */
    public function testReadRelationshipsToParents()
    {
        $factory = new FamilyTreeStateFactory();
        $collection = $this->collectionState($factory);

        $father = $this->createPerson('male');
        $this->assertEquals(
            HttpStatus::CREATED,
            $father->getResponse()->getStatusCode(),
            $this->buildFailMessage(__METHOD__."(father)", $father)
        );
        $mother = $this->createPerson('female');
        $this->assertEquals(
            HttpStatus::CREATED,
            $mother->getResponse()->getStatusCode(),
            $this->buildFailMessage(__METHOD__."(mother)", $mother)
        );
        $child = $this->createPerson();
        $this->assertEquals(
            HttpStatus::CREATED,
            $child->getResponse()->getStatusCode(),
            $this->buildFailMessage(__METHOD__."(child)", $child)
        );
        $family = $collection->addChildAndParents($child, $father, $mother);
        $this->assertEquals(
            HttpStatus::CREATED,
            $family->getResponse()->getStatusCode(),
            $this->buildFailMessage(__METHOD__."(family)", $family)
        );
        $this->queueForDelete($family);

        $child = $child->get();
        $this->assertEquals(
            HttpStatus::OK,
            $child->getResponse()->getStatusCode(),
            $this->buildFailMessage(__METHOD__."(get)", $child)
        );

        $child->loadParentRelationships();

        $this->assertNotNull($child->getEntity(), "Load failed. Entity is null.");
        $this->assertNotEmpty($child->getRelationshipsToParents(), "No parent relationships found." );
    }

    /**
     * @link https://familysearch.org/developers/docs/api/tree/Read_Relationships_To_Spouses_usecase
     */
    public function testReadRelationshipsToSpouses()
    {
        $factory = new StateFactory();
        $this->collectionState($factory);

        /** @var PersonState $husband */
        $husband = $this->createPerson('male');
        $this->assertEquals(
            HttpStatus::CREATED,
            $husband->getResponse()->getStatusCode(),
            $this->buildFailMessage(__METHOD__."(husband)", $husband)
        );
        $wife = $this->createPerson('female');
        $this->assertEquals(
            HttpStatus::CREATED,
            $wife->getResponse()->getStatusCode(),
            $this->buildFailMessage(__METHOD__."(wife)", $wife)
        );

        $husband = $husband->get();
        $this->assertEquals(
            HttpStatus::OK,
            $husband->getResponse()->getStatusCode(),
            $this->buildFailMessage(__METHOD__."(get)", $husband)
        );

        /** @var RelationshipState $family */
        $family = $husband->addSpouse($wife);
        $this->assertEquals(
            HttpStatus::CREATED,
            $family->getResponse()->getStatusCode(),
            $this->buildFailMessage(__METHOD__."(family)", $family)
        );

        $this->queueForDelete($family);

        $husband->loadSpouseRelationships();

        $this->assertNotNull($husband->getEntity(), "Load failed. Entity is null.");
        $this->assertNotEmpty($husband->getSpouseRelationships(), "No spouse relationships found." );
    }

    /**
     * @link https://familysearch.org/developers/docs/api/tree/Read_Relationships_To_Spouses_with_Persons_usecase
     */
    public function testReadRelationshipsToSpousesWithPersons(){
        $factory = new StateFactory();
        $this->collectionState($factory);

        /** @var PersonState $husband */
        $husband = $this->createPerson('male');
        $this->assertEquals(
            HttpStatus::CREATED,
            $husband->getResponse()->getStatusCode(),
            $this->buildFailMessage(__METHOD__."(husband)", $husband)
        );
        $wife = $this->createPerson('female');
        $this->assertEquals(
            HttpStatus::CREATED,
            $wife->getResponse()->getStatusCode(),
            $this->buildFailMessage(__METHOD__."(wife)", $wife)
        );

        $husband = $husband->get();
        $this->assertEquals(
            HttpStatus::OK,
            $husband->getResponse()->getStatusCode(),
            $this->buildFailMessage(__METHOD__."(get)", $husband)
        );

        /** @var RelationshipState $family */
        $family = $husband->addSpouse($wife);
        $this->assertEquals(
            HttpStatus::CREATED,
            $family->getResponse()->getStatusCode(),
            $this->buildFailMessage(__METHOD__."(family)", $family)
        );

        $this->queueForDelete($family);

        $option = new QueryParameter(true,"persons","");
        $husband->loadSpouseRelationships($option);

        $this->assertNotNull($husband->getEntity(), "Load failed. Entity is null");
        $this->assertGreaterThan(0, count($husband->getEntity()->getPersons()), "No persons relationships found." );
    }

    /**
     * @link https://familysearch.org/developers/docs/api/tree/Create_Discussion_Reference_usecase
     * @link https://familysearch.org/developers/docs/api/tree/Read_Discussion_References_usecase
     */
    public function testReadDiscussionReference(){
        $factory = new FamilyTreeStateFactory();
        $this->collectionState($factory);
        /** @var FamilyTreePersonState $person */
        $person = $this->createPerson()->get();

        $userState = $this->collectionState()->readCurrentUser();
        $discussion = DiscussionBuilder::createDiscussion($userState->getUser()->getTreeUserId());

        $discussionState = $this->collectionState()->addDiscussion($discussion);
        $this->queueForDelete($discussionState);

        $newState = $person->addDiscussionState($discussionState);

        $this->assertAttributeEquals(HttpStatus::CREATED, "statusCode", $newState->getResponse(), $this->buildFailMessage(__METHOD__, $newState) );

        $person->loadDiscussionReferences();

        $found = false;
        foreach ($person->getPerson()->getExtensionElements() as $ext) {
            if ($ext instanceof DiscussionReference) {
                $found = true;
                break;
            }
        }
        $this->assertTrue($found);
    }

    /**
     * @link https://familysearch.org/developers/docs/api/tree/Read_Children_of_a_Person_usecase
     */
    public function testReadPersonChildren(){
        $factory = new FamilyTreeStateFactory();
        $collection = $this->collectionState($factory);

        /** @var FamilyTreePersonState $person */
        $person = $this->createPerson('male');
        $this->assertEquals(
            HttpStatus::CREATED,
            $person->getResponse()->getStatusCode(),
            $this->buildFailMessage(__METHOD__."(person)", $person)
        );
        $child1 = $this->createPerson();
        $this->assertEquals(
            HttpStatus::CREATED,
            $child1->getResponse()->getStatusCode(),
            $this->buildFailMessage(__METHOD__."(child1)", $child1)
        );
        $child2 = $this->createPerson();
        $this->assertEquals(
            HttpStatus::CREATED,
            $child2->getResponse()->getStatusCode(),
            $this->buildFailMessage(__METHOD__."(child2)", $child2)
        );
        $childRel1 = $collection->addChildAndParents($child1,$person);
        $this->assertEquals(
            HttpStatus::CREATED,
            $childRel1->getResponse()->getStatusCode(),
            $this->buildFailMessage(__METHOD__."(childRel1)", $childRel1)
        );
        $childRel2 = $collection->addChildAndParents($child2,$person);
        $this->assertEquals(
            HttpStatus::CREATED,
            $childRel2->getResponse()->getStatusCode(),
            $this->buildFailMessage(__METHOD__."(childRel2)", $childRel2)
        );

        $this->queueForDelete($childRel1, $childRel2);

        $person = $person->get();
        $this->assertEquals(
            HttpStatus::OK,
            $person->getResponse()->getStatusCode(),
            $this->buildFailMessage(__METHOD__."(get)", $person)
        );

        /** @var PersonChildrenState $childrenState */
        $childrenState = $person->readChildren();
        $this->assertEquals(
            HttpStatus::OK,
            $childrenState->getResponse()->getStatusCode(),
            $this->buildFailMessage(__METHOD__."(childrenState)", $childrenState)
        );

        $this->assertEquals(
            HttpStatus::OK,
            $childrenState->getResponse()->getStatusCode(),
            $this->buildFailMessage(__METHOD__,$childrenState)
        );
    }

    /**
     * @link https://familysearch.org/developers/docs/api/tree/Read_Not_Found_Person_usecase
     */
    public function testReadNotFoundPerson(){
        $factory = new StateFactory();
        $this->collectionState($factory);

        $personState = $this->getPerson('MMMM-NNN');
        $this->assertEquals(
            HttpStatus::NOT_FOUND,
            $personState->getResponse()->getStatusCode(),
            $this->buildFailMessage(__METHOD__,$personState)
        );
    }

    /**
     * @link https://familysearch.org/developers/docs/api/tree/Read_Not-Modified_Person_usecase
     */
    public function testReadNotModifiedPerson(){
        $factory = new StateFactory();
        $this->collectionState($factory);

        $person = $this->createPerson('male')->get();

        $options = array();
        $options[] = new HeaderParameter(true, HeaderParameter::IF_NONE_MATCH, $person->getResponse()->getEtag());
        $options[] = new HeaderParameter(true, HeaderParameter::ETAG, $person->getResponse()->getEtag());

        $secondState = $this->getPerson($person->getPerson()->getId(), $options);

        $this->assertEquals(
            HttpStatus::NOT_MODIFIED,
            $secondState->getResponse()->getStatusCode(),
            $this->buildFailMessage(__METHOD__, $secondState)
        );
    }

    /**
     * @link https://familysearch.org/developers/docs/api/tree/Read_Notes_usecase
     * @see NotesTests::testReadNotes
     */

    /**
     * @link https://familysearch.org/developers/docs/api/tree/Read_Parents_of_a_Person_usecase
     */
    public function testReadParentsOfPerson()
    {
        $factory = new FamilyTreeStateFactory();
        $collection = $this->collectionState($factory);

        $father = $this->createPerson('male');
        $this->assertEquals(
            HttpStatus::CREATED,
            $father->getResponse()->getStatusCode(),
            $this->buildFailMessage(__METHOD__."(father)", $father)
        );
        $mother = $this->createPerson('female');
        $this->assertEquals(
            HttpStatus::CREATED,
            $mother->getResponse()->getStatusCode(),
            $this->buildFailMessage(__METHOD__."(mother)", $mother)
        );
        $child = $this->createPerson();
        $this->assertEquals(
            HttpStatus::CREATED,
            $child->getResponse()->getStatusCode(),
            $this->buildFailMessage(__METHOD__."(child)", $child)
        );

        $family = $collection->addChildAndParents($child, $father, $mother);
        $this->assertEquals(
            HttpStatus::CREATED,
            $family->getResponse()->getStatusCode(),
            $this->buildFailMessage(__METHOD__."(family)", $family)
        );

        $this->queueForDelete($family);

        $child = $child->get();
        $this->assertEquals(
            HttpStatus::OK,
            $child->getResponse()->getStatusCode(),
            $this->buildFailMessage(__METHOD__."(get)", $child)
        );

        /** @var PersonParentsState $parentState */
        $parentState = $child->readParents();
        $this->assertEquals(
            HttpStatus::OK,
            $parentState->getResponse()->getStatusCode(),
            $this->buildFailMessage(__METHOD__, $parentState)
        );

        $this->assertNotNull($parentState->getEntity(), "Read parents failed. Entity is null.");
        $this->assertEquals(2, count($parentState->getRelationships()), "Should have two relationship objects on Entity.");
        $this->assertEquals(2, count($parentState->getPersons()), "Should have two person objects on Entity.");

    }

    /**
     * testReadPreferredParentRelationship
     * @link https://familysearch.org/developers/docs/api/tree/Read_Preferred_Parent_Relationship_usecase
     * @see this::testPreferredParentRelationship
     */

    /**
     * testReadPreferredSpouseRelationship
     * @link https://familysearch.org/developers/docs/api/tree/Read_Preferred_Spouse_Relationship_usecase
     * @see this::testPreferredSpouseRelationship
     */

    /**
     * @link https://familysearch.org/developers/docs/api/tree/Read_Spouses_of_a_Person_usecase
     */
    public function testReadSpousesOfPerson()
    {
        $factory = new StateFactory();
        $this->collectionState($factory);

        /** @var FamilyTreePersonState $husband */
        /** @var PersonState $husband */
        $husband = $this->createPerson('male');
        $this->assertEquals(
            HttpStatus::CREATED,
            $husband->getResponse()->getStatusCode(),
            $this->buildFailMessage(__METHOD__."(husband)", $husband)
        );
        $wife = $this->createPerson('female');
        $this->assertEquals(
            HttpStatus::CREATED,
            $wife->getResponse()->getStatusCode(),
            $this->buildFailMessage(__METHOD__."(wife)", $wife)
        );

        $husband = $husband->get();
        $this->assertEquals(
            HttpStatus::OK,
            $husband->getResponse()->getStatusCode(),
            $this->buildFailMessage(__METHOD__."(get)", $husband)
        );

        /** @var RelationshipState $family */
        $family = $husband->addSpouse($wife);
        $this->assertEquals(
            HttpStatus::CREATED,
            $family->getResponse()->getStatusCode(),
            $this->buildFailMessage(__METHOD__."(family)", $family)
        );

        $spouse = $husband->readSpouses();
        $this->assertEquals(
            HttpStatus::OK,
            $spouse->getResponse()->getStatusCode(),
            $this->buildFailMessage(__METHOD__,$spouse)
        );

        $this->assertNotNull($spouse->getEntity(), "Read spouses failed. Entity is null.");
        $this->assertGreaterThan(0, count($spouse->getPersons()), "No spouse persons found.");
        $this->assertGreaterThan(0, count($spouse->getRelationships()), "No relationships found.");
    }

    /**
     * @link https://familysearch.org/developers/docs/api/tree/Merge_Person_usecase
     */
    public function testMergePerson()
    {
        $factory = new FamilyTreeStateFactory();
        $this->collectionState($factory);

        $person = PersonBuilder::buildPerson('male');
        /** @var FamilyTreePersonState $person1 */
        $person1 = $this->collectionState()->addPerson($person)->get();
        /** @var FamilyTreePersonState $person2 */
        $person2 = $this->collectionState()->addPerson($person)->get();
        $this->queueForDelete($person2, $person1);

        /** @var  \Gedcomx\Extensions\FamilySearch\Rs\Client\PersonMergeState $result */
        $result = $person1->readMergeAnalysis($person2);
        /** @var  \Gedcomx\Extensions\FamilySearch\Platform\Tree\MergeAnalysis $analysis */
        $analysis = $result->getAnalysis();

        $merge = new Merge();
        $merge->setResourcesToCopy($analysis->getDuplicateResources());
        /** @var \Gedcomx\Extensions\FamilySearch\Platform\Tree\MergeConflict $resource */
        foreach ($analysis->getConflictingResources() as $resource) {
            if ($resource->getDuplicateResource()) {
                $ref = $resource->getDuplicateResource();
                $merge->addResourceToCopy($ref);
            }
        }

        $state = $result->doMerge($merge);
        $this->assertEquals(
            HttpStatus::NO_CONTENT,
            $state->getResponse()->getStatusCode(),
            $this->buildFailMessage(__METHOD__,$state)
        );

        //  Read the results of the merge

        $person1 = $person1->get();
        $person2 = $person2->get();

        //  HttpStatus::MOVED_PERMANENTLY is the correct response for a person that has been merged.
        //  However, asserting that response assumes that the HTTP client does not automatically
        //  follow redirects, which Guzzle does.
        //
        //  Rather than disabling the redirect feature, we'll assert that both person states have the
        //  same URI now.

        $this->assertEquals(
            $person1->getSelfUri(),
            $person2->getSelfUri(),
            "Person URIs don't match."
        );
    }

    /**
     * @link https://familysearch.org/developers/docs/api/tree/Read_Merged_Person_usecase
     * @see testMergePerson
     */

    /**
     * @link https://familysearch.org/developers/docs/api/tree/Head_Person_usecase
     */
    public function testHeadPerson()
    {
        $factory = new StateFactory();
        $this->collectionState($factory);

        $person = $this->createPerson();
        $newState = $person->head();

        $this->assertEquals(
			HttpStatus::CREATED, 
			$person->getResponse()->getStatusCode(),
			$this->buildFailMessage(__METHOD__, $person)
        );
        $this->assertEquals(
            HttpStatus::OK,
            $newState->getResponse()->getStatusCode(),
            $this->buildFailMessage(__METHOD__, $newState)
        );
    }

    /**
     * @link https://familysearch.org/developers/docs/api/tree/Update_Person_Source_Reference_usecase
     * @see SourcesTests::testUpdatePersonSourceReference
     */

    /**
     * @link https://familysearch.org/developers/docs/api/tree/Update_Person_Conclusion_usecase
     */
    public function testUpdatePersonConclusion()
    {
        $factory = new StateFactory();
        $this->collectionState($factory);

        /** @var PersonState $personState */
        $personState = $this->createPerson()->get();
        $gender = new Gender(array(
            "type" =>GenderType::MALE
        ));
        $personState->updateGender($gender);

        $this->assertEquals(
            HttpStatus::OK,
            $personState->getResponse()->getStatusCode(),
            $this->buildFailMessage(__METHOD__, $personState)
        );
    }

    /**
     * @link https://familysearch.org/developers/docs/api/tree/Update_Person_Custom_Non-Event_Fact_usecase
     */
    public function testUpdatePersonCustomNonEventFact()
    {
        $factory = new StateFactory();
        $this->collectionState($factory);

        /** @var PersonState $personState */
        $personState = $this->createPerson()->get();
        $fact = FactBuilder::eagleScout();
        $newState = $personState->addFact($fact);

        $this->assertEquals(
            HttpStatus::NO_CONTENT,
            $newState->getResponse()->getStatusCode(),
            $this->buildFailMessage(__METHOD__, $newState)
        );
    }

    /**
     * @link https://familysearch.org/developers/docs/api/tree/Update_Person_Life_Sketch_usecase
     */
    public function testUpdatePersonLifeSketch()
    {
        $factory = new FamilyTreeStateFactory();
        $this->collectionState($factory);

        /** @var FamilyTreePersonState $personState */
        $personState = $this->createPerson();
        if( $personState->getPerson() == null ){
            $uri = $personState->getSelfUri();
            $personState = $this->collectionState()->readPerson($uri);
        }
        $fact = FactBuilder::lifeSketch();
        $personState = $personState->addFact($fact)->get();
        $sketch = $personState->getPerson()->getFactsOfType(FactType::LIFE_SKETCH);
        if (is_array($sketch)) {
            $sketch = array_shift($sketch);
        }
        $sketch->setValue($this->faker->paragraph(3));

        $newState = $personState->updateFact($sketch);

        $this->assertEquals(
            HttpStatus::NO_CONTENT,
            $newState->getResponse()->getStatusCode(),
            $this->buildFailMessage(__METHOD__, $newState)
        );
    }

    /**
     * @link https://familysearch.org/developers/docs/api/tree/Update_Person_Not-a-Match_Declarations_usecase
     */
    public function testUpdatePersonNotAMatchDeclarations()
    {
        $factory = new FamilyTreeStateFactory();
        $this->collectionState($factory);

        $personData = PersonBuilder::buildPerson(null);

        /** @var FamilyTreePersonState $one */
        $one = $this->collectionState()->addPerson($personData)->get();
        $two = $this->collectionState()->addPerson($personData)->get();
        $this->queueForDelete($one, $two);

        $nonMatch = $one->addNonMatchPerson($two->getPerson());
        $this->assertEquals(
            HttpStatus::NO_CONTENT,
            $nonMatch->getResponse()->getStatusCode(),
            $this->buildFailMessage(__METHOD__, $nonMatch)
        );
    }

    /**
     * @link https://familysearch.org/developers/docs/api/tree/Update_Person_With_Preconditions_usecase
     */
    public function testUpdatePersonWithPreconditions()
    {
        $factory = new StateFactory();
        $this->collectionState($factory);

        /** @var PersonState $personState */
        $personState = $this->createPerson()->get();

        $mangled = str_replace(array(1,3,5,'a','b','d'), array(8,4,3,'Z','X','W'), $personState->getResponse()->getEtag());
        $check = new Preconditions();
        $check->setEtag($mangled);
        $check->setLastModified(new \DateTime($personState->getResponse()->getLastModified()));

        $persons = $personState->getEntity()->getPersons();
        $state = $personState->update($persons[0], $check);
        $this->assertEquals(
            HttpStatus::PRECONDITION_FAILED,
            $state->getResponse()->getStatusCode(),
            $this->buildFailMessage(__METHOD__, $state)
        );
    }

    /**
     * testUpdatePreferredParentRelationship
     * @link https://familysearch.org/developers/docs/api/tree/Update_Preferred_Parent_Relationship_usecase
     * @see this::testPreferredParentRelationship
     */

    /**
     * testUpdatePreferredSpouseRelationship
     * @link https://familysearch.org/developers/docs/api/tree/Update_Preferred_Spouse_Relationship_usecase
     * @see this::testPreferredSpouseRelationship
     */

    /**
     * testDeletePerson
     * @link https://familysearch.org/developers/docs/api/tree/Delete_Person_usecase
     * @see this::testDeleteAndRestorePerson
     */

    /**
     * @link https://familysearch.org/developers/docs/api/tree/Delete_Person_Source_Reference_usecase
     * @see SourcesTests::testDeletePersonSourceReference
     */

    /**
     * @link https://familysearch.org/developers/docs/api/tree/Delete_Person_Conclusion_usecase
     */
    public function testDeletePersonConclusion()
    {
        $factory = new StateFactory();
        $this->collectionState($factory);

        /** @var PersonState $personState */
        $personState = $this->createPerson()->get();
        $name = PersonBuilder::nickName();
        $newPersonState = $personState->addName($name);

        $this->assertAttributeEquals(HttpStatus::NO_CONTENT, "statusCode", $newPersonState->getResponse() );
        /** @var PersonState $newPersonState */
        $newPersonState = $personState->get();

        /** @var \Gedcomx\Conclusion\Person[] $persons */
        $persons = $newPersonState->getEntity()->getPersons();
        $names = $persons[0]->getNames();
        $deletedState = $newPersonState->deleteName($names[1]);

        $this->assertEquals(
            HttpStatus::NO_CONTENT,
            $deletedState->getResponse()->getStatusCode(),
            $this->buildFailMessage(__METHOD__, $deletedState)
        );
    }

    /**
     * @link https://familysearch.org/developers/docs/api/tree/Delete_Person_Not-a-Match_usecase
     */
    public function testDeletePersonNotAMatch()
    {
        $factory = new FamilyTreeStateFactory();
        $this->collectionState($factory);

        $personData = PersonBuilder::buildPerson(null);

        /** @var FamilyTreePersonState $one */
        $one = $this->collectionState()->addPerson($personData)->get();
        $two = $this->collectionState()->addPerson($personData)->get();
        $this->queueForDelete($one, $two);

        $nonMatch = $one->addNonMatchPerson($two->getPerson());
        $rematch = $nonMatch->removeNonMatch($two->getPerson());

        $this->assertEquals(
            HttpStatus::NO_CONTENT,
            $rematch->getResponse()->getStatusCode(),
            "Restore person failed. Returned {$rematch->getResponse()->getStatusCode()}"
        );
    }

    /**
     * @link https://familysearch.org/developers/docs/api/tree/Delete_Person_With_Preconditions_usecase
     */
    public function testDeletePersonWithPreconditions()
    {
        $factory = new StateFactory();
        $this->collectionState($factory);

        /** @var PersonState $personState */
        $personState = $this->createPerson()->get();

        $mangled = str_replace(array(1,3,5,'a','b','d'), array(8,4,3,'Z','X','W'), $personState->getResponse()->getEtag());
        $check = new Preconditions();
        $check->setEtag($mangled);
        $check->setLastModified(new \DateTime($personState->getResponse()->getLastModified()));

        $dState = $personState->delete($check);
        $this->assertEquals(
            HttpStatus::PRECONDITION_FAILED,
            $dState->getResponse()->getStatusCode(),
            $this->buildFailMessage(__METHOD__, $dState)
        );
    }

    /**
     * @link https://familysearch.org/developers/docs/api/tree/Delete_Discussion_Reference_usecase
     */
    public function testDeleteDiscussionReference()
    {
        $factory = new FamilyTreeStateFactory();
        $this->collectionState($factory);

        $userState = $this->collectionState()->readCurrentUser();
        $discussion = DiscussionBuilder::createDiscussion($userState->getUser()->getTreeUserId());

        /** @var DiscussionState $discussionState */
        $discussionState = $this->collectionState()->addDiscussion($discussion);
        $this->queueForDelete($discussionState);

        $ref = new DiscussionReference();
        $ref->setResource($discussionState->getSelfUri());

        /** @var FamilyTreePersonState $person */
        $person = $this->collectionState()->readPersonForCurrentUser();
        $newState = $person->deleteDiscussionReference($ref);

        $this->assertEquals(
            HttpStatus::NO_CONTENT,
            $newState->getResponse()->getStatusCode(),
            $this->buildFailMessage(__METHOD__, $newState)
        );
    }

    /**
     * testDeletePreferredParentRelationship
     * @link https://familysearch.org/developers/docs/api/tree/Delete_Preferred_Parent_Relationship_usecase
     * @see this::testPreferredParentRelationship
     */

    /**
     * testDeletePreferredSpouseRelationship
     * @link https://familysearch.org/developers/docs/api/tree/Delete_Preferred_Spouse_Relationship_usecase
     * @see this::testPreferredSpouseRelationship
     */

    /**
     * testUploadPhotoForPerson
     * @link https://familysearch.org/developers/docs/api/tree/Upload_Photo_for_Person_usecase
     * @see MemoriesTests::uploadPhoto
     */

    /**
     * @link https://familysearch.org/developers/docs/api/tree/Delete_Person_usecase
     * @link https://familysearch.org/developers/docs/api/tree/Read_Deleted_Person_usecase
     * @link https://familysearch.org/developers/docs/api/tree/Restore_Person_usecase
     */
    public function testDeleteAndRestorePerson()
    {
        $factory = new StateFactory();
        $this->collectionState($factory);

        /** @var PersonState $personState */
        $personState = $this->createPerson()->get();

        $newState = $personState->delete();
        $this->assertEquals(
            HttpStatus::NO_CONTENT,
            $newState->getResponse()->getStatusCode(),
            $this->buildFailMessage(__METHOD__."(delete)", $newState)
        );

        /** @var \Gedcomx\Conclusion\Person[] $persons */
        $persons = $personState->getEntity()->getPersons();
        $id = $persons[0]->getId();
        $newState = $this->getPerson($id);
        $this->assertEquals(
            HttpStatus::GONE,
            $newState->getResponse()->getStatusCode(),
            $this->buildFailMessage(__METHOD__.'(read)', $newState)
        );

        $factory = new FamilyTreeStateFactory();
        $ftOne = $this->collectionState($factory);
        $ftTwo = $ftOne->readPersonById($id);
        $ftThree = $ftTwo->restore();
        $this->assertEquals(
            HttpStatus::NO_CONTENT,
            $ftThree->getResponse()->getStatusCode(),
            $this->buildFailMessage(__METHOD__.'(restore)', $ftThree)
        );
    }

    /**
     * @link https://familysearch.org/developers/docs/api/tree/Update_Preferred_Parent_Relationship_usecase
     * @link https://familysearch.org/developers/docs/api/tree/Read_Preferred_Parent_Relationship_usecase
     * @link https://familysearch.org/developers/docs/api/tree/Delete_Preferred_Parent_Relationship_usecase
     */
    public function testPreferredParentRelationship()
    {
        $factory = new FamilyTreeStateFactory();
        $this->collectionState($factory);

        $userState = $this->collectionState()->readCurrentUser();

        // First create a relationship

        $father = $this->createPerson('male');
        $this->assertEquals(
            HttpStatus::CREATED,
            $father->getResponse()->getStatusCode(),
            $this->buildFailMessage(__METHOD__."(father)", $father)
        );
        $child = $this->createPerson();
        $this->assertEquals(
            HttpStatus::CREATED,
            $child->getResponse()->getStatusCode(),
            $this->buildFailMessage(__METHOD__."(child)", $child)
        );
        $family = $this->collectionState()->addChildAndParents($child, $father);
        $this->assertEquals(
            HttpStatus::CREATED,
            $family->getResponse()->getStatusCode(),
            $this->buildFailMessage(__METHOD__."(family)", $family)
        );
        $this->queueForDelete($family);

        $child = $child->get();
        $this->assertEquals(
            HttpStatus::OK,
            $child->getResponse()->getStatusCode(),
            $this->buildFailMessage(__METHOD__."(child)", $child)
        );
        $this->assertNotNull($child->getEntity(), "Get failed. Entity is null.");
        $this->assertNotNull($child->getPerson(), "Get failed. Person is null.");

        //  Set the preferred relationship

        $updated = $this->collectionState()->updatePreferredParentRelationship(
            $userState->getUser()->getTreeUserId(),
            $child->getPerson()->getId(),
            $family
        );
        $this->assertEquals(
            HttpStatus::NO_CONTENT,
            $updated->getResponse()->getStatusCode(),
            $this->buildFailMessage(__METHOD__, $updated)
        );

        //  Read the preferred state

        /** @var ChildAndParentsRelationshipState $preferred */
        $preferred = $this->collectionState()->readPreferredParentRelationship(
            $userState->getUser()->getTreeUserId(),
            $child->getPerson()->getId()
        );

        //  readPreferredParentRelationship returns a '303/See Other' response which
        //  the HTTP client will follow. We'll test to make sure that the effective
        //  URL on the response contains 'child-and-parents-relationship' which indicates we've
        //  been bounced to the preferred relationship.

        $this->assertContains(
            'child-and-parents-relationship',
            $preferred->getResponse()->getEffectiveUrl(),
            $this->buildFailMessage(__METHOD__, $preferred)
        );

        //  Now clean up

        $updated = $this->collectionState()->deletePreferredSpouseRelationship(
            $userState->getUser()->getTreeUserId(),
            $child->getPerson()->getId()
        );
        $this->assertEquals(
            HttpStatus::NO_CONTENT,
            $updated->getResponse()->getStatusCode(),
            $this->buildFailMessage(__METHOD__, $updated)
        );
    }

    /**
     * @link https://familysearch.org/developers/docs/api/tree/Update_Preferred_Spouse_Relationship_usecase
     * @link https://familysearch.org/developers/docs/api/tree/Read_Preferred_Spouse_Relationship_usecase
     * @link https://familysearch.org/developers/docs/api/tree/Delete_Preferred_Spouse_Relationship_usecase
     */
    public function testPreferredSpouseRelationship()
    {
        $factory = new FamilyTreeStateFactory();
        $this->collectionState($factory);

        $userState = $this->collectionState()->readCurrentUser();

        // First create a relationship

        $husband = $this->createPerson('male');
        $this->assertEquals(
            HttpStatus::CREATED,
            $husband->getResponse()->getStatusCode(),
            $this->buildFailMessage(__METHOD__."(husband)", $husband)
        );
        $wife = $this->createPerson('female');
        $this->assertEquals(
            HttpStatus::CREATED,
            $wife->getResponse()->getStatusCode(),
            $this->buildFailMessage(__METHOD__."(wife)", $wife)
        );
        $family = $this->collectionState()->addSpouseRelationship($husband, $wife);
        $this->assertEquals(
            HttpStatus::CREATED,
            $family->getResponse()->getStatusCode(),
            $this->buildFailMessage(__METHOD__."(family)", $family)
        );
        $this->queueForDelete($family);

        $husband = $husband->get();
        $this->assertEquals(
            HttpStatus::OK,
            $husband->getResponse()->getStatusCode(),
            $this->buildFailMessage(__METHOD__."(get)", $husband)
        );
        $this->assertNotNull($husband->getEntity(), "Get failed. Entity is null.");
        $this->assertNotNull($husband->getPerson(), "Get failed. Person is null.");

        // Set the preferred relationship

        $updated = $this->collectionState()->updatePreferredSpouseRelationship(
            $userState->getUser()->getTreeUserId(),
            $husband->getPerson()->getId(),
            $family
        );
        $this->assertAttributeEquals(
            HttpStatus::NO_CONTENT,
            "statusCode",
            $updated->getResponse(),
            $this->buildFailMessage(__METHOD__, $updated)
        );

        // Read the preferred state

        /** @var ChildAndParentsRelationshipState $preferred */
        $preferred = $this->collectionState()->readPreferredSpouseRelationship(
            $userState->getUser()->getTreeUserId(),
            $husband->getPerson()->getId()
        );

         // readPreferredSpouseRelationship returns a '303/See Other' response which
         // the HTTP client will follow. We'll test to make sure that the effective
         // URL on the response contains 'couple-relationship' which indicates we've
         // been bounced to the preferred relationship.

        $this->assertContains(
            'couple-relationship',
            $preferred->getResponse()->getEffectiveUrl(),
            $this->buildFailMessage(__METHOD__, $preferred)
        );

        // Now clean up

        $updated = $this->collectionState()->deletePreferredSpouseRelationship(
            $userState->getUser()->getTreeUserId(),
            $husband->getPerson()->getId()
        );
        $this->assertEquals(
            HttpStatus::NO_CONTENT,
            $updated->getResponse()->getStatusCode(),
            $this->buildFailMessage(__METHOD__, $updated)
        );
    }
}