<?php

namespace Gedcomx\GedcomxFile;

class Manifest
{
    const MANIFEST_FOLDER = 'META-INF';
    const MANIFEST_FILE = 'MANIFEST.MF';

    private $entries;
    private $attributes;

    public function __construct()
    {
        $this->entries = array();
    }

    public function addAttribute($key, $value)
    {
        $this->attributes[] = new ManifestAttribute($key, $value);
    }

    public function addAttributeToEntry($name, $key, $value)
    {
        $this->entries[$name]->addAttribute(new ManifestAttribute($key, $value));
    }

    public function updateEntryAttribute($name, $key, $value)
    {
        foreach ($this->entries[$name] as $attr) {
            if($attr->getKey() == $key){
                $attr->setValue($value);
            }
        }
    }

    public function addEntry($name)
    {
        $this->entries[$name]->addAttribute(new ManifestAttribute("Name", $name));
    }

    public function __toString()
    {
        $output = '';
        foreach ($this->attributes as $attr) {
            $output .= $attr->toString() . "\n";
        }
        $output .= "\n";
        foreach ($this->entries as $entry) {
            $output .= $entry->toString() . "\n";
        }
        $output .= "\n";

        return $output;
    }
}