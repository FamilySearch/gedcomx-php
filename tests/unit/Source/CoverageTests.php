<?php

namespace Gedcomx\Tests\Unit\Source;

use Gedcomx\Source\Coverage;
use Gedcomx\Tests\ApiTestCase;

class CoverageTests extends ApiTestCase
{
    public function testCoverageDeserialization()
    {
        $json = $this->loadJson('coverage.json');
        $coverage = new Coverage($json);

        $this->assertEquals('http://gedcomx.org/Census', $coverage->getRecordType());
        $this->assertNotNull($coverage->getSpatial());
        $this->assertNotNull($coverage->getTemporal());
    }

    public function testCoverageGettersAndSetters()
    {
        $coverage = new Coverage();
        $coverage->setRecordType('http://gedcomx.org/BirthCertificate');

        $this->assertEquals('http://gedcomx.org/BirthCertificate', $coverage->getRecordType());
    }

    public function testCoverageWithoutSpatial()
    {
        $coverage = new Coverage([
            'recordType' => 'http://gedcomx.org/Marriage'
        ]);

        $this->assertEquals('http://gedcomx.org/Marriage', $coverage->getRecordType());
        $this->assertNull($coverage->getSpatial());
    }
}
