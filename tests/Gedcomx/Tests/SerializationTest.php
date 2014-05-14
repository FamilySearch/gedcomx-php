<?php


namespace Gedcomx\Tests;

use Gedcomx\Gedcomx;
use Gedcomx\Records\RecordSet;
use XMLReader;

class SerializationTest extends \PHPUnit_Framework_TestCase {

    public function testSerializeDeserializeJson()
    {
        $content = file_get_contents(__DIR__ . '/alma-heaton.json');
        //print $content;
        $gx1 = new Gedcomx(json_decode($content, true));
        $content_processed = $gx1->toJson();
        //print $content_processed;
        $gx2 = new Gedcomx(json_decode($content_processed, true));
        $this->assertEquals($gx1->toArray(), $gx2->toArray());
    }

    public function testSerializeDeserializeXmlRecords()
    {
        $xml = new XMLReader();
        $this->assertTrue($xml->open(__DIR__ . '/1910806.sample.gedcomx.xml'));
        //print $content;
        $recordSet = new RecordSet($xml);
        $this->assertEquals(31, sizeof($recordSet->getRecords()));
        $this->assertEquals("#s1", $recordSet->getRecords()[0]->getDescriptionRef());
        //todo: do some more assertions...
    }

}
