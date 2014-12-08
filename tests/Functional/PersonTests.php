<?php

namespace Gedcomx\Tests\Functional;

use Gedcomx\Conclusion\Fact;
use Gedcomx\Conclusion\Gender;
use Gedcomx\Conclusion\Person;
use Gedcomx\Conclusion\Relationship;
use Gedcomx\Extensions\FamilySearch\Platform\Tree\ChildAndParentsRelationship;
use Gedcomx\Extensions\FamilySearch\Platform\Tree\DiscussionReference;
use Gedcomx\Extensions\FamilySearch\Platform\Tree\Merge;
use Gedcomx\Extensions\FamilySearch\Rs\Client\FamilyTree\FamilyTreeStateFactory;
use Gedcomx\Extensions\FamilySearch\Rs\Client\Rel;
use Gedcomx\Extensions\FamilySearch\Types\FactType;
use Gedcomx\Rs\Client\Options\HeaderParameter;
use Gedcomx\Rs\Client\Options\Preconditions;
use Gedcomx\Rs\Client\Options\QueryParameter;
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

        $person = PersonBuilder::buildPerson($this->faker);
        $personState = $this->collectionState()->addPerson( $person );

        $this->assertEquals(
            HttpStatus::CREATED,
            $personState->getResponse()->getStatusCode(),
            $this->buildFailMessage(__METHOD__, $personState));

        $personState->delete();
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

        $personState = $this->createPerson();
        if( $personState->getPerson() == null ){
            $uri = $personState->getSelfUri();
            $personState = $this->collectionState()->readPerson($uri);
        }
        $fact = FactBuilder::militaryService();
        $newState = $personState->addFact($fact);

        $this->assertAttributeEquals(HttpStatus::NO_CONTENT, "statusCode", $newState->getResponse() );
    }

    /**
     * @link https://familysearch.org/developers/docs/api/tree/Create_Person_Life_Sketch_usecase
     */
    public function testCreatePersonLifeSketch()
    {
        $factory = new FamilyTreeStateFactory();
        $this->collectionState($factory);

        $personState = $this->createPerson();
        if( $personState->getPerson() == null ){
            $uri = $personState->getSelfUri();
            $personState = $this->collectionState()->readPerson($uri);
        }
        $fact = FactBuilder::lifeSketch();
        $newState = $personState->addFact($fact);

        $this->assertAttributeEquals(HttpStatus::NO_CONTENT, "statusCode", $newState->getResponse() );
    }

    /**
     * @link https://familysearch.org/developers/docs/api/tree/Create_Discussion_Reference_usecase
     */
    public function testCreateDiscussionReference(){
        $factory = new FamilyTreeStateFactory();
        $this->collectionState($factory);

        $userState = $this->collectionState()->readCurrentUser();
        $discussion = DiscussionBuilder::createDiscussion($userState->getUser()->getTreeUserId());

        $discussionState = $this->collectionState()->addDiscussion($discussion);

        $personState = $this->getPerson();
        /** @var \Gedcomx\Rs\Client\PersonState $newState */
        $newState = $personState->addDiscussionState($discussionState);

        $this->assertAttributeEquals(HttpStatus::CREATED, "statusCode", $newState->getResponse(), $this->buildFailMessage(__METHOD__, $newState) );
    }

    /**
     * @link https://familysearch.org/developers/docs/api/tree/Create_Note_usecase
     * @see NotesTests::testCreateNote
     */

    /**
     * @link https://familysearch.org/developers/docs/api/tree/Read_Merged_Person_usecase
     */
    public function testReadMergedPerson(){
        // KWWV-DN4 was merged with KWWN-MQY
        $factory = new StateFactory();
        $this->collectionState($factory);

        $personState = $this->getPerson('KWWV-DN4');
        /**
         * This assertion--technically the correct response for a person that has been merged--
         * assumes that the HTTP client code does not automatically follow redirects.
         *
         * $this->assertAttributeEquals(HttpStatus::MOVED_PERMANENTLY, "statusCode", $personState->getResponse() );
         *
         * Hacking the code to disable the redirect feature for this test seems undesirable. Instead we'll
         * assert that an id different from the one we requested is returned.
         */
        /** @var \Gedcomx\Conclusion\Person $person */
        $person = $personState->getPerson();

        $this->assertNotEquals( 'KWWV-DN4', $person->getId() );
    }

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

        $personState = $this->getPerson();

        $this->assertAttributeEquals(HttpStatus::OK, "statusCode", $personState->getResponse() );
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

        /** @var \Gedcomx\Extensions\FamilySearch\Rs\Client\PersonMergeState $analysis */
        $analysis = $stateOne->readMergeAnalysis($stateTwo);
        $this->assertEquals(
            HttpStatus::OK,
            $analysis->getResponse()->getStatusCode(),
            $this->buildFailMessage(__METHOD__, $analysis)
        );
        $this->assertNotEmpty($analysis->getAnalysis());

        $stateTwo->delete();
        $stateOne->delete();
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

        /** @var \Gedcomx\Extensions\FamilySearch\Rs\Client\PersonMergeState $state */
        $state = $person1->readMergeOptions($person2);
        $this->assertEquals(
            HttpStatus::OK,
            $state->getResponse()->getStatusCode(),
            $this->buildFailMessage(__METHOD__, $state)
        );
        $this->assertFalse($state->isAllowed(), $this->buildFailMessage(__METHOD__, $state));

        $person1->delete();
        $person2->delete();
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
        $person = $this->createPerson()->get();

        $filename = ArtifactBuilder::makeTextFile();
        $artifact = new DataSource();
        $artifact->setFile($filename);
        $person->addArtifact($artifact);
        $person = $person->get();
        $memories = $person->readArtifacts();

        $this->assertEquals(
            HttpStatus::OK,
            $memories->getResponse()->getStatusCode(),
            $this->buildFailMessage(__METHOD__, $memories)
        );

        $person->delete();
    }

    /**
     * @link https://familysearch.org/developers/docs/api/tree/Read_Person_Memories_By_Type._usecase
     */
    public  function testReadPersonMemoriesByType()
    {
        $factory = new FamilyTreeStateFactory();
        $this->collectionState($factory);
        $person = $this->createPerson()->get();

        $filename = ArtifactBuilder::makeTextFile();
        $artifact = new DataSource();
        $artifact->setFile($filename);
        $person->addArtifact($artifact);
        $person = $person->get();

        $option = new QueryParameter(true, "type", "photo");
        $memories = $person->readArtifacts($option);
        $this->assertEquals(
            HttpStatus::OK,
            $memories->getResponse()->getStatusCode(),
            $this->buildFailMessage(__METHOD__, $memories)
        );
        $this->assertEmpty($memories->getEntity()->getSourceDescriptions());

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

        $person = $this->getPerson();
        /** @var \Guzzle\Http\Message\Response $response */
        $response = $person->readPortrait();

        $this->assertEquals(
            HttpStatus::NO_CONTENT,
            $response->getStatusCode(),
            'Get portrait failed. Returned: ' . HttpStatus::getText($response->getStatusCode()) . "(" . $response->getStatusCode() . ")"
        );
    }

    /**
     * @link https://familysearch.org/developers/docs/api/tree/Read_Person_Portrait_With_Default_usecase
     */
    public function testReadPortraitWithDefault()
    {
        $factory = new FamilyTreeStateFactory();
        $this->collectionState($factory);

        $person = $this->getPerson();
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

        $person = $this->getPerson();
        $portraits = $person->readPortraits();

        $this->assertEquals(
            HttpStatus::OK,
            $portraits->getResponse()->getStatusCode(),
            $this->buildFailMessage(__METHOD__, $portraits)
        );
    }

    /**
     * @link https://familysearch.org/developers/docs/api/tree/Read_Person_With_Relationships_usecase
     */
    public function restReadPersonWithRelationships()
    {
        $factory = new FamilyTreeStateFactory();
        $this->collectionState($factory);

        $person = $this->collectionState()->readPersonWithRelationshipsById($this->getPersonId());
        $this->assertAttributeEquals(HttpStatus::OK, "statusCode", $person->getResponse(), "Restore person failed. Returned {$person->getResponse()->getStatusCode()}");

        $thePerson = $person->getPerson();
        $ftRelationships = $person->getChildAndParentsRelationships();
        $relationships = $person->getRelationships();

        $data_check = $thePerson instanceof Person
            && count($ftRelationships) > 0
            && $ftRelationships[0] instanceof ChildAndParentsRelationship
            && count($relationships) > 0
            && $relationships[0] instanceof Relationship;
        $this->assertTrue($data_check);
    }

    /**
     * @link https://familysearch.org/developers/docs/api/tree/Read_Relationships_to_Children_usecase
     */
    public function testReadRelationshipsToChildren(){
        $factory = new StateFactory();
        $this->collectionState($factory);

        $personState = $this->getPerson();
        $personState
            ->loadChildRelationships();

        $this->assertAttributeEquals(HttpStatus::OK, "statusCode", $personState->getResponse() );
    }

    /**
     * @link https://familysearch.org/developers/docs/api/tree/Read_Relationships_to_Parents_usecase
     */
    public function testReadRelationshipsToParents(){
        $factory = new StateFactory();
        $this->collectionState($factory);

        $personState = $this->getPerson();
        $personState
            ->loadParentRelationships();

        $this->assertAttributeEquals(HttpStatus::OK, "statusCode", $personState->getResponse() );
    }

    /**
     * @link https://familysearch.org/developers/docs/api/tree/Read_Relationships_To_Spouses_usecase
     */
    public function testReadRelationshipsToSpouses(){
        $factory = new StateFactory();
        $this->collectionState($factory);

        $personState = $this->getPerson();
        $personState
            ->loadSpouseRelationships();

        $this->assertAttributeEquals(HttpStatus::OK, "statusCode", $personState->getResponse() );
    }

    /**
     * @link https://familysearch.org/developers/docs/api/tree/Read_Relationships_To_Spouses_with_Persons_usecase
     */
    public function testReadRelationshipsToSpousesWithPersons(){
        $factory = new StateFactory();
        $this->collectionState($factory);

        $personState = $this->getPerson();
        $option = new QueryParameter(true,"persons","");
        $personState
            ->loadSpouseRelationships($option);

        $this->assertAttributeEquals(HttpStatus::OK, "statusCode", $personState->getResponse() );
    }

    /**
     * @link https://familysearch.org/developers/docs/api/tree/Create_Discussion_Reference_usecase
     * @link https://familysearch.org/developers/docs/api/tree/Read_Discussion_References_usecase
     */
    public function testReadDiscussionReference(){
        $factory = new FamilyTreeStateFactory();
        $this->collectionState($factory);

        $userState = $this->collectionState()->readCurrentUser();
        $discussion = DiscussionBuilder::createDiscussion($userState->getUser()->getTreeUserId());

        $discussionState = $this->collectionState()->addDiscussion($discussion);

        $personState = $this->getPerson();
        $newState = $personState->addDiscussionState($discussionState);

        $this->assertAttributeEquals(HttpStatus::CREATED, "statusCode", $newState->getResponse(), $this->buildFailMessage(__METHOD__, $newState) );

        $personState->loadDiscussionReferences();

        $found = false;
        foreach ($personState->getPerson()->getExtensionElements() as $ext) {
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
        $factory = new StateFactory();
        $this->collectionState($factory);

        $personState = $this->getPerson();
        $childrenState = $personState
            ->readChildren();

        $this->assertAttributeEquals(HttpStatus::OK, "statusCode", $childrenState->getResponse() );
    }

    /**
     * @link https://familysearch.org/developers/docs/api/tree/Read_Not_Found_Person_usecase
     */
    public function testReadNotFoundPerson(){
        $factory = new StateFactory();
        $this->collectionState($factory);

        $personState = $this->getPerson('MMMM-NNN');
        $this->assertAttributeEquals(HttpStatus::NOT_FOUND, "statusCode", $personState ->getResponse() );
    }

    /**
     * @link https://familysearch.org/developers/docs/api/tree/Read_Not-Modified_Person_usecase
     */
    public function testReadNotModifiedPerson(){
        $factory = new StateFactory();
        $this->collectionState($factory);

        $personState = $this->getPerson();
        $options = array();
        $options[] = new HeaderParameter(true, HeaderParameter::IF_NONE_MATCH, $personState->getResponse()->getEtag());
        $options[] = new HeaderParameter(true, HeaderParameter::ETAG, $personState->getResponse()->getEtag());

        $secondState = $this->getPerson($personState->getPerson()->getId(), $options);

        $this->assertAttributeEquals(HttpStatus::NOT_MODIFIED, "statusCode", $secondState->getResponse() );
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
        $factory = new StateFactory();
        $this->collectionState($factory);

        $personState = $this->getPerson();
        $parentState = $personState
            ->readParents();

        $this->assertAttributeEquals(HttpStatus::OK, "statusCode", $parentState->getResponse() );
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

        $personState = $this->getPerson();
        $spouseState = $personState
            ->readSpouses();

        $this->assertAttributeEquals(HttpStatus::OK, "statusCode", $spouseState->getResponse());
    }

    /**
     * @link https://familysearch.org/developers/docs/api/tree/Merge_Person_usecase
     */
    public function testMergePerson()
    {
        $factory = new FamilyTreeStateFactory();
        $this->collectionState($factory);

        $person = PersonBuilder::buildPerson('male');
        $person1 = $this->collectionState()->addPerson($person)->get();
        $person2 = $this->collectionState()->addPerson($person)->get();

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

        $person1 = $person1->get();
        $person2 = $person2->get();
        $this->assertEquals(
            $person1->getSelfUri(),
            $person2->getSelfUri(),
            "Person URIs don't match."
        );

        $person1->delete();
        $person2->delete();
    }

    /**
     * @link https://familysearch.org/developers/docs/api/tree/Head_Person_usecase
     */
    public function testHeadPerson()
    {
        $factory = new StateFactory();
        $this->collectionState($factory);

        $personState = $this->getPerson();
        $newState = $personState->head();

        $this->assertAttributeEquals(HttpStatus::OK, "statusCode", $newState->getResponse());
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

        $personState = $this->createPerson();
        if( $personState->getPerson() == null ){
            $uri = $personState->getSelfUri();
            $personState = $this->collectionState()->readPerson($uri);
        }
        $gender = new Gender(array(
            "type" =>GenderType::MALE
        ));
        $personState->updateGender($gender);

        $this->assertAttributeEquals(HttpStatus::OK, "statusCode", $personState->getResponse());

    }

    /**
     * @link https://familysearch.org/developers/docs/api/tree/Update_Person_Custom_Non-Event_Fact_usecase
     */
    public function testUpdatePersonCustomNonEventFact()
    {
        $factory = new StateFactory();
        $this->collectionState($factory);

        $personState = $this->createPerson();
        if( $personState->getPerson() == null ){
            $uri = $personState->getSelfUri();
            $personState = $this->collectionState()->readPerson($uri);
        }
        $fact = FactBuilder::eagleScout();
        $newState = $personState->addFact($fact);

        $this->assertAttributeEquals(HttpStatus::NO_CONTENT, "statusCode", $newState->getResponse());
    }

    /**
     * @link https://familysearch.org/developers/docs/api/tree/Update_Person_Life_Sketch_usecase
     */
    public function testUpdatePersonLifeSketch()
    {
        $factory = new FamilyTreeStateFactory();
        $this->collectionState($factory);

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

        $this->assertAttributeEquals(HttpStatus::NO_CONTENT, "statusCode", $newState->getResponse() );
    }

    /**
     * @link https://familysearch.org/developers/docs/api/tree/Update_Person_Not-a-Match_Declarations_usecase
     */
    public function testUpdatePersonNotAMatchDeclarations()
    {
        $factory = new FamilyTreeStateFactory();
        $this->collectionState($factory);

        $personData = PersonBuilder::buildPerson(null);

        $one = $this->collectionState()->addPerson($personData)->get();
        $two = $this->collectionState()->addPerson($personData)->get();

        $nonMatch = $one->addNonMatchPerson($two->getPerson());
        $this->assertAttributeEquals(HttpStatus::NO_CONTENT, "statusCode", $nonMatch->getResponse(), "Restore person failed. Returned {$nonMatch->getResponse()->getStatusCode()}");
    }

    /**
     * @link https://familysearch.org/developers/docs/api/tree/Update_Person_With_Preconditions_usecase
     */
    public function testUpdatePersonWithPreconditions()
    {
        $factory = new StateFactory();
        $this->collectionState($factory);

        $personState = $this->createPerson()->get();

        $mangled = str_replace(array(1,3,5,'a','b','d'), array(8,4,3,'Z','X','W'), $personState->getResponse()->getEtag());
        $check = new Preconditions();
        $check->setEtag($mangled);
        $check->setLastModified(new \DateTime($personState->getResponse()->getLastModified()));

        $persons = $personState->getEntity()->getPersons();
        $state = $personState->update($persons[0], $check);
        $this->assertAttributeEquals(HttpStatus::PRECONDITION_FAILED, "statusCode", $state->getResponse());
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

        $personState = $this->createPerson();
        if( $personState->getPerson() == null ){
            $uri = $personState->getSelfUri();
            $personState = $this->collectionState()->readPerson($uri);
        }
        $name = PersonBuilder::nickName();
        $newPersonState = $personState->addName($name);

        $this->assertAttributeEquals(HttpStatus::NO_CONTENT, "statusCode", $newPersonState->getResponse() );
        $newPersonState = $personState->get();

        /** @var \Gedcomx\Conclusion\Person[] $persons */
        $persons = $newPersonState->getEntity()->getPersons();
        $names = $persons[0]->getNames();
        $deletedState = $newPersonState->deleteName($names[1]);

        $this->assertAttributeEquals(HttpStatus::NO_CONTENT, "statusCode", $deletedState->getResponse());
    }

    /**
     * @link https://familysearch.org/developers/docs/api/tree/Delete_Person_Not-a-Match_usecase
     */
    public function testDeletePersonNotAMatch()
    {
        $factory = new FamilyTreeStateFactory();
        $this->collectionState($factory);

        $personData = PersonBuilder::buildPerson(null);

        $one = $this->collectionState()->addPerson($personData)->get();
        $two = $this->collectionState()->addPerson($personData)->get();

        $nonMatch = $one->addNonMatchPerson($two->getPerson());
        $rematch = $nonMatch->removeNonMatch($two->getPerson());

        $this->assertAttributeEquals(HttpStatus::NO_CONTENT, "statusCode", $rematch->getResponse(), "Restore person failed. Returned {$rematch->getResponse()->getStatusCode()}");
    }

    /**
     * @link https://familysearch.org/developers/docs/api/tree/Delete_Person_With_Preconditions_usecase
     */
    public function testDeletePersonWithPreconditions()
    {
        $factory = new StateFactory();
        $this->collectionState($factory);

        $personState = $this->createPerson()->get();

        $mangled = str_replace(array(1,3,5,'a','b','d'), array(8,4,3,'Z','X','W'), $personState->getResponse()->getEtag());
        $check = new Preconditions();
        $check->setEtag($mangled);
        $check->setLastModified(new \DateTime($personState->getResponse()->getLastModified()));

        $dState = $personState->delete($check);
        $this->assertAttributeEquals(HttpStatus::PRECONDITION_FAILED, "statusCode", $dState->getResponse());
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

        $discussionState = $this->collectionState()->addDiscussion($discussion);
        $ref = new DiscussionReference();
        $ref->setResource($discussionState->getSelfUri());

        $personState = $this->getPerson();
        $newState = $personState->deleteDiscussionReference($ref);

        $this->assertAttributeEquals(HttpStatus::NO_CONTENT, "statusCode", $newState->getResponse(), $this->buildFailMessage(__METHOD__, $newState) );
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

        $personState = $this->createPerson()->get();
        $newState = $personState->delete();
        $this->assertAttributeEquals(HttpStatus::NO_CONTENT, "statusCode", $newState->getResponse(), "Delete person failed. Returned {$newState->getResponse()->getStatusCode()}");

        /** @var \Gedcomx\Conclusion\Person[] $persons */
        $persons = $personState->getEntity()->getPersons();
        $id = $persons[0]->getId();
        $newState = $this->getPerson($id);
        $this->assertAttributeEquals(HttpStatus::GONE, "statusCode", $newState->getResponse(), "Read deleted person failed. Returned {$newState->getResponse()->getStatusCode()}");

        $factory = new FamilyTreeStateFactory();
        $ftOne = $this->collectionState($factory);
        $ftTwo = $ftOne->readPersonById($id);
        $ftThree = $ftTwo->restore();
        $this->assertAttributeEquals(HttpStatus::NO_CONTENT, "statusCode", $ftThree->getResponse(), "Restore person failed. Returned {$ftThree->getResponse()->getStatusCode()}");
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

        /* First create a relationship */
        $person1 = $this->createPerson('male')->get();
        $person2 = $this->createPerson('male')->get();
        $relation = $this->collectionState()->addChildAndParents($person1, $person2);
        $this->assertAttributeEquals(HttpStatus::CREATED, "statusCode", $relation->getResponse(), $this->buildFailMessage(__METHOD__, $relation));

        /* Set the preferred relationship */
        $updated = $this->collectionState()->updatePreferredParentRelationship(
            $userState->getUser()->getTreeUserId(),
            $person1->getPerson()->getId(),
            $relation
        );
        $this->assertAttributeEquals(HttpStatus::NO_CONTENT, "statusCode", $updated->getResponse(), $this->buildFailMessage(__METHOD__, $updated));

        /* Read the preferred state */
        $preferred = $this->collectionState()->readPreferredParentRelationship(
            $userState->getUser()->getTreeUserId(),
            $person1->getPerson()->getId()
        );
        /*
         * readPreferredParentRelationship returns a '303/See Other' response which
         * the HTTP client will follow. We'll test to make sure that the effective
         * URL on the response contains 'child-and-parents-relationship' which indicates we've
         * been bounced to the preferred relationship.
         */
        $this->assertAttributeContains('child-and-parents-relationship', "effectiveUrl", $preferred->getResponse(), $this->buildFailMessage(__METHOD__, $preferred));

        /* Now clean up */
        $updated = $this->collectionState()->deletePreferredSpouseRelationship(
            $userState->getUser()->getTreeUserId(),
            $person1->getPerson()->getId()
        );
        $this->assertAttributeEquals(HttpStatus::NO_CONTENT, "statusCode", $updated->getResponse(), $this->buildFailMessage(__METHOD__, $updated));

        $relation->delete();
        $person1->delete();
        $person2->delete();
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

        /* First create a relationship */
        $person1 = $this->createPerson('male')->get();
        $person2 = $this->createPerson('female')->get();
        $relation = $this->collectionState()->addSpouseRelationship($person1, $person2);
        $this->assertAttributeEquals(HttpStatus::CREATED, "statusCode", $relation->getResponse(), $this->buildFailMessage(__METHOD__, $relation));

        /* Set the preferred relationship */
        $updated = $this->collectionState()->updatePreferredSpouseRelationship(
            $userState->getUser()->getTreeUserId(),
            $person1->getPerson()->getId(),
            $relation
        );
        $this->assertAttributeEquals(HttpStatus::NO_CONTENT, "statusCode", $updated->getResponse(), $this->buildFailMessage(__METHOD__, $updated));

        /* Read the preferred state */
        $preferred = $this->collectionState()->readPreferredSpouseRelationship(
            $userState->getUser()->getTreeUserId(),
            $person1->getPerson()->getId()
        );
        /*
         * readPreferredSpouseRelationship returns a '303/See Other' response which
         * the HTTP client will follow. We'll test to make sure that the effective
         * URL on the response contains 'couple-relationship' which indicates we've
         * been bounced to the preferred relationship.
         */
        $this->assertAttributeContains('couple-relationship', "effectiveUrl", $preferred->getResponse(), $this->buildFailMessage(__METHOD__, $preferred));

        /* Now clean up */
        $updated = $this->collectionState()->deletePreferredSpouseRelationship(
            $userState->getUser()->getTreeUserId(),
            $person1->getPerson()->getId()
        );
        $this->assertAttributeEquals(HttpStatus::NO_CONTENT, "statusCode", $updated->getResponse(), $this->buildFailMessage(__METHOD__, $updated));

        $relation->delete();
        $person1->delete();
        $person2->delete();
    }
}