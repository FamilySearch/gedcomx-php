<?php

namespace Gedcomx\Vocab;

use Gedcomx\Common\TextValue;

class VocabElement
{
    /**
     * @var string
     */
    private $id;
    /**
     * @var string
     */
    private $uri;
    /**
     * @var string A uri
     */
    private $subclass;
    /**
     * @var string A uri
     */
    private $type;
    /**
     * @var string
     */
    private $sortName;
    /**
     * @var \Gedcomx\Common\TextValue[]
     */
    private $labels;
    /**
     * @var \Gedcomx\Common\TextValue[]
     */
    private $descriptions;
    /**
     * This is only present (OPTIONALLY) when used as an "Entries in a List" object
     * @var string A uri
     */
    private $sublist;
    /**
     * This is only present (OPTIONALLY) when used as an "Entries in a List" object
     * var int
     */
    private $position;

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param string $type
     */
    public function setType($type)
    {
        $this->type = $type;
    }

    /**
     * @return string
     */
    public function getSortName()
    {
        return $this->sortName;
    }

    /**
     * @param string $sortName
     */
    public function setSortName($sortName)
    {
        $this->sortName = $sortName;
    }

    /**
     * @return \Gedcomx\Common\TextValue[]
     */
    public function getLabels()
    {
        return $this->labels;
    }

    /**
     * @param \Gedcomx\Common\TextValue[] $labels
     */
    public function setLabels($labels)
    {
        $this->labels = $labels;
    }

    /**
     * @param string $label
     * @param string $locale
     *
     * @return $this
     */
    public function addLabel($label, $locale)
    {
        $t = new TextValue();
        $this->labels[] = $t->setValue($label)->setLang($locale);

        return $this;
    }

    /**
     * @return \Gedcomx\Common\TextValue[]
     */
    public function getDescriptions()
    {
        return $this->descriptions;
    }

    /**
     * @param \Gedcomx\Common\TextValue[] $descriptions
     */
    public function setDescriptions($descriptions)
    {
        $this->descriptions = $descriptions;
    }

    public function addDescription($description, $locale)
    {
        $t = new TextValue();
        $this->descriptions[] = $t->setValue($description)->setLang($locale);

        return $this;
    }

    /**
     * @return string
     */
    public function getSublist()
    {
        return $this->sublist;
    }

    /**
     * @param string $sublist
     */
    public function setSublist($sublist)
    {
        $this->sublist = $sublist;
    }

    /**
     * @return mixed
     */
    public function getPosition()
    {
        return $this->position;
    }

    /**
     * @param mixed $position
     */
    public function setPosition($position)
    {
        $this->position = $position;
    }

    public function getId()
    {
        return $this->id;
    }

    /**
     * @param string $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getUri()
    {
        return $this->uri;
    }

    /**
     * @param string $uri
     */
    public function setUri($uri)
    {
        $this->uri = $uri;
    }

    public function getSubclass()
    {
        return $this->subclass;
    }

    /**
     * @param string $subclass
     */
    public function setSubclass($subclass)
    {
        $this->subclass = $subclass;
    }

    public function compareTo(VocabElement $o)
    {
        // A position value overrides and trumps sortName
        // Otherwise, compare alphabetically against sortName
        // Then arbitrarily compare on Term Type, Concept, and Id
        $pos = 0;
        $oPosition = $o->getPosition();
        if ($this->position != null) {
            $pos = ($oPosition == null) ? $this->position : $this->position - $oPosition;
        } else {
            if ($oPosition != null) {
                $pos = $oPosition;
            }
        }
        if ($pos == 0) { // Either positions are the same or null
            $pos = $this->sortName == $o->getSortName() ? 1 : 0;
        }
        if ($pos == 0) {
            $pos = $this->type == $o->getType() ? 1 : 0;
        }
        if ($pos == 0) {
            $pos = $this->subclass == $o->getSubclass() ? 1 : 0;
        }
        if ($pos == 0) {
            $pos = $this->uri == $o->getUri() ? 1 : 0;
        }

        return $pos;
    }

}