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
 * A set of data that supports extension elements.
 */
class ExtensibleData
{

    /**
     * A local, context-specific id for the data.
     *
     * @var string
     */
    private $id;

    /**
     * Constructs a ExtensibleData from a (parsed) JSON hash
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
     * A local, context-specific id for the data.
     *
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * A local, context-specific id for the data.
     *
     * @param string $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }
    /**
     * Returns the associative array for this ExtensibleData
     *
     * @return array
     */
    public function toArray()
    {
        $a = array();
        if ($this->id) {
            $a["id"] = $this->id;
        }
        return $a;
    }

    /**
     * Returns the JSON string for this ExtensibleData
     *
     * @return string
     */
    public function toJson()
    {
        return json_encode($this->toArray());
    }

    /**
     * Initializes this ExtensibleData from an associative array
     *
     * @param array $o
     */
    public function initFromArray($o)
    {
        if (isset($o['id'])) {
            $this->id = $o["id"];
        }
    }
}