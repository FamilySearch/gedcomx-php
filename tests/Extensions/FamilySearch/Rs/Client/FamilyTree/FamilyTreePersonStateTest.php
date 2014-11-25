<?php

namespace Gedcomx\tests\Extensions\FamilySearch\Rs\Client\FamilyTree;

use Gedcomx\Extensions\FamilySearch\FamilySearchPlatform;
use Gedcomx\Extensions\FamilySearch\Feature;
use Gedcomx\Extensions\FamilySearch\Rs\Client\FamilySearchStateFactory;
use Gedcomx\Extensions\FamilySearch\Rs\Client\FamilyTree\FamilyTreeStateFactory;
use Gedcomx\Extensions\FamilySearch\Rs\Client\Rel;
use Gedcomx\Extensions\FamilySearch\Rs\Client\Util\ExperimentsFilter;
use Gedcomx\Rs\Client\GedcomxApplicationState;
use Gedcomx\Rs\Client\Util\HttpStatus;
use Gedcomx\Tests\ApiTestCase;
use Gedcomx\Tests\PersonBuilder;
use Gedcomx\Rs\Client\Options\QueryParameter;
use Gedcomx\Rs\Client\Util\GedcomxPersonSearchQueryBuilder;
use Guzzle\Http\Message\Header\HeaderInterface;

class FamilyTreePersonStateTest extends ApiTestCase
{
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

    public function testReadPersonWithMultiplePendingModificationsActivated()
    {
        $factory = new FamilyTreeStateFactory();
        $this->collectionState($factory);
        /** @var Feature[] $features */
        $features = array();

        $request = $this->collectionState()->getClient()->createRequest("GET", "https://sandbox.familysearch.org/platform/pending-modifications");
        $request->addHeader("Accept", GedcomxApplicationState::JSON_APPLICATION_TYPE);
        // Get all the features that are pending
        $response = $request->send($request);

        // Get each pending feature
        $json = json_decode($response->getBody(true), true);
        $fsp = new FamilySearchPlatform($json);
        foreach ($fsp->getFeatures() as $feature) {
            $features[] = $feature;
        }

        // Add every pending feature to the tree's current client
        $this->collectionState()->getClient()->addFilter(new ExperimentsFilter(array_map(function (Feature $feature) {
            return $feature->getName();
        }, $features)));

        $state = $this->createPerson();

        // Ensure a response came back
        $this->assertNotNull($state);
        $check = array();
        /** @var HeaderInterface $header */
        foreach ($state->getRequest()->getHeaders() as $header) {
            if ($header->getName() == "X-FS-Feature-Tag") {
                $check[] = $header;
            }
        }

        /** @var string[] $requestedFeatures */
        $requestedFeatures = join(",", $check);
        // Ensure each requested feature was found in the request headers
        foreach ($features as $feature) {
            $this->assertTrue(strpos($requestedFeatures, $feature->getName()) !== false, $feature->getName() . " was not found in the requested features.");
        }

        $state->delete();
    }

    public function testReadPersonWithPendingModificationActivated()
    {
        // The default client from this factory is assumed to add a single pending feature (if it doesn't, this test will fail)
        $factory = new FamilySearchStateFactory();
        $state = $this->collectionState($factory);

        $this->assertNotNull($state);
        $check = array();
        /** @var HeaderInterface $header */
        foreach ($state->getRequest()->getHeaders() as $header) {
            if ($header->getName() == "X-FS-Feature-Tag") {
                $check[] = $header;
            }
        }

        /** @var string[] $requestedFeatures */
        $requestedFeatures = join(",", $check);
        $this->assertNotNull($requestedFeatures);
        $this->assertEquals(false, strpos($requestedFeatures, ","));
        $this->assertEquals(1, count($requestedFeatures));
    }

    public function testRedirectToPerson()
    {
        $factory = new FamilyTreeStateFactory();
        $this->collectionState($factory);

        $person = $this->createPerson();
        $id = $person->getResponse()->getHeader("X-ENTITY-ID")->__toString();
        $uri = "https://sandbox.familysearch.org/platform/redirect?person=" . $id;
        $request = $this->collectionState()->getClient()->createRequest("GET", $uri);
        $request->addHeader("Header", "application/json");
        $response = $this->collectionState()->getClient()->send($request);

        $this->assertNotNull($response);
        $this->assertEquals(1, $response->getRedirectCount());
        $this->assertNotEquals($uri, $response->getEffectiveUrl());

        $person->delete();
    }

    public function testRedirectToPersonMemories()
    {
        $factory = new FamilyTreeStateFactory();
        $this->collectionState($factory);

        $person = $this->createPerson();
        $id = $person->getResponse()->getHeader("X-ENTITY-ID")->__toString();
        $uri = "https://sandbox.familysearch.org/platform/redirect?context=memories&person=" . $id;
        $request = $this->collectionState()->getClient()->createRequest("GET", $uri);
        $request->addHeader("Header", "application/json");
        $response = $this->collectionState()->getClient()->send($request);

        $this->assertNotNull($response);
        $this->assertEquals(1, $response->getRedirectCount());
        $this->assertNotEquals($uri, $response->getEffectiveUrl());

        $person->delete();
    }

    public function testRedirectToSourceLinker()
    {
        $factory = new FamilyTreeStateFactory();
        $this->collectionState($factory);

        $person = $this->createPerson()->get();

        $identifiers = $person->getPerson()->getIdentifiers();
        $uri = sprintf("https://sandbox.familysearch.org/platform/redirect?context=sourcelinker&person=%s&hintId=%s", $person->getPerson()->getId(), array_shift($identifiers)->getValue());
        $request = $this->collectionState()->getClient()->createRequest("GET", $uri);
        $request->addHeader("Header", "application/json");
        $response = $this->collectionState()->getClient()->send($request);

        $this->assertNotNull($response);
        $this->assertEquals(1, $response->getRedirectCount());
        $this->assertNotEquals($uri, $response->getEffectiveUrl());

        $person->delete();
    }

    public function testRedirectToUri()
    {
        $factory = new FamilyTreeStateFactory();
        $this->collectionState($factory);

        $uri = "https://sandbox.familysearch.org/platform/redirect?uri=https://familysearch.org/some/path?p1%3Dp1-value%26p2%3Dp2-value";
        $request = $this->collectionState()->getClient()->createRequest("GET", $uri);
        $request->addHeader("Header", "application/json");
        $response = $this->collectionState()->getClient()->send($request);

        $this->assertNotNull($response);
        $this->assertEquals(1, $response->getRedirectCount());
        $this->assertNotEquals($uri, $response->getEffectiveUrl());
    }
}