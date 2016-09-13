<?php

namespace Gedcomx\Tests\Functional;

use Gedcomx\Gedcomx;
use Gedcomx\Tests\ApiTestCase;

class XMLTests extends ApiTestCase
{
    
    public function testDeserializeXML()
    {
        $xmlReader = new \XMLReader();
        $xmlReader->XML(file_get_contents($this->testRootDir.'/files/record.xml'));
        $gedcomX = new Gedcomx($xmlReader);
        $extenstions = $gedcomX->getPersons()[0]->getSources()[0]->getExtensionElements();
        $this->assertEquals(3, count($extenstions));
    }

}
