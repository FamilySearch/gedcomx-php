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
use Gedcomx\Extensions\FamilySearch\Rs\Client\FamilyTree\FamilyTreeStateFactory;
use Gedcomx\Extensions\FamilySearch\Rs\Client\Rel;
use Gedcomx\Rs\Client\RelationshipState;
use Gedcomx\Rs\Client\SourceDescriptionState;
use Gedcomx\Rs\Client\StateFactory;
use Gedcomx\Rs\Client\Util\DataSource;
use Gedcomx\Rs\Client\Util\HttpStatus;
use Gedcomx\Source\SourceCitation;
use Gedcomx\Source\SourceDescription;
use Gedcomx\Tests\ApiTestCase;
use Gedcomx\Tests\SourceBuilder;
use Gedcomx\Extensions\FamilySearch\Rs\Client\FamilySearchSourceDescriptionState;
use Gedcomx\Extensions\FamilySearch\Rs\Client\FamilyTree\FamilyTreePersonState;
use Gedcomx\Rs\Client\GedcomxApplicationState;
use Gedcomx\Source\SourceReference;
use Guzzle\Http\Message\Request;

class SourcesTests extends ApiTestCase
{
    /**
     * @link https://familysearch.org/developers/docs/api/tree/Create_Person_Source_Reference_usecase
     */
    public function testCreatePersonSourceReference()
    {
        $factory = new StateFactory();
        $this->collectionState($factory);

        $personState = $this->createPerson()->get();

        $sourceState = $this->createSource();
        $this->assertAttributeEquals(HttpStatus::CREATED, "statusCode", $sourceState->getResponse() );

        $reference = new SourceReference();
        $reference->setDescriptionRef($sourceState->getSelfUri());
        $reference->setAttribution( new Attribution( array(
                                                         "changeMessage" => $this->faker->sentence(6)
                                                     )));
        /** @var \Gedcomx\Rs\Client\PersonState $newState */
        $newState = $personState->addSourceReferenceObj($reference);
        $this->assertAttributeEquals(HttpStatus::CREATED, "statusCode", $newState->getResponse() );

        $sourceState->delete();
        $personState->delete();
    }

    /**
     * @link https://familysearch.org/developers/docs/api/sources/Create_Source_Description_usecase
     */
    public function testCreateSourceDescription()
    {
        $this->collectionState(new StateFactory());
        /** @var SourceDescription $source */
        $source = SourceBuilder::newSource();
        $link = $this->collectionState()->getLink(Rel::SOURCE_DESCRIPTIONS);
        if ($link === null || $link->getHref() === null) {
            return null;
        }

        $sourceState = $this->collectionState()->addSourceDescription($source);
        $this->assertAttributeEquals(HttpStatus::CREATED, "statusCode", $sourceState->getResponse(), $this->buildFailMessage(__METHOD__ . "(CREATE)", $sourceState));
    }

    /**
     * @link https://familysearch.org/developers/docs/api/tree/Create_Child-and-Parents_Relationship_Source_Reference_usecase
     */
    public function testCreateChildAndParentsRelationshipSourceReferences()
    {
        $factory = new FamilyTreeStateFactory();
        /** @var FamilyTreeCollectionState $collection */
        $this->collectionState($factory);

        /** @var ChildAndParentsRelationshipState $relation */
        $relation = $this->createRelationship();
        $sourceState = $this->createSource();
        $this->assertAttributeEquals(HttpStatus::CREATED, "statusCode", $sourceState->getResponse() );

        $reference = new SourceReference();
        $reference->setDescriptionRef($sourceState->getSelfUri());
        $reference->setAttribution( new Attribution( array(
                                                         "changeMessage" => $this->faker->sentence(6)
                                                     )));
        $newState = $relation->addSourceReference($reference);
        $this->assertAttributeEquals(HttpStatus::CREATED, "statusCode", $newState->getResponse(), $this->buildFailMessage(__METHOD__, $newState));

        $sourceState->delete();
    }

    /**
     * @link https://familysearch.org/developers/docs/api/tree/Create_Couple_Relationship_Source_Reference_usecase
     * @link https://familysearch.org/developers/docs/api/tree/Read_Couple_Relationship_Sources_usecase
     * @link https://familysearch.org/developers/docs/api/tree/Read_Couple_Relationship_Source_References_usecase
     */
    public function testCreateCoupleRelationshipSourceReference()
    {
        $factory = new FamilyTreeStateFactory();
        $this->collectionState($factory);

        $person1 = $this->createPerson('male')->get();
        $person2 = $this->createPerson('female')->get();

        /* Create Relationship */
        /** @var $relation RelationshipState */
        $relation = $this->collectionState()->addSpouseRelationship($person1, $person2)->get();
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
        $relation = $relation->addSourceReference($reference);
        $this->assertAttributeEquals(HttpStatus::CREATED, "statusCode", $relation->getResponse(), $this->buildFailMessage(__METHOD__."(addReference)", $relation));

        $sourceState->delete();
        $relation->delete();
        $person1->delete();
        $person2->delete();
    }

    /**
     * @link https://familysearch.org/developers/docs/api/sources/Create_User-Uploaded_Source_usecase
     */
    public function testCreateUserUploadedSource()
    {
        $this->collectionState(new FamilyTreeStateFactory());
        $person = $this->createPerson()->get();
        $ds = new DataSource();
        $ds->setTitle("Sample Memory");
        $ds->setFile($this->createTextFile());
        $person->addArtifact($ds);
        $artifact = $person->readArtifacts()->getSourceDescription();
        $memoryUri = $artifact->getLink("memory")->getHref();
        $source = SourceBuilder::newSource();
        $source->setAbout($memoryUri);
        $state = $this->collectionState()->addSourceDescription($source);

        $this->assertNotNull($state->ifSuccessful());
        $this->assertEquals(HttpStatus::CREATED, $state->getResponse()->getStatusCode());
    }

    /**
     * @link https://familysearch.org/developers/docs/api/tree/Read_Person_Source_References_usecase
     */
    public function testReadPersonSourceReferences(){
        $factory = new StateFactory();
        $this->collectionState($factory);

        $personState = $this->getPerson();
        $personState->loadSourceReferences();

        $this->assertNotEmpty($personState->getEntity()->getSourceDescriptions());
    }

    /**
     * @link https://familysearch.org/developers/docs/api/tree/Read_Person_Sources_usecase
     */
    public function testReadPersonSources()
    {
        $this->markTestIncomplete("Test not yet implemented"); //todo
    }

    /**
     * @link https://familysearch.org/developers/docs/api/sources/Read_Source_Description_usecase
     * @throws \Gedcomx\Rs\Client\Exception\GedcomxApplicationException
     */
    public function testReadSourceDescription()
    {
        $this->collectionState(new FamilyTreeStateFactory());
        $sd = $this->createSourceDescription();
        /** @var SourceDescriptionState $state */
        $state = $this->collectionState()->addSourceDescription($sd)->get();

        $this->assertNotNull($state->ifSuccessful());
        $this->assertEquals(HttpStatus::OK, $state->getResponse()->getStatusCode());
        $this->assertNotNull($state->getSourceDescription());
    }

    /**
     * @link https://familysearch.org/developers/docs/api/tree/Read_Source_References_usecase
     */
    public function testReadSourceReferences()
    {
        $factory = new FamilyTreeStateFactory();
        $this->collectionState($factory);
        $sd = SourceBuilder::hitchhiker();
        /** @var SourceDescriptionState $source */
        $source = $this->collectionState()->addSourceDescription($sd)->get();
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

        $source->delete();
        $person->delete();
    }

    /**
     * @link https://familysearch.org/developers/docs/api/tree/Read_Child-and-Parents_Relationship_Source_References_usecase
     * @see SourcesTests::testReadChildAndParentsRelationshipSourceReferences
     */
    public function testReadChildAndParentsRelationshipSourceReferences()
    {
        $factory = new FamilyTreeStateFactory();
        /** @var FamilyTreeCollectionState $collection */
        $this->collectionState($factory);

        /** @var ChildAndParentsRelationshipState $relation */
        $relation = $this->createRelationship();
        $sourceState = $this->createSource();

        $reference = new SourceReference();
        $reference->setDescriptionRef($sourceState->getSelfUri());
        $reference->setAttribution( new Attribution( array(
                                                         "changeMessage" => $this->faker->sentence(6)
                                                     )));
        $relation->addSourceReference($reference);

        $relation = $relation->get();
        $relation->loadSourceReferences();
        $this->assertNotEmpty($relation->getRelationship()->getSources());
    }

    /**
     * @link https://familysearch.org/developers/docs/api/tree/Read_Child-and-Parents_Relationship_Sources_usecase
     * @see SourcesTests::testReadChildAndParentsRelationshipSources
     */
    public function testReadChildAndParentsRelationshipSources()
    {
        $factory = new FamilyTreeStateFactory();
        $this->collectionState($factory);
        $client = $this->collectionState()->getClient();
        $token = $this->collectionState()->getAccessToken();
        /** @var FamilyTreePersonState $father */
        $father = $this->createPerson('male')->get();
        $child = $this->createPerson();
        $chapr = new ChildAndParentsRelationship();
        $chapr->setFather($father->getResourceReference());
        $chapr->setChild($child->getResourceReference());
        /** @var ChildAndParentsRelationshipState $relation */
        $relation = $this->collectionState()->addChildAndParentsRelationship($chapr)->get();
        /** @var SourceDescriptionState $sds */
        $sds = $this->collectionState()->addSourceDescription(SourceBuilder::hitchhiker())->get();
        $relation->addSourceReferenceState($sds);
        $relationships = $father->loadChildRelationships()->getChildAndParentsRelationships();
        $relationship = array_shift($relationships);
        $relation = $father->readChildAndParentsRelationship($relationship);
        $link = $relation->getLink("source-descriptions")->getHref();
        $request = $client->createRequest(Request::GET, $link);
        $request->setHeader('Accept', FamilySearchPlatform::JSON_MEDIA_TYPE);
        $request->setHeader('Authorization', "Bearer {$token}");
        $response = $client->send($request);
        $state = new FamilySearchSourceDescriptionState($client, $request, $response, $token, $factory);
        $father->delete();
        $child->delete();

        $this->assertNotNull($state->ifSuccessful());
        $this->assertEquals(HttpStatus::OK, $state->getResponse()->getStatusCode());
        $this->assertNotNull($state->getSourceDescription());
    }

    /**
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

        $sourceState->delete();
        $relation->delete();
        $person1->delete();
        $person2->delete();
    }

    /**
     * @link https://familysearch.org/developers/docs/api/tree/Read_Couple_Relationship_Sources_usecase
     */
    public function testReadCoupleRelationshipSources()
    {
        $factory = new FamilyTreeStateFactory();
        $this->collectionState($factory);
        $client = $this->collectionState()->getClient();
        $token = $this->collectionState()->getAccessToken();
        /** @var FamilyTreePersonState $husband */
        $husband = $this->createPerson('male')->get();
        $wife = $this->createPerson('female');
        /** @var RelationshipState $relation */
        $relation = $husband->addSpouse($wife);
        $sds = $this->collectionState()->addSourceDescription(SourceBuilder::hitchhiker());
        $relation->addSourceDescriptionState($sds);
        $relationships = $husband->loadSpouseRelationships();
        $relations = $relationships->getRelationships();
        $relationship = array_shift($relations);
        $relation = $husband->readRelationship($relationship);
        $link = $relation->getLink("source-descriptions")->getHref();
        $request = $client->createRequest(Request::GET, $link);
        $request->setHeader('Accept', GedcomxApplicationState::JSON_MEDIA_TYPE);
        $request->setHeader('Authorization', "Bearer {$token}");
        $response = $client->send($request);
        $state = new FamilySearchSourceDescriptionState($client, $request, $response, $token, $factory);
        $husband->delete();
        $wife->delete();

        $this->assertNotNull($state->ifSuccessful());
        $this->assertEquals(HttpStatus::OK, $state->getResponse()->getStatusCode());
        $this->assertNotNull($state->getSourceDescription());
    }

    /**
     * @link https://familysearch.org/developers/docs/api/tree/Update_Person_Source_Reference_usecase
     */
    public function testUpdatePersonSourceReference()
    {
        $factory = new StateFactory();
        $this->collectionState($factory);

        $personState = $this->createPerson()->get();

        $sourceState = $this->createSource();
        $this->assertAttributeEquals(HttpStatus::CREATED, "statusCode", $sourceState->getResponse());

        $reference = new SourceReference();
        $reference->setDescriptionRef($sourceState->getSelfUri());
        $reference->setAttribution( new Attribution( array(
                                                         "changeMessage" => $this->faker->sentence(6)
                                                     )));

        $personState->addSourceReferenceObj($reference);
        $newState = $personState->loadSourceReferences();
        $persons = $newState->getEntity()->getPersons();
        $newerState = $newState->updateSourceReferences($persons[0]);
        $this->assertAttributeEquals(HttpStatus::NO_CONTENT, "statusCode", $newerState->getResponse());
    }

    /**
     * @link https://familysearch.org/developers/docs/api/sources/Update_Source_Description_usecase
     * @throws \Gedcomx\Rs\Client\Exception\GedcomxApplicationException
     */
    public function testUpdateSourceDescription()
    {
        $this->collectionState(new FamilyTreeStateFactory());
        $sd = $this->createSourceDescription();
        /** @var SourceDescriptionState $description */
        $description = $this->collectionState()->addSourceDescription($sd)->get();
        $state = $description->update($description->getSourceDescription());

        $this->assertNotNull($state->ifSuccessful());
        $this->assertEquals(HttpStatus::NO_CONTENT, $state->getResponse()->getStatusCode());
    }

    /**
     * @link https://familysearch.org/developers/docs/api/tree/Delete_Person_Source_Reference_usecase
     */
    public function testDeletePersonSourceReference()
    {
        $factory = new StateFactory();
        $this->collectionState($factory);

        $personState = $this->createPerson()->get();

        $sourceState = $this->createSource();
        $this->assertAttributeEquals(HttpStatus::CREATED, "statusCode", $sourceState->getResponse() );

        $reference = new SourceReference();
        $reference->setDescriptionRef($sourceState->getSelfUri());

        $personState->addSourceReferenceObj($reference);
        $newState = $personState->loadSourceReferences();

        /** @var \Gedcomx\Conclusion\Person[] $persons */
        $persons = $newState->getEntity()->getPersons();
        $references = $persons[0]->getSources();
        $newerState = $newState->deleteSourceReference($references[0]);
        $this->assertAttributeEquals(HttpStatus::NO_CONTENT, "statusCode", $newerState->getResponse());
    }

    /*
     * @link https://familysearch.org/developers/docs/api/sources/Delete_Source_Description_usecase
     */
    public function testDeleteSourceDescription()
    {
        $this->collectionState(new FamilyTreeStateFactory());
        $sd = $this->createSourceDescription();
        /** @var SourceDescriptionState $description */
        $description = $this->collectionState()->addSourceDescription($sd)->get();
        $state = $description->delete();

        $this->assertNotNull($state->ifSuccessful());
        $this->assertEquals(HttpStatus::NO_CONTENT, $state->getResponse()->getStatusCode());
    }

    /**
     * @link https://familysearch.org/developers/docs/api/tree/Delete_Child-and-Parents_Relationship_Source_Reference_usecase
     */
    public function testDeleteChildAndParentsRelationshipSourceReference()
    {
        $factory = new FamilyTreeStateFactory();
        /** @var FamilyTreeCollectionState $collection */
        $this->collectionState($factory);

        /** @var ChildAndParentsRelationshipState $relation */
        $relation = $this->createRelationship();
        $sourceState = $this->createSource();

        $reference = new SourceReference();
        $reference->setDescriptionRef($sourceState->getSelfUri());
        $reference->setAttribution( new Attribution( array(
                                                         "changeMessage" => $this->faker->sentence(6)
                                                     )));
        $relation->addSourceReference($reference);

        $relation = $relation->get();
        $relation->loadSourceReferences();
        $sources = $relation->getRelationship()->getSources();
        $deleted = $relation->deleteSourceReference($sources[0]);
        $this->assertAttributeEquals(HttpStatus::NO_CONTENT, "statusCode", $deleted->getResponse(), $this->buildFailMessage(__METHOD__."(updateFact)", $deleted));
    }

    /**
     * @link https://familysearch.org/developers/docs/api/tree/Delete_Couple_Relationship_Source_Reference_usecase
     */
    public function testDeleteCoupleRelationshipSourceReference()
    {
        $this->markTestIncomplete('Not yet implemented');
    }

    /**
     * @return \Gedcomx\Source\SourceDescription
     */
    private function createSourceDescription()
    {
        $sd = new SourceDescription();
        $citation = new SourceCitation();
        $citation->setValue("\"United States Census, 1900.\" database and digital images, FamilySearch (https://familysearch.org/: accessed 17 Mar 2012), Ethel Hollivet, 1900; citing United States Census Office, Washington, D.C., 1900 Population Census Schedules, Los Angeles, California, population schedule, Los Angeles Ward 6, Enumeration District 58, p. 20B, dwelling 470, family 501, FHL microfilm 1,240,090; citing NARA microfilm publication T623, roll 90.");
        $sd->setCitations(array($citation));
        $title = new TextValue();
        $title->setValue("1900 US Census, Ethel Hollivet");
        $sd->setTitles(array($title));
        $note = new Note();
        $note->setText("Ethel Hollivet (line 75) with husband Albert Hollivet (line 74); also in the dwelling: step-father Joseph E Watkins (line 72), mother Lina Watkins (line 73), and grandmother -- Lina's mother -- Mary Sasnett (line 76).  Albert's mother and brother also appear on this page -- Emma Hollivet (line 68), and Eddie (line 69).");
        $sd->setNotes(array($note));
        $attribution = new Attribution();
        $rr = new ResourceReference();
        $rr->setResource("https://familysearch.org/platform/users/agents/MM6M-8QJ");
        $rr->setResourceId("MM6M-8QJ");
        $attribution->setContributor($rr);
        $attribution->setModified(time());
        $attribution->setChangeMessage("This is the change message");
        $sd->setAttribution($attribution);

        return $sd;
    }


}