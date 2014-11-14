<?php

namespace Gedcomx\Extensions\FamilySearch\Rs\Client\Util;

use Gedcomx\Atom\Entry;
use Gedcomx\Extensions\FamilySearch\Platform\Tree\ChangeInfo;
use Gedcomx\Extensions\FamilySearch\Rt\FamilySearchPlatformLocalReferenceResolver;

class ChangeEntry extends Entry
{
    /** @var  $entry Entry */
    private $entry;
    /** @var  $changeInfo ChangeInfo */
    private $changeInfo;

    public function __construct(Entry $entry)
    {
        $this->entry = $entry;
        $this->changeInfo = $this->entry->findExtensionOfType('ChangeInfo');
    }

    public function getChangeInfo()
    {
        return $this->changeInfo;
    }

    public function getEntry()
    {
        return $this->entry;
    }

    public function getOperation()
    {
        return $this->changeInfo == null ? null : $this->changeInfo->getOperation();
    }

    public function getObjectType()
    {
        return $this->changeInfo == null ? null : $this->changeInfo->getObjectType();
    }

    public function getObjectModifier()
    {
        return $this->changeInfo == null ? null : $this->changeInfo->getObjectModifier();
    }

    public function getReason()
    {
        return $this->changeInfo == null ? null : $this->changeInfo->getReason();
    }

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