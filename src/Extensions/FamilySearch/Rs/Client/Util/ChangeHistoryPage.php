<?php

namespace Gedcomx\Extensions\FamilySearch\Rs\Client\Util;

use Gedcomx\Atom\Entry;
use Gedcomx\Atom\Feed;
use Gedcomx\Extensions\FamilySearch\Platform\Tree\ChangeObjectModifier;
use Gedcomx\Extensions\FamilySearch\Platform\Tree\ChangeObjectType;
use Gedcomx\Extensions\FamilySearch\Platform\Tree\ChangeOperation;
use ReflectionClass;

class ChangeHistoryPage
{
    private $feed;
    private $entries;

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

    public function getFeed()
    {
        return $this->feed;
    }

    public function getEntries()
    {
        return $this->entries;
    }

    public function findChange(ChangeOperation $operation, ChangeObjectType $objectType)
    {
        return $this->findChangeWithModifier($operation, $objectType, null);
    }

    public function findChangeWithModifier(ChangeOperation $operation, ChangeObjectType $objectType, ChangeObjectModifier $modifier)
    {
        $changes = $this->findChangesBySingleWithModifier($operation, $objectType, $modifier);
        return count($changes) > 0 ? $changes[0] : null;
    }

    public function findChangesBySingle(ChangeOperation $operation, ChangeObjectType $objectType)
    {
        $ops = array();
        $types = array();
        array_push($ops, $operation);
        array_push($types, $objectType);
        return $this->findChangesByMany($ops, $types);
    }

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

    public function findChangesByMany(array $operations, array $types)
    {
        $modClass = new ReflectionClass('ChangeObjectModifier');
        return $this->findChangesByManyWithModifiers($operations, $types, $modClass->getConstants());
    }

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