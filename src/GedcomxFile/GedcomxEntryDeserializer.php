<?php

namespace Gedcomx\GedcomxFile;

/**
 * Interface GedcomxEntryDeserializer
 * @package Gedcomx\src\GedcomxFile
 *
 *          Basic interface abstracting deserialization of an entry.
 */
interface GedcomxEntryDeserializer
{
    /**
     * Deserialize the resource from the specified input stream.
     *
     * @param string $incoming The text to deserialize.
     * @return mixed the resource.
     */
    public function deserialize($incoming);
}