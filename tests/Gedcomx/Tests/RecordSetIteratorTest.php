<?php
namespace Gedcomx\Tests;


use Gedcomx\Records\RecordSetIterator;

class RecordSetIteratorTest extends \PHPUnit_Framework_TestCase
{

    public function testNextAsString()
    {
        $xml = new \XMLReader();
        $this->assertTrue($xml->open(__DIR__ . '/1910806.sample.gedcomx.xml'));
        $iterator = new RecordSetIterator($xml);
        $count = 0;
        while (($record = $iterator->nextRecord(true)) !== null) {
            $count++;
            $this->assertTrue(is_string($record));
        }
        $this->assertEquals(31, $count);
    }

    public function testNextAsGedcomX()
    {
        $xml = new \XMLReader();
        $this->assertTrue($xml->open(__DIR__ . '/1910806.sample.gedcomx.xml'));
        $iterator = new RecordSetIterator($xml);
        $count = 0;
        while (($record = $iterator->nextRecord(false)) !== null) {
            $count++;
            $this->assertInstanceOf('Gedcomx\Gedcomx', $record);
        }
        $this->assertEquals(31, $count);

    }

}
 
