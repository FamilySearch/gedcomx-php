<?php

namespace Gedcomx\Extensions\FamilySearch\Rs\Client\Util;

use Gedcomx\Atom\Entry;
use Gedcomx\Atom\Feed;
use Gedcomx\Extensions\FamilySearch\Platform\Tree\ChangeObjectModifier;
use Gedcomx\Extensions\FamilySearch\Platform\Tree\ChangeObjectType;
use Gedcomx\Extensions\FamilySearch\Platform\Tree\ChangeOperation;
use ReflectionClass;

/**
 * Represents a page of change histories.
 *
 * Class ChangeHistoryPage
 *
 * @package Gedcomx\Extensions\FamilySearch\Rs\Client\Util
 */
class ChangeHistoryPage extends Entry
{
    private $feed;
    private $entries;

    /**
     * Constructs a new change history page using the specified atom feed.
     *
     * @param \Gedcomx\Atom\Feed $feed
     */
    public function __construct(Feed $feed)
    {
        $this->feed = $feed;

        $entries = $feed->getEntries();
        $changes = array();
        if ($entries != null) {
            /** @var $entry Entry */
            foreach ($entries as $entry) {
                array_push($changes, new ChangeEntry($entry));
            }
        }

        $this->entries = $changes;
    }

    /**
     * Gets the feed associated with this page.
     *
     * @return \Gedcomx\Atom\Feed
     */
    public function getFeed()
    {
        return $this->feed;
    }

    /**
     * Gets the collection of change entries associated with this page.
     *
     * @return array
     */
    public function getEntries()
    {
        return $this->entries;
    }

    /**
     * Searches the current page of change entries for the type of object and operation changed.
     *
     * @param \Gedcomx\Extensions\FamilySearch\Platform\Tree\ChangeOperation  $operation
     * @param \Gedcomx\Extensions\FamilySearch\Platform\Tree\ChangeObjectType $objectType
     *
     * @return null
     */
    public function findChange(ChangeOperation $operation, ChangeObjectType $objectType)
    {
        return $this->findChangeWithModifier($operation, $objectType, null);
    }

    /**
     * Searches the current page of change entries for the type of object and operation changed and the modifier involved.
     *
     * @param \Gedcomx\Extensions\FamilySearch\Platform\Tree\ChangeOperation      $operation
     * @param \Gedcomx\Extensions\FamilySearch\Platform\Tree\ChangeObjectType     $objectType
     * @param \Gedcomx\Extensions\FamilySearch\Platform\Tree\ChangeObjectModifier $modifier
     *
     * @return null
     */
    public function findChangeWithModifier(ChangeOperation $operation, ChangeObjectType $objectType, ChangeObjectModifier $modifier)
    {
        $changes = $this->findChangesBySingleWithModifier($operation, $objectType, $modifier);
        return count($changes) > 0 ? $changes[0] : null;
    }

    /**
     * Searches the current page of change entries for the type of object and operation changed.
     *
     * @param \Gedcomx\Extensions\FamilySearch\Platform\Tree\ChangeOperation  $operation
     * @param \Gedcomx\Extensions\FamilySearch\Platform\Tree\ChangeObjectType $objectType
     *
     * @return array
     */
    public function findChangesBySingle(ChangeOperation $operation, ChangeObjectType $objectType)
    {
        $ops = array();
        $types = array();
        array_push($ops, $operation);
        array_push($types, $objectType);
        return $this->findChangesByMany($ops, $types);
    }

    /**
     * Searches the current page of change entries for the type of object and operation changed and the modifier involved.
     *
     * @param \Gedcomx\Extensions\FamilySearch\Platform\Tree\ChangeOperation      $operation
     * @param \Gedcomx\Extensions\FamilySearch\Platform\Tree\ChangeObjectType     $objectType
     * @param \Gedcomx\Extensions\FamilySearch\Platform\Tree\ChangeObjectModifier $modifier
     *
     * @return array
     */
    public function findChangesBySingleWithModifier(ChangeOperation $operation, ChangeObjectType $objectType, ChangeObjectModifier $modifier)
    {
        $modClass = new ReflectionClass('ChangeObjectModifier');
        $ops = array();
        $types = array();
        $mods = array();
        array_push($ops, $operation);
        array_push($types, $objectType);
        array_push($mods, $modifier);
        return $this->findChangesByManyWithModifiers($ops, $types, $modifier != null ? $mods : $modClass->getConstants());
    }

    /**
     * Searches the current page of change entries for the type of object and operation changed.
     *
     * @param array $operations
     * @param array $types
     *
     * @return array
     */
    public function findChangesByMany(array $operations, array $types)
    {
        $modClass = new ReflectionClass('ChangeObjectModifier');
        return $this->findChangesByManyWithModifiers($operations, $types, $modClass->getConstants());
    }

    /**
     * Searches the current page of change entries for the type of object and operation changed.
     *
     * @param array $operations
     * @param array $types
     * @param array $modifiers
     *
     * @return array
     */
    public function findChangesByManyWithModifiers(array $operations, array $types, array $modifiers)
    {
        $changes = array();
        foreach ($this->entries as $entry) {
            /** @var $entry ChangeEntry */
            $operation = $entry->getOperation();
            $type = $entry->getObjectType();
            $modifier = $entry->getObjectModifier();
            if ($operation !== null && $type !== null & in_array($operation, $operations) && in_array($type, $types)) {
                if ($modifier === null || in_array($modifier, $modifiers)) {
                    array_push($changes, $entry);
                }
            }
        }
        return $changes;
    }
}