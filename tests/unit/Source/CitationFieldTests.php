<?php

namespace Gedcomx\Tests\Unit\Source;

use Gedcomx\Source\CitationField;
use Gedcomx\Tests\ApiTestCase;

class CitationFieldTests extends ApiTestCase
{
    public function testCitationFieldDeserialization()
    {
        $json = $this->loadJson('citation-field.json');
        $field = new CitationField($json);

        $this->assertEquals('volume', $field->getName());
        $this->assertEquals('Volume 12', $field->getValue());
    }

    public function testCitationFieldGettersAndSetters()
    {
        $field = new CitationField();
        $field->setName('page');
        $field->setValue('Page 42');

        $this->assertEquals('page', $field->getName());
        $this->assertEquals('Page 42', $field->getValue());
    }

    public function testCitationFieldEmpty()
    {
        $field = new CitationField();

        $this->assertNull($field->getName());
        $this->assertNull($field->getValue());
    }
}
