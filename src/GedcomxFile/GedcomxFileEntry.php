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
     * @var string[] Associative array of attributes from the METAINF.MF file
     */
    private $attributes;

    /**
     * Return the name in the archive
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set the name of the entry
     *
     * @param mixed $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * Return the contents of this entry
     *
     * @return string|null
     */
    public function getContents()
    {
        return $this->contents;
    }

    /**
     * The contents of the zip file entry
     *
     * @param string $contents
     */
    public function setContents($contents)
    {
        $this->contents = $contents;
    }

    /**
     * Return the attributes of this entry
     *
     * @return mixed
     */
    public function getAttributes()
    {
        return $this->attributes;
    }

    /**
     * Set the attributes array
     *
     * @param array $attributes
     */
    public function setAttributes($attributes)
    {
        $this->attributes = $attributes;
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
     * Return the value of a given key
     *
     * @param string $key
     *
     * @return null|string
     */
    public function getAttribute($key)
    {
        return isset($this->attributes[$key]) ? $this->attributes[$key] : null;
    }

    /**
     * Return the content type stored with this entry.
     *
     * @return null|string
     */
    public function getContentType()
    {
        return $this->getAttribute("Content-Type");
    }

    /**
     * Parse the attributes from a data block in the MANIFEST file
     *
     * @param string $block
     */
    public function parseEntryData($block)
    {
        $lines = explode("\n",$block);
        foreach ($lines as $line) {
            list($key, $value) = explode(": ", $line);
            $this->addAttribute($key, $value);
            if ($key == "Name") {
                $this->setName($value);
            }
        }
    }
}