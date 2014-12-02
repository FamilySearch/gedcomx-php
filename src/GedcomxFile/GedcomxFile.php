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
     * @var The file attributes based on the manifest file.
     */
    private $entries;
    /**
     * @var array Warning messages generated during parsing.
     */
    private $warnings;

    public function __construct($filepath)
    {
        $this->archive = new ZipArchive();
        $result = $this->archive->open($filepath);
        if ( $result !== true) {
            throw new GedcomxFileException($result);
        }

        $this->parseManifest();
    }

    private function parseManifest()
    {
        $index = $this->archive->locateName("manifest.mf", ZipArchive::FL_NOCASE|ZipArchive::FL_NODIR);
        $this->parseEntries($index);
    }

    private function parseEntries($index)
    {
        if ($index === false) {
            $this->parseEntriesFromArchive();
            return;
        }

        $manifest = $this->archive->getFromIndex($index);
        $this->parseEntriesFromManifest($manifest);
    }

    private function parseEntriesFromManifest($incoming){
        $blocks = explode("\n\n", $incoming);
        foreach ($blocks as $block) {
            $entry = new GedcomxFileEntry();
            $entry->parseEntryData($block);
            $this->addWarnings($entry);
            $this->entries[] = $entry;
        }
    }

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
}