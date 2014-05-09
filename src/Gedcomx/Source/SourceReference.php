<?php
/**
 *
 * 
 *
 * Generated by <a href="http://enunciate.codehaus.org">Enunciate</a>.
 *
 */

namespace Gedcomx\Source;

/**
 * An attributable reference to a description of a source.
 */
class SourceReference extends \Gedcomx\Links\HypermediaEnabledData
{

    /**
     * A reference to a description of the source being referenced.
     *
     * @var string
     */
    private $descriptionRef;

    /**
     * The attribution metadata for this source reference.
     *
     * @var \Gedcomx\Common\Attribution
     */
    private $attribution;

    /**
     * The qualifiers associated with this source reference.
     *
     * @var \Gedcomx\Common\Qualifier[]
     */
    private $qualifiers;

    /**
     * Constructs a SourceReference from a (parsed) JSON hash
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
     * A reference to a description of the source being referenced.
     *
     * @return string
     */
    public function getDescriptionRef()
    {
        return $this->descriptionRef;
    }

    /**
     * A reference to a description of the source being referenced.
     *
     * @param string $descriptionRef
     */
    public function setDescriptionRef($descriptionRef)
    {
        $this->descriptionRef = $descriptionRef;
    }
    /**
     * The attribution metadata for this source reference.
     *
     * @return \Gedcomx\Common\Attribution
     */
    public function getAttribution()
    {
        return $this->attribution;
    }

    /**
     * The attribution metadata for this source reference.
     *
     * @param \Gedcomx\Common\Attribution $attribution
     */
    public function setAttribution($attribution)
    {
        $this->attribution = $attribution;
    }
    /**
     * The qualifiers associated with this source reference.
     *
     * @return \Gedcomx\Common\Qualifier[]
     */
    public function getQualifiers()
    {
        return $this->qualifiers;
    }

    /**
     * The qualifiers associated with this source reference.
     *
     * @param \Gedcomx\Common\Qualifier[] $qualifiers
     */
    public function setQualifiers($qualifiers)
    {
        $this->qualifiers = $qualifiers;
    }
    /**
     * Returns the associative array for this SourceReference
     *
     * @return array
     */
    public function toArray()
    {
        $a = parent::toArray();
        if ($this->descriptionRef) {
            $a["description"] = $this->descriptionRef;
        }
        if ($this->attribution) {
            $a["attribution"] = $this->attribution->toArray();
        }
        if ($this->qualifiers) {
            $ab = array();
            foreach ($this->qualifiers as $i => $x) {
                $ab[$i] = $x->toArray();
            }
            $a['qualifiers'] = $ab;
        }
        return $a;
    }


    /**
     * Initializes this SourceReference from an associative array
     *
     * @param array $o
     */
    public function initFromArray($o)
    {
        parent::initFromArray($o);
        if (isset($o['description'])) {
            $this->descriptionRef = $o["description"];
        }
        if (isset($o['attribution'])) {
                $this->attribution = new \Gedcomx\Common\Attribution($o["attribution"]);
        }
        $this->qualifiers = array();
        if (isset($o['qualifiers'])) {
            foreach ($o['qualifiers'] as $i => $x) {
                    $this->qualifiers[$i] = new \Gedcomx\Common\Qualifier($x);
            }
        }
    }
}