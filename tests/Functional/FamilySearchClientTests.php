<?php

namespace Gedcomx\Tests\Functional;

use Gedcomx\Rs\Client\Util\HttpStatus;
use Gedcomx\Tests\ApiTestCase;
use Gedcomx\Tests\PersonBuilder;
use Gedcomx\Extensions\FamilySearch\Feature;

class FamilySearchClientTests extends ApiTestCase
{
    
    public function testAuthenticate()
    {
        $client = $this->createAuthenticatedFamilySearchClient();
        $this->assertNotNull($client->getAccessToken());
    }
    
    public function testCreatePerson()
    {
        $client = $this->createAuthenticatedFamilySearchClient();
        
        $person = PersonBuilder::buildPerson('');
        $responseState = $client->familytree()->addPerson($person);
        $this->queueForDelete($responseState);
        
        $this->assertInstanceOf('Gedcomx\Rs\Client\GedcomxApplicationState', $responseState);
        $this->assertEquals(
            HttpStatus::CREATED,
            $responseState->getResponse()->getStatusCode(),
            $this->buildFailMessage(__METHOD__, $responseState)
        );
    }
    
    public function testGetOAuth2AuthorizationURI()
    {
        $client = $this->createFamilySearchClient();
        $url = $client->getOAuth2AuthorizationURI();
        $this->assertEquals('https://integration.familysearch.org/cis-web/oauth2/v3/authorization?response_type=code&redirect_uri=http%3A%2F%2Fexample.com%2Fredirect&client_id=WCQY-7J1Q-GKVV-7DNM-SQ5M-9Q5H-JX3H-CMJK', $url);
    }
    
    public function testGetAvailablePendingModifications()
    {
        $client = $this->createAuthenticatedFamilySearchClient();
        $features = $client->getAvailablePendingModifications();
        $this->assertNotEmpty($features);
        $this->assertGreaterThan(0, count($features));
        foreach($features as $feature)
        {
            $this->assertInstanceOf('Gedcomx\Extensions\FamilySearch\Feature', $feature);
        }
    }
    
    public function testSetPendingModifications()
    {
        $features = $this->createAuthenticatedFamilySearchClient()->getAvailablePendingModifications();
        $modifications = array_map(function (Feature $feature) {
            return $feature->getName();
        }, $features);
        
        $client = $this->createAuthenticatedFamilySearchClient($modifications);
        
        $person = PersonBuilder::buildPerson('');
        $responseState = $client->familytree()->addPerson($person);
        $this->queueForDelete($responseState);

        // Ensure a response came back
        $this->assertNotNull($responseState);
        $check = array();
        /** @var HeaderInterface $header */
        foreach ($responseState->getRequest()->getHeaders() as $header) {
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
}