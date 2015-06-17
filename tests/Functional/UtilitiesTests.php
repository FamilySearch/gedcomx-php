<?php

namespace Gedcomx\Tests\Functional;

use Gedcomx\Extensions\FamilySearch\FamilySearchPlatform;
use Gedcomx\Extensions\FamilySearch\Feature;
use Gedcomx\Extensions\FamilySearch\Rs\Client\FamilySearchStateFactory;
use Gedcomx\Extensions\FamilySearch\Rs\Client\FamilyTree\FamilyTreeStateFactory;
use Gedcomx\Extensions\FamilySearch\Rs\Client\Util\ExperimentsFilter;
use Gedcomx\Gedcomx;
use Gedcomx\Rs\Client\GedcomxApplicationState;
use Gedcomx\Tests\ApiTestCase;
use Guzzle\Http\Message\Header\HeaderInterface;

class UtilitiesTests extends ApiTestCase
{
    /**
     * @link https://familysearch.org/developers/docs/api/tree/Read_Person_With_Multiple_Pending_Modifications_Activated_usecase
     */
    public function testReadPersonWithMultiplePendingModificationsActivated()
    {
        $factory = new FamilyTreeStateFactory();
        $this->collectionState($factory);
        /** @var Feature[] $features */
        $features = array();

        $request = $this->collectionState()->getClient()->createRequest("GET", "https://sandbox.familysearch.org/platform/pending-modifications");
        $request->addHeader("Accept", Gedcomx::JSON_APPLICATION_TYPE);
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

        /** @var string $requestedFeatures */
        $requestedFeatures = join(",", $check);
        // Ensure each requested feature was found in the request headers
        foreach ($features as $feature) {
            $this->assertTrue(strpos($requestedFeatures, $feature->getName()) !== false, $feature->getName() . " was not found in the requested features.");
        }
    }

    /**
     * testReadPersonWithPendingModificationActivated
     * @link https://familysearch.org/developers/docs/api/tree/Read_Person_With_Pending_Modification_Activated_usecase
     * @see testReadPersonWithMultiplePendingModificationsActivated
     */

    /**
     * @link https://familysearch.org/developers/docs/api/tree/Redirect_to_Person_usecase
     */
    public function testRedirectToPerson()
    {
        $factory = new FamilyTreeStateFactory();
        $this->collectionState($factory);

        $person = $this->createPerson();
        $id = (string)$person->getResponse()->getHeader("X-ENTITY-ID");
        $uri = "https://sandbox.familysearch.org/platform/redirect?person=" . $id;
        $request = $this->collectionState()->getClient()->createRequest("GET", $uri);
        $request->addHeader("Header", "application/json");
        $response = $this->collectionState()->getClient()->send($request);

        $this->assertNotNull($response, "Response is null.");
        $this->assertEquals(1, $response->getRedirectCount(), "No apparent redirect.");
        $this->assertNotEquals($uri, $response->getEffectiveUrl(), "Effective URL should not match original.");
    }

    /**
     * @link https://familysearch.org/developers/docs/api/tree/Redirect_to_Person_memories_usecase
     */
    public function testRedirectToPersonMemories()
    {
        $factory = new FamilyTreeStateFactory();
        $this->collectionState($factory);

        $person = $this->createPerson();
        $id = (string)$person->getResponse()->getHeader("X-ENTITY-ID");
        $uri = "https://sandbox.familysearch.org/platform/redirect?context=memories&person=" . $id;
        $request = $this->collectionState()->getClient()->createRequest("GET", $uri);
        $request->addHeader("Header", "application/json");
        $response = $this->collectionState()->getClient()->send($request);

        $this->assertNotNull($response, "Response is null.");
        $this->assertEquals(1, $response->getRedirectCount(), "No apparent redirect.");
        $this->assertNotEquals($uri, $response->getEffectiveUrl(), "Effective URL should not match original.");
    }

    /**
     * @link https://familysearch.org/developers/docs/api/tree/Redirect_to_Source_Linker_usecase
     */
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

        $this->assertNotNull($response, "Response is empty.");
        $this->assertEquals(1, $response->getRedirectCount(), "No apparent redirect.");
        $this->assertNotEquals($uri, $response->getEffectiveUrl(), "Effective URL should not match original request.");
    }

    /**
     * @link https://familysearch.org/developers/docs/api/tree/Redirect_to_Uri_usecase
     */
    public function testRedirectToUri()
    {
        $factory = new FamilyTreeStateFactory();
        $this->collectionState($factory);

        $uri = "https://sandbox.familysearch.org/platform/redirect?uri=https://familysearch.org/some/path?p1%3Dp1-value%26p2%3Dp2-value";
        $request = $this->collectionState()->getClient()->createRequest("GET", $uri);
        $request->addHeader("Header", "application/json");
        $response = $this->collectionState()->getClient()->send($request);

        $this->assertNotNull($response, "Response is empty.");
        $this->assertEquals(1, $response->getRedirectCount(), "No apparent redirect.");
        $this->assertNotEquals($uri, $response->getEffectiveUrl(), "Effective URLs should not match");
    }
}