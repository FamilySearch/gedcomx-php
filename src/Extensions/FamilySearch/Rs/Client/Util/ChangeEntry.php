<?php

namespace Gedcomx\Extensions\FamilySearch\Rs\Client\Util;

use Gedcomx\Atom\Entry;
use Gedcomx\Extensions\FamilySearch\Platform\Tree\ChangeInfo;
use Gedcomx\Extensions\FamilySearch\Rt\FamilySearchPlatformLocalReferenceResolver;

/**
 * Represents a historical record of some operation performed (such as deleting a person).
 *
 * Class ChangeEntry
 *
 * @package Gedcomx\Extensions\FamilySearch\Rs\Client\Util
 */
class ChangeEntry extends Entry
{
    /** @var  $entry Entry */
    private $entry;
    /** @var  $changeInfo ChangeInfo */
    private $changeInfo;

    /**
     * Constructs a new change entry using the specified atom feed entry.
     *
     * @param \Gedcomx\Atom\Entry $entry
     */
    public function __construct(Entry $entry)
    {
        $this->entry = $entry;
        $this->changeInfo = $this->entry->findExtensionOfType('Gedcomx\Extensions\FamilySearch\Platform\Tree\ChangeInfo');
    }

    /**
     * Gets the change information associated with this change.
     *
     * @return \Gedcomx\Extensions\FamilySearch\Platform\Tree\ChangeInfo|mixed
     */
    public function getChangeInfo()
    {
        return $this->changeInfo;
    }

    /**
     * Gets the atom entry associated with this change.
     *
     * @return \Gedcomx\Atom\Entry
     */
    public function getEntry()
    {
        return $this->entry;
    }

    /**
     * Gets the change operation associated with this change (if available).
     *
     * @return null|string
     */
    public function getOperation()
    {
        return $this->changeInfo == null ? null : $this->changeInfo->getOperation();
    }

    /**
     * Gets the change object type associated with this change (if available).
     *
     * @return null|string
     */
    public function getObjectType()
    {
        return $this->changeInfo == null ? null : $this->changeInfo->getObjectType();
    }

    /**
     * Gets the change object modifier associated with this change (if available).
     *
     * @return null|string
     */
    public function getObjectModifier()
    {
        return $this->changeInfo == null ? null : $this->changeInfo->getObjectModifier();
    }

    /**
     * Gets the reason this change was performed (if available).
     *
     * @return null|string
     */
    public function getReason()
    {
        return $this->changeInfo == null ? null : $this->changeInfo->getReason();
    }

    /**
     * Gets the original value from before the change.
     *
     * @return null
     */
    public function getOriginalValue()
    {
        $changeInfo = $this->getChangeInfo();
        if ($changeInfo != null && $this->getEntry()->getContent() != null && $this->getEntry()->getContent()->getGedcomx() != null) {
            $original = $changeInfo->getOriginal();
            if ($original != null) {
                return FamilySearchPlatformLocalReferenceResolver::resolveByRef($original, $this->getEntry()->getContent()->getGedcomx());
            }
        }

        return null;
    }

    /**
     * Gets the resulting value from after the change.
     *
     * @return null
     */
    public function getResultingValue()
    {
        $changeInfo = $this->getChangeInfo();
        if ($changeInfo != null && $this->getEntry()->getContent() != null && $this->getEntry()->getContent()->getGedcomx() != null) {
            $resulting = $changeInfo->getResulting();
            if ($resulting != null) {
                return FamilySearchPlatformLocalReferenceResolver::resolveByRef($resulting, $this->getEntry()->getContent()->getGedcomx());
            }
        }

        return null;
    }

    /**
     * Gets the value that was removed by the change.
     *
     * @return null
     */
    public function getRemovedValue()
    {
        $changeInfo = $this->getChangeInfo();
        if ($changeInfo != null && $this->getEntry()->getContent() != null && $this->getEntry()->getContent()->getGedcomx() != null) {
            $removed = $changeInfo->getRemoved();
            if ($removed != null) {
                return FamilySearchPlatformLocalReferenceResolver::resolveByRef($removed, $this->getEntry()->getContent()->getGedcomx());
            }
        }

        return null;
    }
}