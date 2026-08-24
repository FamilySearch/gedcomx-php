<?php

namespace Gedcomx\Tests\Unit;

use Gedcomx\Extensions\FamilySearch\Platform\Names\NameSearchInfo;
use Gedcomx\Types\NamePartType;
use Gedcomx\Tests\ApiTestCase;

class NamesTest extends ApiTestCase
{
    public function testNameSearchInfoConstruction()
    {
        $info = new NameSearchInfo();
        $info->setText('John');
        $info->setNameId('12345');
        $info->setNamePartType(NamePartType::GIVEN);
        $info->setWeight(100);

        $this->assertEquals('John', $info->getText());
        $this->assertEquals('12345', $info->getNameId());
        $this->assertEquals(NamePartType::GIVEN, $info->getNamePartType());
        $this->assertEquals(100, $info->getWeight());
    }

    public function testNameSearchInfoWithKnownNamePartType()
    {
        $info = new NameSearchInfo();
        $info->setNamePartType('http://gedcomx.org/Given');

        $this->assertEquals('http://gedcomx.org/Given', $info->getNamePartType());
        $this->assertEquals('Given', $info->getKnownNamePartType());
    }

    public function testNameSearchInfoSetKnownNamePartType()
    {
        $info = new NameSearchInfo();
        $info->setKnownNamePartType('Surname');

        $this->assertEquals('Surname', $info->getNamePartType());
        $this->assertEquals('Surname', $info->getKnownNamePartType());
    }

    public function testNameSearchInfoJsonRoundTrip()
    {
        $info = new NameSearchInfo([
            'text' => 'Smith',
            'nameId' => 'N-123',
            'namePartType' => NamePartType::SURNAME,
            'weight' => 95
        ]);

        $json = $info->toJson();
        $this->assertStringContainsString('Smith', $json);
        $this->assertStringContainsString('N-123', $json);

        $decoded = json_decode($json, true);
        $info2 = new NameSearchInfo($decoded);

        $this->assertEquals('Smith', $info2->getText());
        $this->assertEquals('N-123', $info2->getNameId());
        $this->assertEquals(95, $info2->getWeight());
    }

    public function testNameSearchInfoWithArrayConstruction()
    {
        $data = [
            'text' => 'Mary',
            'nameId' => 'N-456',
            'weight' => 85
        ];

        $info = new NameSearchInfo($data);

        $this->assertEquals('Mary', $info->getText());
        $this->assertEquals('N-456', $info->getNameId());
        $this->assertEquals(85, $info->getWeight());
    }

    public function testNameSearchInfoNullValues()
    {
        $info = new NameSearchInfo();

        $this->assertNull($info->getText());
        $this->assertNull($info->getNameId());
        $this->assertNull($info->getNamePartType());
        $this->assertNull($info->getWeight());
        $this->assertNull($info->getKnownNamePartType());
    }

    public function testNameSearchInfoWeightIsInteger()
    {
        $info = new NameSearchInfo([
            'weight' => 75
        ]);

        $this->assertIsInt($info->getWeight());
        $this->assertEquals(75, $info->getWeight());
    }
}
