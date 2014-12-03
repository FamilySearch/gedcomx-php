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
     * @var ZipArchive The handle to the .gedx archive
     */
    private $archive;
    /**
     * @var \Gedcomx\GedcomxFile\GedcomxFileEntry[] The file attributes based on the manifest file.
     */
    private $entries;
    /**
     * @var array Attributes from the manifest file
     */
    private $attributes;
    /**
     * @var string[] Warning messages generated during parsing.
     */
    private $warnings;
    /**
     * @var \Gedcomx\GedcomxFile\GedcomxEntryDeserializer
     */
    private $deserializer;

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

        $this->parseManifest();
    }

    /**
     * Return the entries found in the archive
     *
     * @return array
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
        return isset($this->attributes[$key]) ? $this->attributes[$key] : null;
    }


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
    private function parseManifest()
    {
        $index = $this->archive->locateName("manifest.mf", ZipArchive::FL_NOCASE|ZipArchive::FL_NODIR);
        $this->parseEntries($index);
    }

    /**
     * Call the correct parsing function based on whether or not a manifest file was found.
     *
     * @param integer $index
     */
    private function parseEntries($index)
    {
        if ($index === false) {
            $this->warnings[] = "No MANIFEST.MF file was found.";
            $this->parseEntriesFromArchive();
            return;
        }

        $manifest = $this->archive->getFromIndex($index);
        $manifest = $this->normalizeLineEndings($manifest);

        $this->parseEntriesFromManifest($manifest);
    }

    /**
     * Parse the contents of the manifest file
     *
     * @param string $incoming The contents of the manifest file
     */
    private function parseEntriesFromManifest($incoming){
        $blocks = explode("\n\n", $incoming);
        /*
         *  The first block should be the attributes of the gedx file itself.
         *  Parse and remove those before parsing attributes of the archive entries.
         */
        $lines = explode("\n",$blocks[0]);
        foreach ($lines as $line) {
            list($key, $value) = explode(": ", $line);
            $this->addAttribute($key, $value);
        }
        array_shift($blocks);
        /*
         *  Parse the entry attributes
         */
        $this->entries = $this->createEntries($blocks);
    }

    /**
     * Create a list of entries without a manifest file
     */
    private function parseEntriesFromArchive()
    {
        $i = 0;
        do {
            $name = $this->archive->getNameIndex($i);
            if ($name !== false){
                $entry = new GedcomxFileEntry();
                $entry->setName($name);
                $entry->setContents($this->archive->getFromIndex($i));
                $this->entries[] = $entry;
            }
            $i++;
        } while ($name !== false);
    }

    /**
     * Create the GedcomxFileEntry
     *
     * @param string[] $blocks
     *
     * @return \Gedcomx\GedcomxFile\GedcomxFileEntry[]
     */
    private function createEntries($blocks)
    {
        $entries = array();

        foreach ($blocks as $block) {
            if (strlen($block) == 0) {
                continue;
            }

            $entry = new GedcomxFileEntry();
            $entry->parseEntryData($block);
            $contents = $this->archive->getFromName($entry->getName());
            $entry->setContents($contents);
            if ($contents === false) {
                $this->warnings[] = "Manifest entry ".$entry->getName()." not found in the archive.";
            }
            $entries[] = $entry;
        }

        return $entries;
    }

    /**
     * Normalize line endings to LF
     *
     * @param $stringData
     *
     * @return string
     */
    private function normalizeLineEndings($stringData)
    {
        $stringData = str_replace("\r\n", "\n", $stringData);
        $stringData = str_replace("\r", "\n", $stringData);

        return $stringData;
    }
}