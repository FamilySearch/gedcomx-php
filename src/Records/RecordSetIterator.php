<?php
namespace Gedcomx\Records;

use Gedcomx\Gedcomx;

/**
 * Use this class to iterate over a large set of records in order to avoid large memory and cpu consumption
 *
 * Class RecordSetIterator
 * @package Gedcomx\Records
 */
class RecordSetIterator
{

    const RECORD_NODE_NAME = 'record';

    /**
     * @var \XMLReader
     */
    private $xmlReader;

    /**
     * @param \XMLReader $xmlReader
     */
    public function __construct(\XMLReader $xmlReader)
    {
        $this->xmlReader = $xmlReader;
        $this->nextNode($xmlReader);
    }

    /**
     * @param bool $asString - true - return xml as string, false - return as Gedcomx object
     * @return Gedcomx|null|string - null when there are no more records
     */
    public function nextRecord($asString)
    {
        $xmlReader = $this->xmlReader;
        if ($xmlReader->name !== self::RECORD_NODE_NAME) {
            return null;
        }
        $record = $xmlReader->readOuterXml();
        $xmlReader->next(self::RECORD_NODE_NAME);
        if (!$asString) {
            $tempXmlReader = new \XMLReader();
            $tempXmlReader->XML($record);
            $record = new Gedcomx($tempXmlReader);
        }
        return $record;

    }

    /**
     * @param \XMLReader $xmlReader
     */
    private function nextNode(\XMLReader $xmlReader)
    {
        while ($xmlReader->read() && $xmlReader->name !== self::RECORD_NODE_NAME) {}
    }

} 
