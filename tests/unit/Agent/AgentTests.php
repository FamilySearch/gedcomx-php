<?php

namespace Gedcomx\Tests\Unit\Agent;

use Gedcomx\Tests\ApiTestCase;
use Gedcomx\Agent\Agent;

class AgentTests extends ApiTestCase
{
    public function testAgentDeserialization()
    {
        $agent = new Agent($this->loadJson('agent.json'));

        $this->assertEquals('A-1', $agent->getId());

        $names = $agent->getNames();
        $this->assertCount(1, $names);
        $this->assertEquals('Jane Doe', $names[0]->getValue());

        $emails = $agent->getEmails();
        $this->assertCount(1, $emails);
        $this->assertEquals('mailto:jane.doe@example.com', $emails[0]->getResource());

        $homepage = $agent->getHomepage();
        $this->assertNotNull($homepage);
        $this->assertEquals('https://example.com/janedoe', $homepage->getResource());
    }

    public function testAgentGettersAndSetters()
    {
        $agent = new Agent();
        $agent->setId('A-2');

        $this->assertEquals('A-2', $agent->getId());
    }

    public function testAgentWithoutNames()
    {
        $agent = new Agent();
        $agent->setId('A-3');

        $this->assertNull($agent->getNames());
    }
}
