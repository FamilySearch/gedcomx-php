<?php

namespace Gedcomx\Tests\Functional;

use Gedcomx\Rs\Client\Util\HttpStatus;
use Gedcomx\Tests\ApiTestCase;
use Gedcomx\Tests\PersonBuilder;

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
        
        $this->assertInstanceOf('Gedcomx\Rs\Client\GedcomxApplicationState', $responseState);
        $this->queueForDelete($responseState);
        $this->assertEquals(
            HttpStatus::CREATED,
            $responseState->getResponse()->getStatusCode(),
            $this->buildFailMessage(__METHOD__, $responseState)
        );
    }
    
    public function testGetOAuth2AuthorizationURL()
    {
        $client = $this->createFamilySearchClient();
        $url = $client->getOAuth2AuthorizationURL();
        $this->assertEquals('https://integration.familysearch.org/cis-web/oauth2/v3/authorization?response_type=code&redirect_uri=http%3A%2F%2Fexample.com%2Fredirect&client_id=WCQY-7J1Q-GKVV-7DNM-SQ5M-9Q5H-JX3H-CMJK', $url);
    }
}