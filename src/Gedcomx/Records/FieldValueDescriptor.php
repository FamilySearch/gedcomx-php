<?php
/**
 *
 * 
 *
 * Generated by <a href="http://enunciate.codehaus.org">Enunciate</a>.
 *
 */

namespace Gedcomx\Records;

/**
 * A way a field is to be displayed to a user.
 */
class FieldValueDescriptor extends \Gedcomx\Links\HypermediaEnabledData
{

    /**
     * Whether the treatment of the field value is optional. Used to determine whether it should be displayed even if the value is empty.
     *
     * @var boolean
     */
    private $optional;

    /**
     * The type of the field value.
     *
     * @var string
     */
    private $type;

    /**
     * The id of the label applicable to the field value
     *
     * @var string
     */
    private $labelId;

    /**
     * The labels to be used for display purposes.
     *
     * @var \Gedcomx\Common\TextValue[]
     */
    private $displayLabels;

    /**
     * Constructs a FieldValueDescriptor from a (parsed) JSON hash
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
     * Whether the treatment of the field value is optional. Used to determine whether it should be displayed even if the value is empty.
     *
     * @return boolean
     */
    public function getOptional()
    {
        return $this->optional;
    }

    /**
     * Whether the treatment of the field value is optional. Used to determine whether it should be displayed even if the value is empty.
     *
     * @param boolean $optional
     */
    public function setOptional($optional)
    {
        $this->optional = $optional;
    }
    /**
     * The type of the field value.
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * The type of the field value.
     *
     * @param string $type
     */
    public function setType($type)
    {
        $this->type = $type;
    }
    /**
     * The id of the label applicable to the field value
     *
     * @return string
     */
    public function getLabelId()
    {
        return $this->labelId;
    }

    /**
     * The id of the label applicable to the field value
     *
     * @param string $labelId
     */
    public function setLabelId($labelId)
    {
        $this->labelId = $labelId;
    }
    /**
     * The labels to be used for display purposes.
     *
     * @return \Gedcomx\Common\TextValue[]
     */
    public function getDisplayLabels()
    {
        return $this->displayLabels;
    }

    /**
     * The labels to be used for display purposes.
     *
     * @param \Gedcomx\Common\TextValue[] $displayLabels
     */
    public function setDisplayLabels($displayLabels)
    {
        $this->displayLabels = $displayLabels;
    }
    /**
     * Returns the associative array for this FieldValueDescriptor
     *
     * @return array
     */
    public function toArray()
    {
        $a = parent::toArray();
        if ($this->optional) {
            $a["optional"] = $this->optional;
        }
        if ($this->type) {
            $a["type"] = $this->type;
        }
        if ($this->labelId) {
            $a["labelId"] = $this->labelId;
        }
        if ($this->displayLabels) {
            $ab = array();
            foreach ($this->displayLabels as $i => $x) {
                $ab[$i] = $x->toArray();
            }
            $a['labels'] = $ab;
        }
        return $a;
    }


    /**
     * Initializes this FieldValueDescriptor from an associative array
     *
     * @param array $o
     */
    public function initFromArray($o)
    {
        parent::initFromArray($o);
        if (isset($o['optional'])) {
            $this->optional = $o["optional"];
        }
        if (isset($o['type'])) {
            $this->type = $o["type"];
        }
        if (isset($o['labelId'])) {
            $this->labelId = $o["labelId"];
        }
        $this->displayLabels = array();
        if (isset($o['labels'])) {
            foreach ($o['labels'] as $i => $x) {
                    $this->displayLabels[$i] = new \Gedcomx\Common\TextValue($x);
            }
        }
    }
}