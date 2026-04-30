<?php

namespace Gedcomx\Tests\Unit;

use Gedcomx\Agent\Agent;
use Gedcomx\Agent\Address;
use Gedcomx\Agent\OnlineAccount;
use Gedcomx\Tests\ApiTestCase;

/**
 * Tests for GEDCOM X agent models
 */
class AgentModelsTests extends ApiTestCase
{
    public function testAgentConstruction()
    {
        $agent = new Agent();
        $agent->setId('A-1');

        $this->assertEquals('A-1', $agent->getId());
    }

    public function testAgentWithDetails()
    {
        $agent = new Agent([
            'id' => 'A-1',
            'names' => [
                ['value' => 'John Smith']
            ],
            'emails' => [
                ['resource' => 'mailto:john@example.com']
            ]
        ]);

        $this->assertEquals('A-1', $agent->getId());
        $this->assertNotEmpty($agent->getNames());
    }

    public function testAddressConstruction()
    {
        $address = new Address();
        $address->setCity('Springfield');
        $address->setStateOrProvince('Illinois');
        $address->setCountry('USA');

        $this->assertEquals('Springfield', $address->getCity());
        $this->assertEquals('Illinois', $address->getStateOrProvince());
        $this->assertEquals('USA', $address->getCountry());
    }

    public function testAddressFromArray()
    {
        $address = new Address([
            'street' => '123 Main St',
            'city' => 'Boston',
            'stateOrProvince' => 'Massachusetts',
            'postalCode' => '02101',
            'country' => 'USA'
        ]);

        $this->assertEquals('123 Main St', $address->getStreet());
        $this->assertEquals('Boston', $address->getCity());
        $this->assertEquals('02101', $address->getPostalCode());
    }

    public function testOnlineAccountConstruction()
    {
        $account = new OnlineAccount();
        $account->setServiceHomepage('https://familysearch.org');
        $account->setAccountName('john_doe_123');

        $this->assertEquals('https://familysearch.org', $account->getServiceHomepage());
        $this->assertEquals('john_doe_123', $account->getAccountName());
    }

    public function testAgentJsonRoundTrip()
    {
        $agent = new Agent([
            'id' => 'A-TEST',
            'names' => [
                ['value' => 'Test Agent']
            ]
        ]);

        $json = $agent->toJson();
        $this->assertStringContainsString('A-TEST', $json);
        $this->assertStringContainsString('Test Agent', $json);

        $decoded = json_decode($json, true);
        $agent2 = new Agent($decoded);
        $this->assertEquals('A-TEST', $agent2->getId());
    }
}
