<?php

namespace Gedcomx\Tests\Unit\Conclusion;

use Gedcomx\Conclusion\NamePart;
use Gedcomx\Tests\ApiTestCase;

class NamePartTests extends ApiTestCase
{
    public function testNamePartDeserialization()
    {
        $json = $this->loadJson('name-part.json');
        $namePart = new NamePart($json);

        $this->assertEquals('http://gedcomx.org/Given', $namePart->getType());
        $this->assertEquals('Elizabeth', $namePart->getValue());
    }

    public function testNamePartGettersAndSetters()
    {
        $namePart = new NamePart();
        $namePart->setType('http://gedcomx.org/Surname');
        $namePart->setValue('Anderson');

        $this->assertEquals('http://gedcomx.org/Surname', $namePart->getType());
        $this->assertEquals('Anderson', $namePart->getValue());
    }

    public function testNamePartWithoutType()
    {
        $namePart = new NamePart(['value' => 'Maria']);

        $this->assertEquals('Maria', $namePart->getValue());
        $this->assertNull($namePart->getType());
    }
}
