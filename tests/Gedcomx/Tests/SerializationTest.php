<?php


namespace Gedcomx\Tests;

use Gedcomx\Gedcomx;

class SerializationTest extends \PHPUnit_Framework_TestCase {

    public function testSerializeDeserialize()
    {
        $content = file_get_contents(__DIR__ . '/alma-heaton.json');
        //print $content;
        $gx1 = new Gedcomx(json_decode($content, true));
        $content_processed = $gx1->toJson();
        //print $content_processed;
        $gx2 = new Gedcomx(json_decode($content_processed, true));
        $this->assertEquals($gx1->toArray(), $gx2->toArray());
    }

}
