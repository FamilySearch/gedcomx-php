<?php
/**
 *
 * 
 *
 * Generated by <a href="http://enunciate.codehaus.org">Enunciate</a>.
 *
 */

namespace Gedcomx\Common;

/**
 * A data qualifier. Qualifiers are used to "qualify" certain data elements to provide additional context, information, or details.
 */
class Qualifier
{

    /**
     * The value of the qualifier.
     *
     * @var string
     */
    private $value;

    /**
     * Constructs a Qualifier from a (parsed) JSON hash
     *
     * @param array $o
     */
    public function __construct($o = null)
    {
        if ($o) {
            $this->initFromArray($o);
        }
    }

    /**
     * The value of the qualifier.
     *
     * @return string
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * The value of the qualifier.
     *
     * @param string $value
     */
    public function setValue($value)
    {
        $this->value = $value;
    }
    /**
     * Returns the associative array for this Qualifier
     *
     * @return array
     */
    public function toArray()
    {
        $a = array();
        if ($this->value) {
            $a["value"] = $this->value;
        }
        return $a;
    }

    /**
     * Returns the JSON string for this Qualifier
     *
     * @return string
     */
    public function toJson()
    {
        return json_encode($this->toArray());
    }

    /**
     * Initializes this Qualifier from an associative array
     *
     * @param array $o
     */
    public function initFromArray($o)
    {
        if (isset($o['value'])) {
            $this->value = $o["value"];
        }
    }
}