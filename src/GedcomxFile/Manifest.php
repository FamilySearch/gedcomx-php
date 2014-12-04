<?php

namespace Gedcomx\GedcomxFile;

class Manifest
{
    const MANIFEST_FOLDER = 'META-INF';
    const MANIFEST_FILE = 'MANIFEST.MF';

    /**
     * Associative array containing ManifestAttribute[], keyed by file name
     *
     * @var array
     */
    private $entries;
    /**
     * Attributes of the mainifest file
     *
     * @var ManifestAttribute[]
     */
    private $attributes;

    /**
     * Create an instance of Gedcomx\GedcomxFile\Manifest
     */
    public function __construct()
    {
        $this->entries = array();
    }

    /**
     * Return the value of a given attribute
     *
     * @param string $key
     *
     * @return null|string
     */
    public function getAttribute($key)
    {
        foreach ($this->attributes as $attr) {
            if ($attr->getKey() == $key) {
                return $attr->getValue();
            }
        }

        return null;
    }

    /**
     * Return the attributes of a given file entry
     *
     * @param string $name
     *
     * @return MainfestAttribute[]|null
     */
    public function getEntryAttributes($name)
    {
        return isset($this->entries[$name]) ? $this->entries[$name] : null;
    }

    /**
     * Add an attribute to the manifest file
     *
     * @param string $key
     * @param string $value
     */
    public function addAttribute($key, $value)
    {
        $this->attributes[] = new ManifestAttribute($key, $value);
    }

    /**
     * Add an attribute to a given file entry.
     *
     * @param string $name
     * @param string $key
     * @param string $value
     */
    public function addAttributeToEntry($name, $key, $value)
    {
        $this->entries[$name][] = new ManifestAttribute($key, $value);
    }

    /**
     * Update a given attribute of a given file entry
     *
     * @param string $name
     * @param string $key
     * @param string $value
     */
    public function updateEntryAttribute($name, $key, $value)
    {
        foreach ($this->entries[$name] as $attr) {
            if($attr->getKey() == $key){
                $attr->setValue($value);
            }
        }
    }

    /**
     * Create a new entry record
     *
     * @param string $name
     */
    public function addEntry($name)
    {
        $this->entries[$name] = array();
        $this->entries[$name][] = new ManifestAttribute("Name", $name);
    }

    /**
     * Parse an existing manifest file into its component objects
     *
     * @param string $incoming
     */
    public function parse($incoming){
        $incoming = $this->normalizeLineEndings($incoming);
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
     * Convert the Manifest object to a string
     *
     * @return string
     */
    public function toString(){
        return $this->__toString();
    }

    /**
     * Define the string output
     *
     * @return string
     */
    public function __toString()
    {
        $output = '';
        foreach ($this->attributes as $attr) {
            $output .= $attr->toString() . "\n";
        }
        $output .= "\n";
        foreach ($this->entries as $entry) {
            foreach ($entry as $attr) {
                $output .= $attr->toString() . "\n";
            }
            $output .= "\n";
        }

        return $output;
    }

    /**
     * Create the a file entry
     *
     * @param string[] $blocks
     *
     * @return ManifestAttribute[]
     */
    private function createEntries($blocks)
    {
        $entries = array();

        foreach ($blocks as $block) {
            if (strlen($block) == 0) {
                continue;
            }

            $lines = explode("\n", $block);
            $name = null;
            $entry = array();

            foreach ($lines as $line) {
                list($key, $value) = explode(": ", $line);
                $entry[] = new ManifestAttribute($key, $value);
                if ($key == "Name") {
                    $name = $value;
                }
            }
            $entries[$name] = $entry;
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