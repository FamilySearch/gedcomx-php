<?php

namespace Gedcomx\GedcomxFile;

use \ZipArchive;

/**
 * Class GedcomxFile
 * @package Gedcomx\GedcomxFile
 *
 * Provides methods for working with a GedcomX file.
 */
class GedcomxFile
{
    /**
     * The handle to the .gedx archive
     *
     * @var ZipArchive
     */
    private $archive;
    /**
     * The file attributes based on the manifest file.
     *
     * @var \Gedcomx\GedcomxFile\GedcomxFileEntry[]
     */
    private $entries;
    /**
     * Attributes from the manifest file
     *
     * @var array
     */
    private $attributes;
    /**
     * Warning messages generated during parsing.
     *
     * @var string[]
     */
    private $warnings;
    /**
     * @var \Gedcomx\GedcomxFile\GedcomxEntryDeserializer
     */
    private $deserializer;
    /**
     * @var Manifest
     */
    private $manifest;

    /**
     * Create a new instance of a GedcomxFile
     *
     * @param string                                        $filepath     The path to the .gedx file
     * @param \Gedcomx\GedcomxFile\GedcomxEntryDeserializer $deserializer The class to use for deserialization.
     *                                                                    Defaults to DefaultXMLSerialization
     *
     * @throws \Gedcomx\GedcomxFile\GedcomxFileException
     */
    public function __construct($filepath, GedcomxEntryDeserializer $deserializer = null)
    {
        $this->deserializer = $deserializer;
        if( $this->deserializer == null){
            $this->deserializer = new DefaultXMLSerialization();
        }

        $this->archive = new ZipArchive();
        $result = $this->archive->open($filepath);
        if ( $result !== true) {
            throw new GedcomxFileException($result);
        }

        $this->loadEntries();
    }

    /**
     * Return the entries found in the archive
     *
     * @return GedcomxFileEntry[]
     */
    public function getEntries()
    {
        return $this->entries;
    }

    /**
     * Return attributes from the manifest file
     *
     * @return array
     */
    public function getAttributes()
    {
        return $this->attributes;
    }

    /**
     * Return any warnings generated during parsing
     *
     * @return array
     */
    public function getWarnings()
    {
        return $this->warnings;
    }

    /**
     * Add an attribute to the list
     *
     * @param string $key
     * @param string $value
     */
    public function addAttribute($key, $value)
    {
        $this->attributes[$key] = $value;
    }

    /**
     * Return the value of an attribute
     *
     * @param string $key
     *
     * @return string|null
     */
    public  function getAttribute($key)
    {
        if( $this->manifest != null) {
            return $this->manifest->getAttribute($key);
        }

        return null;
    }

    /**
     * Deserialize a GedcomX resource
     *
     * @param \Gedcomx\GedcomxFile\GedcomxFileEntry $entry
     *
     * @return mixed
     */
    public function readResource(GedcomxFileEntry $entry)
    {
        return $this->deserializer->deserialize($entry->getContents());
    }

    /**
     * Close the archive file.
     */
    public function close()
    {
        if ($this->archive != null) {
            $this->archive->close();
        }
    }

    /**
     * Begin parsing by looking manifest file
     */
    private function loadEntries()
    {
        $this->readManifest();

        $i = 0;
        do {
            $name = $this->archive->getNameIndex($i);
            if ($name !== false && $name != 'META-INF/MANIFEST.MF') {
                $this->entries[] = $this->readEntry($name);
            }
            $i++;
        } while ($name !== false);
    }

    /**
     * Create a list of entries without a manifest file
     */
    private function readManifest()
    {
        $index = $this->archive->locateName("manifest.mf", ZipArchive::FL_NOCASE|ZipArchive::FL_NODIR);
        if ($index === false) {
            $this->warnings[] = "No MANIFEST.MF file was found.";
            return;
        }

        $this->manifest = new Manifest();
        $this->manifest->parse($this->archive->getFromIndex($index));
    }

    /**
     * Read an entry from the archive and look for manifest properties.
     *
     * @param string $name
     *
     * @return \Gedcomx\GedcomxFile\GedcomxFileEntry
     */
    public function readEntry($name)
    {
        $entry = new GedcomxFileEntry($name);
        $entry->setContents($this->archive->getFromName($name));
        $manifestAttrs = $this->manifest->getEntryAttributes($name);
        if ($manifestAttrs != null) {
            foreach ($manifestAttrs as $attr) {
                $entry->addAttribute($attr->getKey(), $attr->getValue());
            }
        } else {
            $this->warnings[] = 'No manifest attributes found for ' . $name;
        }

        return $entry;
    }
}