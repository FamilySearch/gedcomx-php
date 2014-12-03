<?php

namespace Gedcomx\GedcomxFile;

/**
 * Class DefaultXMLSerialization
 * @package Gedcomx\src\GedcomxFile
 *
 *          A class for for reading and writing GEDCOM X files.
 */
class DefaultXMLSerialization implements GedcomxEntrySerializer, GedcomxEntryDeserializer
{

    /**
     * Deserialize the resource from the specified input stream.
     *
     * @param string $incoming The text to deserialize.
     *
     * @return mixed the resource.
     */
    public function deserialize($incoming)
    {
        // TODO: Implement deserialize() method.
    }

    /**
     * Serialize the resource to the specified output stream.
     *
     * @param mixed  $resource
     * @param string $filename
     *
     * @return void
     */
    public function serialze($resource, $filename)
    {
        $xml = new \XMLWriter();
        $xml->openMemory();
        $xml->setIndent(true);
        $xml->setIndentString("    ");
        $xml->startDocument('1.0','UTF-8');
        $resource->toXml($xml);
        $xml->endDocument();

        $fOut = fopen($filename,'w');
        fwrite($fOut, $xml->outputMemory(true));
    }

    /**
     * Whether the specified content type is a known content type and therefore
     * does not need to be written to the entry attributes.
     *
     * @param string $contentType The content type.
     *
     * @return boolean Whether the content type is "known".
     */
    public function isKnownContentType($contentType)
    {
        // TODO: Implement isKnownContentType() method.
}}