<?php

namespace Gedcomx\GedcomxFile;

class GedcomxFileEntry
{
    /**
     * @var string Entry name
     */
    private $name;
    /**
     * @var string The contents of the archive entry
     */
    private $contents;
    /**
     * @var string[] Attributes from the METAINF.MF file
     */
    private $attributes;
    /**
     * @var string[] Any warning generated during parsing.
     */
    private $warnings;

    /**
     * @return mixed
     */
    public function getWarnings()
    {
        return $this->warnings;
    }

    /**
     * @param mixed $warnings
     */
    public function setWarnings($warnings)
    {
        $this->warnings = $warnings;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return mixed
     */
    public function getContents()
    {
        return $this->contents;
    }

    /**
     * @param mixed $contents
     */
    public function setContents($contents)
    {
        $this->contents = $contents;
    }

    /**
     * @return mixed
     */
    public function getAttributes()
    {
        return $this->attributes;
    }

    /**
     * @param mixed $attributes
     */
    public function setAttributes($attributes)
    {
        $this->attributes = $attributes;
    }

    /**
     * @param string $block A block of data from a MANIFEST.MF file
     */
    public function parseEntryData($block)
    {

    }
}