<?php

namespace Gedcomx\Tests\Unit\Conclusion;

use Gedcomx\Tests\ApiTestCase;
use Gedcomx\Conclusion\Name;

class NameTests extends ApiTestCase
{
    public function testNameDeserialization()
    {
        $name = new Name($this->loadJson('name.json'));

        $this->assertEquals('http://gedcomx.org/BirthName', $name->getType());

        $nameForms = $name->getNameForms();
        $this->assertCount(1, $nameForms);

        $nameForm = $nameForms[0];
        $this->assertEquals('John Smith', $nameForm->getFullText());

        $parts = $nameForm->getParts();
        $this->assertCount(2, $parts);
        $this->assertEquals('http://gedcomx.org/Given', $parts[0]->getType());
        $this->assertEquals('John', $parts[0]->getValue());
        $this->assertEquals('http://gedcomx.org/Surname', $parts[1]->getType());
        $this->assertEquals('Smith', $parts[1]->getValue());
    }

    public function testNameGettersAndSetters()
    {
        $name = new Name();
        $name->setType('http://gedcomx.org/MarriedName');

        $this->assertEquals('http://gedcomx.org/MarriedName', $name->getType());
    }

    public function testNameWithMultipleForms()
    {
        $name = new Name();
        $name->setType('http://gedcomx.org/BirthName');

        $this->assertNull($name->getNameForms());
    }
}
