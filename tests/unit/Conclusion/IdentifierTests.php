<?php

namespace Gedcomx\Tests\Unit\Conclusion;

use Gedcomx\Conclusion\Identifier;
use Gedcomx\Tests\ApiTestCase;

class IdentifierTests extends ApiTestCase
{
    public function testIdentifierDeserialization()
    {
        $json = $this->loadJson('identifier.json');
        $identifier = new Identifier($json);

        $this->assertEquals('http://gedcomx.org/Persistent', $identifier->getType());
        $this->assertEquals('https://familysearch.org/ark:/61903/4:1:TEST-123', $identifier->getValue());
    }

    public function testIdentifierGettersAndSetters()
    {
        $identifier = new Identifier();
        $identifier->setType('http://gedcomx.org/Primary');
        $identifier->setValue('ID-12345');

        $this->assertEquals('http://gedcomx.org/Primary', $identifier->getType());
        $this->assertEquals('ID-12345', $identifier->getValue());
    }

    public function testIdentifierWithoutType()
    {
        $identifier = new Identifier(['value' => 'TEST-VALUE']);

        $this->assertEquals('TEST-VALUE', $identifier->getValue());
        $this->assertNull($identifier->getType());
    }
}
