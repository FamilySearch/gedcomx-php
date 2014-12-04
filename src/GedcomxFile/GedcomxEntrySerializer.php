<?php

namespace Gedcomx\GedcomxFile;

/**
 * Interface GedcomxEntrySerializer
 * @package Gedcomx\src\GedcomxFile
 *
 *          Basic interface for abstracting serialization of an entry.
 */
interface GedcomxEntrySerializer
{
    /**
     * Serialize the resource to the specified output stream.
     *
     * @param mixed $resource
     *
     * @return void
     */
    public function serialize($resource);

    /**
     * Whether the specified content type is a known content type and therefore
     * does not need to be written to the entry attributes.
     *
     * @param string $contentType The content type.
     *
     * @return boolean Whether the content type is "known".
     */
    public function isKnownContentType($contentType);
}