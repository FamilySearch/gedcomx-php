<?php

namespace Gedcomx\Tests\Functional;

use Gedcomx\Extensions\FamilySearch\Rs\Client\FamilySearchClient;
use Gedcomx\Rs\Client\Util\HttpStatus;
use Gedcomx\Tests\ApiTestCase;
use Gedcomx\Tests\PersonBuilder;
use Gedcomx\Tests\SandboxCredentials;

class FamilySearchClientTests extends ApiTestCase
{
    
    public function testAuthenticate()
    {
        $client = new FamilySearchClient(array(
            'environment' => 'sandbox',
            'clientId' => SandboxCredentials::API_KEY
        ));
        $client->authenticateViaOAuth2Password(SandboxCredentials::USERNAME, SandboxCredentials::PASSWORD);
        $this->assertNotNull($client->getAccessToken());
    }
    
    public function testCreatePerson()
    {
        $client = new FamilySearchClient(array(
            'environment' => 'sandbox',
            'clientId' => SandboxCredentials::API_KEY
        ));
        $client->authenticateViaOAuth2Password(SandboxCredentials::USERNAME, SandboxCredentials::PASSWORD);
        
        $person = PersonBuilder::buildPerson('');
        $tree = $client->familytree();
        print_r($tree);
        $responseState = $tree->addPerson($person);
        
        $this->assertInstanceOf('Gedcomx\Rs\Client\GedcomxApplicationState', $responseState);
        $this->queueForDelete($state);
        $this->assertEquals(
            HttpStatus::CREATED,
            $responseState->getResponse()->getStatusCode(),
            $this->buildFailMessage(__METHOD__, $responseState)
        );
    }
}