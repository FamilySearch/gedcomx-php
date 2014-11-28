<?php

namespace Gedcomx\Common;

use Gedcomx\Source\SourceReference;

/**
 * Class CustomEntity
 * @package Gedcomx\Common
 *
 *          A class to support custom entity definitions
 */
class CustomEntity
{
    private $id;
    private $refToSomething;
    private $uniqueKeyedItems;
    private $keyedItems;
    private $source;

    public function __construct($id)
    {
        $this->id = $id;
    }

    /*
     * return string
     */
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
     * Set a reference to another object
     *
     * @return mixed
     */
    public function getRefToSomething()
    {
        return $this->refToSomething;
    }

    /**
     * Get the reference to another object
     *
     * @param mixed $refToSomething
     */
    public function setRefToSomething($refToSomething)
    {
        $this->refToSomething = $refToSomething;
    }

    /**
     * Get the list of keyed items for this object
     *
     * @return CustomKeyedItem[]
     */
    public function getKeyedItems()
    {
        return $this->keyedItems;
    }

    /**
     * Set the list of keyed items for this object
     *
     * @param CustomKeyedItem[] $keyedItems
     */
    public function setKeyedItems(array $keyedItems)
    {
        $this->keyedItems = $keyedItems;
    }

    /**
     * Get the array of uniquely keyed items for this object
     *
     * @return UniqueCustomKeyedItem[]
     */
    public function getUniqueKeyedItems()
    {
        return $this->uniqueKeyedItems;
    }

    /**
     * Set array of uniquely keyed items for this object
     *
     * @param UniqueCustomKeyedItem[] $uniqueKeyedItems
     */
    public function setUniqueKeyedItems(array $uniqueKeyedItems)
    {
        $this->uniqueKeyedItems = $uniqueKeyedItems;
    }

    /**
     * Get the source reference for this object
     *
     * @return \Gedcomx\Source\SourceReference
     */
    public function getSource()
    {
        return $this->source;
    }

    /**
     * Set the source reference for this object
     *
     * @param \Gedcomx\Source\SourceReference $source
     */
    public function setSource(SourceReference $source)
    {
        $this->source = $source;
    }
}
