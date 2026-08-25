<?php

namespace Gedcomx\Tests\Unit;

use Gedcomx\Common\TextValue;
use Gedcomx\Extensions\FamilySearch\Platform\Users\AgentName;
use Gedcomx\Tests\ApiTestCase;

class UsersAgentNameTest extends ApiTestCase
{
    public function testAgentNameExtendsTextValue()
    {
        $name = new AgentName();

        $this->assertInstanceOf(TextValue::class, $name);
        $this->assertInstanceOf(AgentName::class, $name);
    }

    public function testAgentNameConstructorNoArgs()
    {
        $name = new AgentName();

        $this->assertNull($name->getType());
        $this->assertNull($name->getValue());
        $this->assertNull($name->getLang());
    }

    public function testAgentNameConstructorWithArgs()
    {
        $name = new AgentName('http://gedcomx.org/BirthName', 'John Doe', 'en');

        $this->assertEquals('http://gedcomx.org/BirthName', $name->getType());
        $this->assertEquals('John Doe', $name->getValue());
        $this->assertEquals('en', $name->getLang());
    }

    public function testAgentNameConstructorWithPartialArgs()
    {
        $name = new AgentName('http://gedcomx.org/BirthName', 'Jane Smith');

        $this->assertEquals('http://gedcomx.org/BirthName', $name->getType());
        $this->assertEquals('Jane Smith', $name->getValue());
        $this->assertNull($name->getLang());
    }

    public function testAgentNameSetters()
    {
        $name = new AgentName();
        $name->setType('http://gedcomx.org/MarriedName');
        $name->setValue('Mary Johnson');
        $name->setLang('fr');

        $this->assertEquals('http://gedcomx.org/MarriedName', $name->getType());
        $this->assertEquals('Mary Johnson', $name->getValue());
        $this->assertEquals('fr', $name->getLang());
    }

    public function testAgentNameSetTypeReturnsSelf()
    {
        $name = new AgentName();
        $result = $name->setType('http://gedcomx.org/Test');

        $this->assertInstanceOf(AgentName::class, $result);
        $this->assertSame($name, $result);
    }

    public function testAgentNameJsonRoundTrip()
    {
        $name = new AgentName('http://gedcomx.org/BirthName', 'Robert Brown', 'en');

        $json = $name->toJson();
        $this->assertStringContainsString('Robert Brown', $json);
        $this->assertStringContainsString('BirthName', $json);

        $decoded = json_decode($json, true);
        $name2 = new AgentName();
        $name2->initFromArray($decoded);

        $this->assertEquals('Robert Brown', $name2->getValue());
        $this->assertEquals('http://gedcomx.org/BirthName', $name2->getType());
        $this->assertEquals('en', $name2->getLang());
    }

    public function testAgentNameToArray()
    {
        $name = new AgentName('http://gedcomx.org/Test', 'Test Name', 'es');

        $array = $name->toArray();

        $this->assertArrayHasKey('type', $array);
        $this->assertArrayHasKey('value', $array);
        $this->assertArrayHasKey('lang', $array);
        $this->assertEquals('http://gedcomx.org/Test', $array['type']);
        $this->assertEquals('Test Name', $array['value']);
        $this->assertEquals('es', $array['lang']);
    }

    public function testAgentNameInitFromArray()
    {
        $data = [
            'type' => 'http://gedcomx.org/NickName',
            'value' => 'Bob',
            'lang' => 'en'
        ];

        $name = new AgentName();
        $name->initFromArray($data);

        $this->assertEquals('http://gedcomx.org/NickName', $name->getType());
        $this->assertEquals('Bob', $name->getValue());
        $this->assertEquals('en', $name->getLang());
    }

    public function testAgentNameWithOnlyType()
    {
        $name = new AgentName();
        $name->setType('http://gedcomx.org/AlsoKnownAs');

        $array = $name->toArray();
        $this->assertArrayHasKey('type', $array);
        $this->assertArrayNotHasKey('value', $array);
    }

    public function testAgentNameInheritsTextValueMethods()
    {
        $name = new AgentName();

        // Test inherited setValue returns self
        $result = $name->setValue('Test');
        $this->assertInstanceOf(AgentName::class, $result);

        // Test inherited setLang returns self
        $result = $name->setLang('de');
        $this->assertInstanceOf(AgentName::class, $result);

        $this->assertEquals('Test', $name->getValue());
        $this->assertEquals('de', $name->getLang());
    }

    public function testAgentNameCompleteWorkflow()
    {
        // Create with constructor
        $name1 = new AgentName('http://gedcomx.org/BirthName', 'Alice Cooper', 'en');

        // Serialize to JSON
        $json = $name1->toJson();

        // Deserialize
        $decoded = json_decode($json, true);
        $name2 = new AgentName();
        $name2->initFromArray($decoded);

        // Verify all properties match
        $this->assertEquals($name1->getType(), $name2->getType());
        $this->assertEquals($name1->getValue(), $name2->getValue());
        $this->assertEquals($name1->getLang(), $name2->getLang());
    }
}
