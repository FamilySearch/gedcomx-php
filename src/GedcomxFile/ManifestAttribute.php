<?php

namespace Gedcomx\GedcomxFile;

class ManifestAttribute
{
    private $key;
    private $value;

    /**
     * Create a new instance of a ManifestAttribute
     *
     * @param string $key
     * @param string $value
     */
    public function __construct($key, $value)
    {
        $this->key = $key;
        $this->value = $value;
    }

    /**
     * Define what a ManifestAttribute string output
     *
     * @return string
     */
    function __toString(){
        return $this->key . ": " . $this->value;
    }

    /**
     * Convert ManifestAttribute to a string
     *
     * @return string
     */
    public function toString()
    {
        return $this->__toString();
    }

    /**
     * Get the key value for this attribute
     *
     * @return string
     */
    public function getKey()
    {
        return $this->key;
    }

    /**
     * Set the key value for this attribute
     *
     * @param string $key
     */
    public function setKey($key)
    {
        $this->key = $key;
    }

    /**
     * Get the value for this attribute
     *
     * @return string
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Set the value for this attribute
     *
     * @param string $value
     */
    public function setValue($value)
    {
        $this->value = $value;
    }
}