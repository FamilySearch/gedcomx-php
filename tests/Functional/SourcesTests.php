<?php

namespace Gedcomx\Tests\Functional;

use Gedcomx\Common\Attribution;
use Gedcomx\Common\Note;
use Gedcomx\Common\ResourceReference;
use Gedcomx\Common\TextValue;
use Gedcomx\Extensions\FamilySearch\FamilySearchPlatform;
use Gedcomx\Extensions\FamilySearch\Platform\Tree\ChildAndParentsRelationship;
use Gedcomx\Extensions\FamilySearch\Rs\Client\FamilyTree\ChildAndParentsRelationshipState;
use Gedcomx\Extensions\FamilySearch\Rs\Client\FamilyTree\FamilyTreeCollectionState;
use Gedcomx\Extensions\FamilySearch\Rs\Client\FamilyTree\FamilyTreeRelationshipState;
use Gedcomx\Extensions\FamilySearch\Rs\Client\FamilyTree\FamilyTreeStateFactory;
use Gedcomx\Extensions\FamilySearch\Rs\Client\Rel;
use Gedcomx\Gedcomx;
use Gedcomx\Rs\Client\PersonState;
use Gedcomx\Rs\Client\RelationshipState;
use Gedcomx\Rs\Client\SourceDescriptionState;
use Gedcomx\Rs\Client\StateFactory;
use Gedcomx\Rs\Client\Util\DataSource;
use Gedcomx\Rs\Client\Util\HttpStatus;
use Gedcomx\Source\SourceCitation;
use Gedcomx\Source\SourceDescription;
use Gedcomx\Tests\ApiTestCase;
use Gedcomx\Tests\ArtifactBuilder;
use Gedcomx\Tests\SourceBuilder;
use Gedcomx\Extensions\FamilySearch\Rs\Client\FamilySearchSourceDescriptionState;
use Gedcomx\Extensions\FamilySearch\Rs\Client\FamilyTree\FamilyTreePersonState;
use Gedcomx\Source\SourceReference;
use Guzzle\Http\Message\Request;
use Gedcomx\Tests\TestBuilder;

class SourcesTests extends ApiTestCase
{
    public function setUp(){
        parent::setUp();
        $this->faker->seed(9451872);
        TestBuilder::seed(9451872);
    }
    
    /**
     * @vcr SourcesTests/testCreatePersonSourceReference.json
     * @link https://familysearch.org/developers/docs/api/tree/Create_Person_Source_Reference_usecase
     */
    public function testCreatePersonSourceReference()
    {
        $factory = new StateFactory();
        $this->collectionState($factory);

        /** @var PersonState $personState */
        $personState = $this->createPerson();
        $this->assertEquals(HttpStatus::CREATED, $personState->getResponse()->getStatusCode() );
        $personState = $personState->get();
        $this->assertEquals(HttpStatus::OK, $personState->getResponse()->getStatusCode() );
        $sourceState = $this->createSource();
        $this->assertEquals(HttpStatus::CREATED, $sourceState->getResponse()->getStatusCode() );

        $reference = new SourceReference();
        $reference->setDescriptionRef($sourceState->getSelfUri());
        $reference->setAttribution( new Attribution( array("changeMessage" => $this->faker->sentence(6))));
        /** @var \Gedcomx\Rs\Client\PersonState $newState */
        $newState = $personState->addSourceReferenceObj($reference);
        $this->assertEquals(HttpStatus::CREATED, $newState->getResponse()->getStatusCode() );

        $personState = $personState->get();
        $this->assertEquals(HttpStatus::OK, $personState->getResponse()->getStatusCode() );
        $this->assertNotNull($personState->getEntity());
        $this->assertNotEmpty($personState->getEntity()->getSourceDescriptions());
    }

    /**
     * @vcr SourcesTests/testCreateSourceDescription.json
     * @link https://familysearch.org/developers/docs/api/sources/Create_Source_Description_usecase
     */
    public function testCreateSourceDescription()
    {
        $this->collectionState(new StateFactory());
        /** @var SourceDescription $source */
        $source = SourceBuilder::newSource();
        $link = $this->collectionState()->getLink(Rel::SOURCE_DESCRIPTIONS);
        $this->assertNotNull($link, "SOURCE_DESCRIPTION rel not found on this collection.");

        $sourceState = $this->collectionState()->addSourceDescription($source);
        $this->assertEquals(
            HttpStatus::CREATED,
            $sourceState->getResponse()->getStatusCode(),
            $this->buildFailMessage(__METHOD__ . "(CREATE)", $sourceState)
        );

        /** @var SourceDescriptionState $sourceState */
        $sourceState = $sourceState->get();
        $this->assertNotNull($sourceState->getEntity(), "Entity is null.");
        $this->assertNotNull($sourceState->getSourceDescription(), "SourceDescription should not be empty.");
    }

    /**
     * @vcr SourcesTests/testCreateChildAndParentsRelationshipSourceReferences.json
     * @link https://familysearch.org/developers/docs/api/tree/Create_Child-and-Parents_Relationship_Source_Reference_usecase
     */
    public function testCreateChildAndParentsRelationshipSourceReferences()
    {
        $factory = new FamilyTreeStateFactory();
        /** @var FamilyTreeCollectionState $collection */
        $this->collectionState($factory);

        /** @var ChildAndParentsRelationshipState $relation */
        $relation = $this->createRelationship();
        $relation = $relation->get();
        $this->assertEquals(
            HttpStatus::OK,
            $relation->getResponse()->getStatusCode(),
            $this->buildFailMessage(__METHOD__.':'.__LINE__, $relation)
        );
        $sourceState = $this->createSource();
        $this->assertEquals(
            HttpStatus::CREATED,
            $sourceState->getResponse()->getStatusCode(),
            $this->buildFailMessage(__METHOD__.':'.__LINE__, $sourceState)
        );
        $this->queueForDelete($sourceState);

        $reference = new SourceReference();
        $reference->setDescriptionRef($sourceState->getSelfUri());
        $reference->setAttribution( new Attribution( array(
                                                         "changeMessage" => $this->faker->sentence(6)
                                                     )));
        $newState = $relation->addSourceReference($reference);
        $this->assertEquals(
            HttpStatus::CREATED,
            $newState->getResponse()->getStatusCode(),
            $this->buildFailMessage(__METHOD__.':'.__LINE__, $newState)
        );

        $relation->loadSourceReferences();
        $this->assertNotEmpty($relation->getRelationship()->getSources());
    }

    /**
     * @vcr SourcesTests/testCreateCoupleRelationshipSourceReference.json
     * @link https://familysearch.org/developers/docs/api/tree/Create_Couple_Relationship_Source_Reference_usecase
     * @link https://familysearch.org/developers/docs/api/tree/Read_Couple_Relationship_Sources_usecase
     * @link https://familysearch.org/developers/docs/api/tree/Read_Couple_Relationship_Source_References_usecase
     */
    public function testCreateCoupleRelationshipSourceReference()
    {
        $factory = new FamilyTreeStateFactory();
        $this->collectionState($factory);

        $person1 = $this->createPerson('male');
        $this->assertEquals(
            HttpStatus::CREATED,
            $person1->getResponse()->getStatusCode(),
            $this->buildFailMessage(__METHOD__.':'.__LINE__, $person1)
        );
        $person1 = $person1->get();
        $this->assertEquals(
            HttpStatus::OK,
            $person1->getResponse()->getStatusCode(),
            $this->buildFailMessage(__METHOD__.':'.__LINE__, $person1)
        );
        $person2 = $this->createPerson('female');
        $this->assertEquals(
            HttpStatus::CREATED,
            $person2->getResponse()->getStatusCode(),
            $this->buildFailMessage(__METHOD__.':'.__LINE__, $person2)
        );
        $person2 = $person2->get();
        $this->assertEquals(
            HttpStatus::OK,
            $person2->getResponse()->getStatusCode(),
            $this->buildFailMessage(__METHOD__.':'.__LINE__, $person2)
        );

        /* Create Relationship */
        /** @var FamilyTreeRelationshipState $relation */
        $relation = $this->collectionState()->addSpouseRelationship($person1, $person2);
        $this->assertEquals(
            HttpStatus::CREATED,
            $relation->getResponse()->getStatusCode(),
            $this->buildFailMessage(__METHOD__.':'.__LINE__, $relation)
        );
        $this->queueForDelete($relation);

        $relation = $relation->get();
        $this->assertEquals(
            HttpStatus::OK,
            $relation->getResponse()->getStatusCode(),
            $this->buildFailMessage(__METHOD__.':'.__LINE__, $relation)
        );

        /* Create source */
        $sourceState = $this->createSource();
        $this->assertEquals(
            HttpStatus::CREATED,
            $sourceState->getResponse()->getStatusCode(),
            $this->buildFailMessage(__METHOD__.':'.__LINE__, $sourceState)
        );
        $this->queueForDelete($sourceState);

        $reference = new SourceReference();
        $reference->setDescriptionRef($sourceState->getSelfUri());
        $reference->setAttribution( new Attribution( array(
                                                         "changeMessage" => $this->faker->sentence(6)
                                                     )));

        /* CREATE the source reference on the relationship */
        $sourceRef = $relation->addSourceReference($reference);
        $this->assertEquals(
            HttpStatus::CREATED,
            $sourceRef->getResponse()->getStatusCode(),
            $this->buildFailMessage(__METHOD__.':'.__LINE__, $sourceRef)
        );
        $relation->loadSourceReferences();
        $this->assertNotEmpty($relation->getRelationship()->getSources());
    }

    /**
     * @link https://familysearch.org/developers/docs/api/sources/Create_User-Uploaded_Source_usecase
     */
    public function testCreateUserUploadedSource()
    {
        $this->markTestSkipped("Memories tests are slow and unreliable.");
        
        $this->collectionState(new FamilyTreeStateFactory());
        /** @var FamilyTreePersonState $person */
        $person = $this->createPerson();
        $this->assertEquals(
            HttpStatus::CREATED,
            $person->getResponse()->getStatusCode(),
            $this->buildFailMessage(__METHOD__.'(create person)', $person)
        );

        $person = $person->get();
        $this->assertEquals(
            HttpStatus::OK,
            $person->getResponse()->getStatusCode(),
            $this->buildFailMessage(__METHOD__.'(read person)', $person)
        );

        $ds = new DataSource();
        $ds->setTitle("Sample Memory");
        $ds->setFile(ArtifactBuilder::makeTextFile());
        $a1 = $person->addArtifact($ds);
        $this->assertEquals(
            HttpStatus::CREATED,
            $a1->getResponse()->getStatusCode(),
            $this->buildFailMessage(__METHOD__.'(add artifact)', $a1)
        );
        $this->queueForDelete($a1);
        $this->assertEquals(HttpStatus::CREATED, $a1->getResponse()->getStatusCode());

        $artifacts = $person->readArtifacts();
        $this->assertEquals(HttpStatus::OK, $artifacts->getResponse()->getStatusCode());

        /** @var \Gedcomx\Rs\Client\SourceDescriptionState $artifact */
        $artifact = $person->readArtifacts();
        $this->assertEquals(
            HttpStatus::OK,
            $artifact->getResponse()->getStatusCode(),
            $this->buildFailMessage(__METHOD__.'(read artifact)', $artifact)
        );
        $artifact = $artifact->getSourceDescription();

        $memoryUri = $artifact->getLink("memory")->getHref();
        $source = SourceBuilder::newSource();
        $source->setAbout($memoryUri);
        $state = $this->collectionState()->addSourceDescription($source);
        $this->queueForDelete($state);

        $this->assertNotNull($state->ifSuccessful());
        $this->assertEquals(
            HttpStatus::CREATED,
            $state->getResponse()->getStatusCode(),
            $this->buildFailMessage(__METHOD__.'(source description)', $state)
        );
    }

    /**
     * @vcr SourcesTests/testReadPersonSourceReferences.json
     * @link https://familysearch.org/developers/docs/api/tree/Read_Person_Source_References_usecase
     */
    public function testReadPersonSourceReferences(){
        $factory = new StateFactory();
        $this->collectionState($factory);

        //  Set up the data we need
        /** @var PersonState $testSubject */
        $testSubject = $this->createPerson();
        $this->assertAttributeEquals(HttpStatus::CREATED, "statusCode", $testSubject->getResponse() );
        $testSubject = $testSubject->get();
        $this->assertAttributeEquals(HttpStatus::OK, "statusCode", $testSubject->getResponse() );

        $source = SourceBuilder::hitchhiker();
        $sourceState = $this->collectionState()->addSourceDescription($source);
        $this->assertAttributeEquals(HttpStatus::CREATED, "statusCode", $sourceState->getResponse() );
        $this->queueForDelete($sourceState);

        $sourceRef = $testSubject->addSourceReferenceState($sourceState);
        $this->assertAttributeEquals(HttpStatus::CREATED, "statusCode", $sourceRef->getResponse() );

        //  Now test it
        $testSubject->loadSourceReferences();

        $this->assertAttributeEquals(HttpStatus::OK, "statusCode", $testSubject->getResponse() );
        $this->assertNotEmpty($testSubject->getEntity()->getSourceDescriptions());
    }

    /**
     * @vcr SourcesTests/testReadPersonSources.json
     * @link https://familysearch.org/developers/docs/api/tree/Read_Person_Sources_usecase
     */
    public function testReadPersonSources()
    {
        $factory = new FamilyTreeStateFactory();
        $this->collectionState($factory);
        $this->assertNotNull($this->collectionState());
        $this->assertTrue($this->collectionState()->isAuthenticated());
        $this->assertNotNull($this->collectionState()->getClient());
        $client = $this->collectionState()->getClient();
        $this->assertNotEmpty($this->collectionState()->getAccessToken());
        $token = $this->collectionState()->getAccessToken();
        /** @var FamilyTreePersonState $person */
        $person = $this->createPerson();
        $this->assertEquals(HttpStatus::CREATED, $person->getResponse()->getStatusCode());
        $person = $person->get();
        $this->assertEquals(HttpStatus::OK, $person->getResponse()->getStatusCode());
        $sds = $this->collectionState()->addSourceDescription(SourceBuilder::hitchhiker());
        $this->queueForDelete($sds);

        $person->addSourceReferenceState($sds);
        $linkObj = $person->getLink(Rel::SOURCE_DESCRIPTIONS);
        $this->assertNotNull($linkObj);
        $link = $linkObj->getHref();
        $this->assertNotEmpty($link);
        $request = $client->createRequest(Request::GET, $link);
        $request->setHeader('Accept', Gedcomx::JSON_MEDIA_TYPE);
        $request->setHeader('Authorization', "Bearer {$token}");
        $response = $client->send($request);
        $state = new FamilySearchSourceDescriptionState($client, $request, $response, $token, $factory);
        $this->queueForDelete($state);

        $this->assertNotNull($state->ifSuccessful());
        $this->assertEquals(HttpStatus::OK, $state->getResponse()->getStatusCode());
        $this->assertNotNull($state->getSourceDescription());
        $this->assertNotNull($state->getEntity());
        $this->assertNotNull($state->getEntity()->getPersons());
    }

    /**
     * @vcr SourcesTests/testReadSourceDescription.json
     * @link https://familysearch.org/developers/docs/api/sources/Read_Source_Description_usecase
     * @throws \Gedcomx\Rs\Client\Exception\GedcomxApplicationException
     */
    public function testReadSourceDescription()
    {
        $this->collectionState(new FamilyTreeStateFactory());
        $sd = SourceBuilder::newSource();
        /** @var SourceDescriptionState $description */
        $description = $this->collectionState()->addSourceDescription($sd);
        $this->assertEquals(HttpStatus::CREATED, $description->getResponse()->getStatusCode());
        $this->queueForDelete($description);

        $description = $description->get();
        $this->assertNotNull($description->ifSuccessful());
        $this->assertEquals(HttpStatus::OK, $description->getResponse()->getStatusCode());
        $this->assertNotNull($description->getSourceDescription());
    }

    /**
     * @vcr SourcesTests/testReadSourceReferences.json
     * @link https://familysearch.org/developers/docs/api/tree/Read_Source_References_usecase
     */
    public function testReadSourceReferences()
    {
        $factory = new FamilyTreeStateFactory();
        $this->collectionState($factory);
        $sd = SourceBuilder::hitchhiker();
        /** @var SourceDescriptionState $source */
        $source = $this->collectionState()->addSourceDescription($sd)->get();
        $this->queueForDelete($source);

        /** @var FamilyTreePersonState $person */
        $person = $this->createPerson();
        $sourceRef = new SourceReference();
        $sourceRef->setAttribution( new Attribution( array(
            "changeMessage" => $this->faker->sentence(6)
        )));
        $sourceRef->setDescriptionRef($source->getSelfUri());
        $person->addSourceReferenceObj($sourceRef);
        $state = $source->queryAttachedReferences();

        $this->assertNotNull($state->ifSuccessful());
        $this->assertEquals(HttpStatus::OK, $state->getResponse()->getStatusCode());
        $this->assertNotNull($state->getEntity());
        $this->assertNotNull($state->getEntity()->getPersons());
        $this->assertGreaterThan(0, count($state->getEntity()->getPersons()));
    }

    /**
     * testReadChildAndParentsRelationshipSourceReferences
     * @link https://familysearch.org/developers/docs/api/tree/Read_Child-and-Parents_Relationship_Source_References_usecase
     * @see SourcesTests::testCreateChildAndParentsRelationshipSourceReferences
     */

    /**
     * @vcr SourcesTests/testReadChildAndParentsRelationshipSources.json
     * @link https://familysearch.org/developers/docs/api/tree/Read_Child-and-Parents_Relationship_Sources_usecase
     * @see SourcesTests::testReadChildAndParentsRelationshipSources
     */
    public function testReadChildAndParentsRelationshipSources()
    {
        $factory = new FamilyTreeStateFactory();
        $this->collectionState($factory);
        $this->assertNotNull($this->collectionState());
        $this->assertTrue($this->collectionState()->isAuthenticated());
        $this->assertNotNull($this->collectionState()->getClient());
        $client = $this->collectionState()->getClient();
        $this->assertNotEmpty($this->collectionState()->getAccessToken());
        $token = $this->collectionState()->getAccessToken();
        /** @var FamilyTreePersonState $father */
        $father = $this->createPerson('male');
        $this->assertEquals(HttpStatus::CREATED, $father->getResponse()->getStatusCode());
        $father = $father->get();
        $this->assertEquals(HttpStatus::OK, $father->getResponse()->getStatusCode());
        $child = $this->createPerson();
        $this->assertEquals(HttpStatus::CREATED, $child->getResponse()->getStatusCode());
        $chapr = new ChildAndParentsRelationship();
        $this->assertNotEmpty($father->getResourceReference());
        $chapr->setFather($father->getResourceReference());
        $this->assertnotnull($child->getResourceReference());
        $chapr->setChild($child->getResourceReference());
        /** @var ChildAndParentsRelationshipState $relation */
        $relation = $this->collectionState()->addChildAndParentsRelationship($chapr);
        $this->queueForDelete($relation);
        $this->assertEquals(HttpStatus::CREATED, $relation->getResponse()->getStatusCode());
        $relation = $relation->get();
        $this->assertEquals(HttpStatus::OK, $relation->getResponse()->getStatusCode());

        /** @var SourceDescriptionState $sds */
        $sds = $this->collectionState()->addSourceDescription(SourceBuilder::hitchhiker());
        $this->queueForDelete($sds);
        $this->assertEquals(HttpStatus::CREATED, $sds->getResponse()->getStatusCode());
        $sds = $sds->get();
        $this->assertEquals(HttpStatus::OK, $sds->getResponse()->getStatusCode());
        $relation->addSourceReferenceState($sds);
        $this->assertNotNull($father->getLink(Rel::CHILD_RELATIONSHIPS));
        $relationships = $father->loadChildRelationships()->getChildAndParentsRelationships();
        $this->assertNotEmpty($relationships);
        $relationship = array_shift($relationships);
        $link1 = $relationship->getLink(Rel::RELATIONSHIP);
        $link2 = $relationship->getLink(Rel::SELF);
        $this->assertTrue($link1 != null || $link2 != null);
        $relation = $father->readChildAndParentsRelationship($relationship);
        $linkObj = $relation->getLink(Rel::SOURCE_DESCRIPTIONS);
        $this->assertNotNull($linkObj);
        $link = $linkObj->getHref();
        $this->assertNotEmpty($link);
        $request = $client->createRequest(Request::GET, $link);
        $request->setHeader('Accept', FamilySearchPlatform::JSON_MEDIA_TYPE);
        $request->setHeader('Authorization', "Bearer {$token}");
        $response = $client->send($request);
        $state = new FamilySearchSourceDescriptionState($client, $request, $response, $token, $factory);

        $this->assertNotNull($state->ifSuccessful());
        $this->assertEquals(HttpStatus::OK, $state->getResponse()->getStatusCode());
        $this->assertNotNull($state->getSourceDescription());
    }

    /**
     * @vcr SourcesTests/testReadCoupleRelationshipSourceReferences.json
     * @link https://familysearch.org/developers/docs/api/tree/Read_Couple_Relationship_Sources_usecase
     * @link https://familysearch.org/developers/docs/api/tree/Read_Couple_Relationship_Source_References_usecase
     */
    public function testReadCoupleRelationshipSourceReferences()
    {
        $factory = new FamilyTreeStateFactory();
        $this->collectionState($factory);

        $person1 = $this->createPerson('male')->get();
        $person2 = $this->createPerson('female')->get();

        /* Create Relationship */
        /** @var $relation RelationshipState */
        $relation = $this->collectionState()->addSpouseRelationship($person1, $person2)->get();
        $this->queueForDelete($relation);
        $this->assertAttributeEquals(HttpStatus::OK, "statusCode", $relation->getResponse(), $this->buildFailMessage(__METHOD__."(addSpouse)", $relation));

        /* Create source */
        $sourceState = $this->createSource();
        $this->assertAttributeEquals(HttpStatus::CREATED, "statusCode", $sourceState->getResponse(), $this->buildFailMessage(__METHOD__."(createSource)", $sourceState));

        $reference = new SourceReference();
        $reference->setDescriptionRef($sourceState->getSelfUri());
        $reference->setAttribution( new Attribution( array(
                                                         "changeMessage" => $this->faker->sentence(6)
                                                     )));

        /* CREATE the source reference on the relationship */
        $updated = $relation->addSourceReference($reference);
        $this->assertAttributeEquals(HttpStatus::CREATED, "statusCode", $updated->getResponse(), $this->buildFailMessage(__METHOD__."(addReference)", $updated));

        /* READ the source references back */
        $relation->loadSourceReferences();
        $this->assertNotEmpty($relation->getRelationship()->getSources(), "loadForRead");
    }

    /**
     * @vcr SourcesTests/testReadCoupleRelationshipSources.json
     * @link https://familysearch.org/developers/docs/api/tree/Read_Couple_Relationship_Sources_usecase
     */
    public function testReadCoupleRelationshipSources()
    {
        $factory = new FamilyTreeStateFactory();
        $this->collectionState($factory);
        $this->assertTrue($this->collectionState()->isAuthenticated());
        $this->assertNotNull($this->collectionState()->getClient());
        $client = $this->collectionState()->getClient();
        $this->assertNotNull($this->collectionState()->getAccessToken());
        $token = $this->collectionState()->getAccessToken();
        /** @var FamilyTreePersonState $husband */
        $husband = $this->createPerson('male');
        $this->assertEquals(HttpStatus::CREATED, $husband->getResponse()->getStatusCode());
        $husband = $husband->get();
        $this->assertEquals(HttpStatus::OK, $husband->getResponse()->getStatusCode());
        $wife = $this->createPerson('female');
        $this->assertEquals(HttpStatus::CREATED, $wife->getResponse()->getStatusCode());
        /** @var RelationshipState $relation */
        $relation = $husband->addSpouse($wife);
        $this->queueForDelete($relation);
        $this->assertEquals(HttpStatus::CREATED, $relation->getResponse()->getStatusCode());

        $sds = $this->collectionState()->addSourceDescription(SourceBuilder::hitchhiker());
        $this->queueForDelete($sds);
        $this->assertEquals(HttpStatus::CREATED, $sds->getResponse()->getStatusCode());

        $relation->addSourceDescriptionState($sds);
        $relationships = $husband->loadSpouseRelationships();
        $this->assertEquals(HttpStatus::OK, $relationships->getResponse()->getStatusCode());
        $relations = $relationships->getRelationships();
        $this->assertNotEmpty($relations);
        $relationship = array_shift($relations);
        $relation = $husband->readRelationship($relationship);
        $this->assertEquals(HttpStatus::OK, $relation->getResponse()->getStatusCode());
        $linkObj = $relation->getLink(Rel::SOURCE_DESCRIPTIONS);
        $this->assertNotNull($linkObj);
        $link = $linkObj->getHref();
        $this->assertNotEmpty($link);
        $request = $client->createRequest(Request::GET, $link);
        $request->setHeader('Accept', Gedcomx::JSON_MEDIA_TYPE);
        $request->setHeader('Authorization', "Bearer {$token}");
        $response = $client->send($request);
        $state = new FamilySearchSourceDescriptionState($client, $request, $response, $token, $factory);

        $this->assertNotNull($state->ifSuccessful());
        $this->assertEquals(HttpStatus::OK, $state->getResponse()->getStatusCode());
        $this->assertNotNull($state->getSourceDescription());
        $this->assertNotEmpty($state->getEntity()->getRelationships());
    }

    /**
     * @vcr SourcesTests/testUpdatePersonSourceReference.json
     * @link https://familysearch.org/developers/docs/api/tree/Update_Person_Source_Reference_usecase
     */
    public function testUpdatePersonSourceReference()
    {
        $factory = new StateFactory();
        $this->collectionState($factory);

        /** @var PersonState $personState */
        $personState = $this->createPerson();
        $this->assertEquals(
            HttpStatus::CREATED,
            $personState->getResponse()->getStatusCode(),
            $this->buildFailMessage(__METHOD__.':'.__LINE__, $personState)
        );
        $personState = $personState->get();
        $this->assertEquals(
            HttpStatus::OK,
            $personState->getResponse()->getStatusCode(),
            $this->buildFailMessage(__METHOD__.':'.__LINE__, $personState)
        );

        $sourceState = $this->createSource();
        $this->assertEquals(
            HttpStatus::CREATED,
            $sourceState->getResponse()->getStatusCode(),
            $this->buildFailMessage(__METHOD__.':'.__LINE__, $sourceState)
        );

        $reference = new SourceReference();
        $reference->setDescriptionRef($sourceState->getSelfUri());
        $reference->setAttribution( new Attribution( array(
                                                         "changeMessage" => $this->faker->sentence(6)
                                                     )));

        $newState = $personState->addSourceReferenceObj($reference);
        $this->assertEquals(
            HttpStatus::CREATED,
            $newState->getResponse()->getStatusCode(),
            $this->buildFailMessage(__METHOD__.':'.__LINE__, $newState)
        );

        $personState->loadSourceReferences();

        $this->assertNotNull($personState->getEntity());
        $persons = $personState->getEntity()->getPersons();
        $newerState = $personState->updateSourceReferences($persons[0]);
        $this->assertEquals(
            HttpStatus::NO_CONTENT,
            $newerState->getResponse()->getStatusCode(),
            $this->buildFailMessage(__METHOD__.':'.__LINE__, $newerState)
        );
    }

    /**
     * @vcr SourcesTests/testUpdateSourceDescription.json
     * @link https://familysearch.org/developers/docs/api/sources/Update_Source_Description_usecase
     * @throws \Gedcomx\Rs\Client\Exception\GedcomxApplicationException
     */
    public function testUpdateSourceDescription()
    {
        $this->collectionState(new FamilyTreeStateFactory());
        $sd = SourceBuilder::newSource();
        /** @var SourceDescriptionState $description */
        $description = $this->collectionState()->addSourceDescription($sd);
        $this->assertEquals(HttpStatus::CREATED, $description->getResponse()->getStatusCode());
        $this->queueForDelete($description);

        $description = $description->get();
        $this->assertEquals(HttpStatus::OK, $description->getResponse()->getStatusCode());

        $state = $description->update($description->getSourceDescription());
        $this->assertNotNull($state->ifSuccessful());
        $this->assertEquals(HttpStatus::NO_CONTENT, $state->getResponse()->getStatusCode());
    }

    /**
     * @vcr SourcesTests/testDeletePersonSourceReference.json
     * @link https://familysearch.org/developers/docs/api/tree/Delete_Person_Source_Reference_usecase
     */
    public function testDeletePersonSourceReference()
    {
        $factory = new StateFactory();
        $this->collectionState($factory);

        /** @var PersonState $personState */
        $personState = $this->createPerson();
        $this->assertEquals(
            HttpStatus::CREATED,
            $personState->getResponse()->getStatusCode(),
            $this->buildFailMessage(__METHOD__.':'.__LINE__, $personState)
        );
        $personState = $personState->get();
        $this->assertEquals(
            HttpStatus::OK,
            $personState->getResponse()->getStatusCode(),
            $this->buildFailMessage(__METHOD__.':'.__LINE__, $personState)
        );

        $sourceState = $this->createSource();
        $this->assertEquals(
            HttpStatus::CREATED,
            $sourceState->getResponse()->getStatusCode(),
            $this->buildFailMessage(__METHOD__.':'.__LINE__, $personState)
        );

        $reference = new SourceReference();
        $reference->setDescriptionRef($sourceState->getSelfUri());

        $added = $personState->addSourceReferenceObj($reference);
        $this->assertEquals(
            HttpStatus::CREATED,
            $added->getResponse()->getStatusCode(),
            $this->buildFailMessage(__METHOD__.':'.__LINE__, $added)
        );
        $personState->loadSourceReferences();

        /** @var \Gedcomx\Conclusion\Person[] $persons */
        $persons = $personState->getEntity()->getPersons();
        $references = $persons[0]->getSources();
        $newerState = $personState->deleteSourceReference($references[0]);
        $this->assertEquals(
            HttpStatus::NO_CONTENT,
            $newerState->getResponse()->getStatusCode(),
            $this->buildFailMessage(__METHOD__.':'.__LINE__, $newerState)
        );
    }

    /**
     * @vcr SourcesTests/testDeleteSourceDescription.json
     * @link https://familysearch.org/developers/docs/api/sources/Delete_Source_Description_usecase
     */
    public function testDeleteSourceDescription()
    {
        $this->collectionState(new FamilyTreeStateFactory());
        $sd = SourceBuilder::newSource();

        /** @var SourceDescriptionState $description */
        $description = $this->collectionState()->addSourceDescription($sd);
        $this->assertEquals(HttpStatus::CREATED, $description->getResponse()->getStatusCode());
        $this->queueForDelete($description);

        $description = $description->get();
        $this->assertEquals(HttpStatus::OK, $description->getResponse()->getStatusCode());

        $state = $description->delete();
        $this->assertNotNull($state->ifSuccessful());
        $this->assertEquals(HttpStatus::NO_CONTENT, $state->getResponse()->getStatusCode());
    }

    /**
     * @vcr SourcesTests/testDeleteChildAndParentsRelationshipSourceReference.json
     * @link https://familysearch.org/developers/docs/api/tree/Delete_Child-and-Parents_Relationship_Source_Reference_usecase
     */
    public function testDeleteChildAndParentsRelationshipSourceReference()
    {
        $factory = new FamilyTreeStateFactory();
        /** @var FamilyTreeCollectionState $collection */
        $this->collectionState($factory);

        /** @var ChildAndParentsRelationshipState $relation */
        $relation = $this->createRelationship();
        $relation = $relation->get();
        $this->assertEquals(
            HttpStatus::OK,
            $relation->getResponse()->getStatusCode(),
            $this->buildFailMessage(__METHOD__.':'.__LINE__, $relation)
        );
        $sourceState = $this->createSource();
        $this->assertEquals(
            HttpStatus::CREATED,
            $sourceState->getResponse()->getStatusCode(),
            $this->buildFailMessage(__METHOD__.':'.__LINE__, $sourceState)
        );
        $this->queueForDelete($sourceState);

        $reference = new SourceReference();
        $reference->setDescriptionRef($sourceState->getSelfUri());
        $reference->setAttribution( new Attribution( array(
                                                         "changeMessage" => $this->faker->sentence(6)
                                                     )));
        $newState = $relation->addSourceReference($reference);
        $this->assertEquals(
            HttpStatus::CREATED,
            $newState->getResponse()->getStatusCode(),
            $this->buildFailMessage(__METHOD__.':'.__LINE__, $newState)
        );

        $relation->loadSourceReferences();
        $this->assertNotEmpty($relation->getRelationship()->getSources());

        $sources = $relation->getRelationship()->getSources();
        $deleted = $relation->deleteSourceReference($sources[0]);
        $this->assertEquals(
            HttpStatus::NO_CONTENT,
            $deleted->getResponse()->getStatusCode(),
            $this->buildFailMessage(__METHOD__.':'.__LINE__, $deleted)
        );

        $relation = $relation->get();
        $relation->loadSourceReferences($this->createCacheBreakerQueryParam());
        $this->assertEmpty($relation->getRelationship()->getSources());
    }

    /**
     * @vcr SourcesTests/testDeleteCoupleRelationshipSourceReference.json
     * @link https://familysearch.org/developers/docs/api/tree/Delete_Couple_Relationship_Source_Reference_usecase
     */
    public function testDeleteCoupleRelationshipSourceReference()
    {
        $factory = new FamilyTreeStateFactory();
        $this->collectionState($factory);

        /** @var FamilyTreePersonState $husband */
        $husband = $this->createPerson('male');
        $this->assertEquals(
            HttpStatus::CREATED,
            $husband->getResponse()->getStatusCode(),
            $this->buildFailMessage(__METHOD__.':'.__LINE__, $husband)
        );
        $husband = $husband->get();
        $this->assertEquals(
            HttpStatus::OK,
            $husband->getResponse()->getStatusCode(),
            $this->buildFailMessage(__METHOD__.':'.__LINE__, $husband)
        );

        /** @var FamilyTreePersonState $wife */
        $wife = $this->createPerson('female');
        $this->assertEquals(
            HttpStatus::CREATED,
            $wife->getResponse()->getStatusCode(),
            $this->buildFailMessage(__METHOD__.':'.__LINE__, $wife)
        );

        /** @var FamilyTreeRelationshipState $relationship */
        $relationship = $husband->addSpouse($wife);
        $this->assertEquals(
            HttpStatus::CREATED,
            $relationship->getResponse()->getStatusCode(),
            $this->buildFailMessage(__METHOD__.':'.__LINE__, $relationship)
        );
        $this->queueForDelete($relationship);
        $relationship = $relationship->get();
        $this->assertEquals(
            HttpStatus::OK,
            $relationship->getResponse()->getStatusCode(),
            $this->buildFailMessage(__METHOD__.':'.__LINE__, $relationship)
        );

        $sourceState = $this->createSource();
        $reference = new SourceReference();
        $reference->setDescriptionRef($sourceState->getSelfUri());
        $reference->setAttribution( new Attribution( array(
            "changeMessage" => $this->faker->sentence(6)
        )));
        $sourceRef = $relationship->addSourceReference($reference);
        $this->assertEquals(
            HttpStatus::CREATED,
            $sourceRef->getResponse()->getStatusCode(),
            $this->buildFailMessage(__METHOD__.':'.__LINE__, $sourceRef)
        );

        $relationship->loadSourceReferences();
        $this->assertNotEmpty($relationship->getRelationship()->getSources());

        $state = $relationship->deleteSourceReference($relationship->getSourceReference());
        $this->AssertNotNull($state->ifSuccessful());
        $this->assertEquals(HttpStatus::NO_CONTENT, $state->getResponse()->getStatusCode());

        $relationship = $relationship->get();
        $relationship->loadSourceReferences($this->createCacheBreakerQueryParam());
        $this->assertEmpty($relationship->getRelationship()->getSources());
    }


}