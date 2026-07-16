<?php
/**
 * Copyright Intellectual Reserve, Inc.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *   http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

namespace Gedcomx\Extensions\FamilySearch\Platform\Tree;

/**
 * Class Tree
 *
 * A FamilySearch tree for genealogical research.
 *
 * Trees in FamilySearch represent collections of genealogical data that can be
 * private to an individual, shared with a group, or part of the collaborative
 * public tree. Each tree has access controls, metadata, and relationships to
 * groups and persons.
 *
 * @package Gedcomx\Extensions\FamilySearch\Platform\Tree
 */
class Tree
{
    /**
     * The tree id.
     *
     * Unique identifier for this tree in the FamilySearch system.
     *
     * @var string
     */
    private $id;

    /**
     * The ids of the groups this tree belongs to.
     *
     * Array of group identifiers that have access to this tree for
     * collaborative research.
     *
     * @var string[]
     */
    private $groupIds;

    /**
     * The tree name.
     *
     * Display name for the tree, visible to users with access.
     *
     * @var string
     */
    private $name;

    /**
     * The tree description.
     *
     * Detailed description of the tree's scope and purpose.
     *
     * @var string
     */
    private $description;

    /**
     * The tree starting person id.
     *
     * The person who serves as the root or starting point for this tree,
     * typically the tree owner or a key ancestor.
     *
     * @var string
     */
    private $startingPersonId;

    /**
     * The hidden state of the tree.
     *
     * If true, the tree is hidden from certain views or listings.
     *
     * @var bool
     */
    private $hidden;

    /**
     * The private state of the tree.
     *
     * If true, the tree is private and access is restricted. Note that
     * "private" is a reserved word in PHP, so we use "isPrivate" internally.
     *
     * @var bool
     */
    private $isPrivate;

    /**
     * The id of the collection the tree belongs to.
     *
     * Identifies which FamilySearch collection this tree is part of.
     *
     * @var string
     */
    private $collectionId;

    /**
     * The owner third party access state of the tree.
     *
     * Controls what third-party applications the tree owner can use to
     * access this tree. See ThirdPartyAccess for possible values.
     *
     * @var string
     */
    private $ownerAccess;

    /**
     * The group third party access state of the tree.
     *
     * Controls what third-party applications group members can use to
     * access this tree. See ThirdPartyAccess for possible values.
     *
     * @var string
     */
    private $groupAccess;

    /**
     * All third party access state of the tree.
     *
     * Controls the broadest level of third-party access to the tree.
     * See ThirdPartyAccess for possible values.
     *
     * @var string
     */
    private $allAccess;

    /**
     * Constructs a Tree from a (parsed) JSON hash or XML reader.
     *
     * @param mixed $o Either an array (JSON) or an XMLReader.
     *
     * @throws \Exception
     */
    public function __construct($o = null)
    {
        if (is_array($o)) {
            $this->initFromArray($o);
        }
        else if ($o instanceof \XMLReader) {
            $success = true;
            while ($success && $o->nodeType != \XMLReader::ELEMENT) {
                $success = $o->read();
            }
            if ($o->nodeType != \XMLReader::ELEMENT) {
                throw new \Exception("Unable to read XML: no start element found.");
            }

            $this->initFromReader($o);
        }
    }

    /**
     * Get the tree id.
     *
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set the tree id.
     *
     * @param string $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * Get the ids of the groups this tree belongs to.
     *
     * @return string[]
     */
    public function getGroupIds()
    {
        return $this->groupIds;
    }

    /**
     * Set the ids of the groups this tree belongs to.
     *
     * @param string[] $groupIds
     */
    public function setGroupIds($groupIds)
    {
        $this->groupIds = $groupIds;
    }

    /**
     * Get the tree name.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set the tree name.
     *
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * Get the tree description.
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set the tree description.
     *
     * @param string $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * Get the tree starting person id.
     *
     * @return string
     */
    public function getStartingPersonId()
    {
        return $this->startingPersonId;
    }

    /**
     * Set the tree starting person id.
     *
     * @param string $startingPersonId
     */
    public function setStartingPersonId($startingPersonId)
    {
        $this->startingPersonId = $startingPersonId;
    }

    /**
     * Get the hidden state of the tree.
     *
     * @return bool
     */
    public function getHidden()
    {
        return $this->hidden;
    }

    /**
     * Set the hidden state of the tree.
     *
     * @param bool $hidden
     */
    public function setHidden($hidden)
    {
        $this->hidden = $hidden;
    }

    /**
     * Get the private state of the tree.
     *
     * @return bool
     */
    public function getPrivate()
    {
        return $this->isPrivate;
    }

    /**
     * Set the private state of the tree.
     *
     * @param bool $isPrivate
     */
    public function setPrivate($isPrivate)
    {
        $this->isPrivate = $isPrivate;
    }

    /**
     * Get the id of the collection the tree belongs to.
     *
     * @return string
     */
    public function getCollectionId()
    {
        return $this->collectionId;
    }

    /**
     * Set the id of the collection the tree belongs to.
     *
     * @param string $collectionId
     */
    public function setCollectionId($collectionId)
    {
        $this->collectionId = $collectionId;
    }

    /**
     * Get the owner third party access state of the tree.
     *
     * @return string
     */
    public function getOwnerAccess()
    {
        return $this->ownerAccess;
    }

    /**
     * Set the owner third party access state of the tree.
     *
     * @param string $ownerAccess
     */
    public function setOwnerAccess($ownerAccess)
    {
        $this->ownerAccess = $ownerAccess;
    }

    /**
     * Get the group third party access state of the tree.
     *
     * @return string
     */
    public function getGroupAccess()
    {
        return $this->groupAccess;
    }

    /**
     * Set the group third party access state of the tree.
     *
     * @param string $groupAccess
     */
    public function setGroupAccess($groupAccess)
    {
        $this->groupAccess = $groupAccess;
    }

    /**
     * Get the all third party access state of the tree.
     *
     * @return string
     */
    public function getAllAccess()
    {
        return $this->allAccess;
    }

    /**
     * Set the all third party access state of the tree.
     *
     * @param string $allAccess
     */
    public function setAllAccess($allAccess)
    {
        $this->allAccess = $allAccess;
    }

    /**
     * Returns the associative array for this Tree.
     *
     * @return array
     */
    public function toArray()
    {
        $a = array();
        if ($this->id) {
            $a["id"] = $this->id;
        }
        if ($this->groupIds) {
            $a["groupIds"] = $this->groupIds;
        }
        if ($this->name) {
            $a["name"] = $this->name;
        }
        if ($this->description) {
            $a["description"] = $this->description;
        }
        if ($this->startingPersonId) {
            $a["startingPersonId"] = $this->startingPersonId;
        }
        if ($this->hidden !== null) {
            $a["hidden"] = $this->hidden;
        }
        if ($this->isPrivate !== null) {
            $a["private"] = $this->isPrivate;
        }
        if ($this->collectionId) {
            $a["collectionId"] = $this->collectionId;
        }
        if ($this->ownerAccess) {
            $a["ownerAccess"] = $this->ownerAccess;
        }
        if ($this->groupAccess) {
            $a["groupAccess"] = $this->groupAccess;
        }
        if ($this->allAccess) {
            $a["allAccess"] = $this->allAccess;
        }
        return $a;
    }

    /**
     * Returns the JSON string for this Tree.
     *
     * @return string
     */
    public function toJson()
    {
        return json_encode($this->toArray());
    }

    /**
     * Initializes this Tree from an associative array.
     *
     * @param array $o
     */
    public function initFromArray($o)
    {
        if (isset($o['id'])) {
            $this->id = $o["id"];
        }
        if (isset($o['groupIds'])) {
            $this->groupIds = $o["groupIds"];
        }
        if (isset($o['name'])) {
            $this->name = $o["name"];
        }
        if (isset($o['description'])) {
            $this->description = $o["description"];
        }
        if (isset($o['startingPersonId'])) {
            $this->startingPersonId = $o["startingPersonId"];
        }
        if (isset($o['hidden'])) {
            $this->hidden = $o["hidden"];
        }
        if (isset($o['private'])) {
            $this->isPrivate = $o["private"];
        }
        if (isset($o['collectionId'])) {
            $this->collectionId = $o["collectionId"];
        }
        if (isset($o['ownerAccess'])) {
            $this->ownerAccess = $o["ownerAccess"];
        }
        if (isset($o['groupAccess'])) {
            $this->groupAccess = $o["groupAccess"];
        }
        if (isset($o['allAccess'])) {
            $this->allAccess = $o["allAccess"];
        }
    }

    /**
     * Initializes this Tree from an XML reader.
     *
     * @param \XMLReader $xml The reader to use to initialize this object.
     */
    public function initFromReader($xml)
    {
        $empty = $xml->isEmptyElement;

        if ($xml->hasAttributes) {
            $moreAttributes = $xml->moveToFirstAttribute();
            while ($moreAttributes) {
                if (!$this->setKnownAttribute($xml)) {
                    //skip unknown attributes...
                }
                $moreAttributes = $xml->moveToNextAttribute();
            }
        }

        if (!$empty) {
            $xml->read();
            while ($xml->nodeType != \XMLReader::END_ELEMENT) {
                if ($xml->nodeType != \XMLReader::ELEMENT) {
                    //no-op: skip any insignificant whitespace, comments, etc.
                }
                else if (!$this->setKnownChildElement($xml)) {
                    $n = $xml->localName;
                    $ns = $xml->namespaceURI;
                    //skip the unknown element
                    while ($xml->nodeType != \XMLReader::END_ELEMENT && $xml->localName != $n && $xml->namespaceURI != $ns) {
                        $xml->read();
                    }
                }
                $xml->read(); //advance the reader.
            }
        }
    }

    /**
     * Sets a known child element of Tree from an XML reader.
     *
     * @param \XMLReader $xml The reader.
     *
     * @return bool Whether a child element was set.
     */
    protected function setKnownChildElement($xml)
    {
        $happened = false;
        if (($xml->localName == 'groupId') && ($xml->namespaceURI == 'http://familysearch.org/v1/')) {
            if ($this->groupIds === null) {
                $this->groupIds = array();
            }
            $this->groupIds[] = $xml->readString();
            $happened = true;
        }
        return $happened;
    }

    /**
     * Sets a known attribute of Tree from an XML reader.
     *
     * @param \XMLReader $xml The reader.
     *
     * @return bool Whether an attribute was set.
     */
    protected function setKnownAttribute($xml)
    {
        return false;
    }

    /**
     * Writes this Tree to an XML writer.
     *
     * @param \XMLWriter $writer The XML writer.
     * @param bool $includeNamespaces Whether to write out the namespaces in the element.
     */
    public function toXml($writer, $includeNamespaces = true)
    {
        $writer->startElementNS('fs', 'tree', null);
        if ($includeNamespaces) {
            $writer->writeAttributeNs('xmlns', 'fs', null, 'http://familysearch.org/v1/');
        }
        $this->writeXmlContents($writer);
        $writer->endElement();
    }

    /**
     * Writes the contents of this Tree to an XML writer.
     * The startElement is expected to be already provided.
     *
     * @param \XMLWriter $writer The XML writer.
     */
    public function writeXmlContents($writer)
    {
        if ($this->id) {
            $writer->writeElement('id', $this->id);
        }
        if ($this->groupIds) {
            foreach ($this->groupIds as $groupId) {
                $writer->writeElement('groupId', $groupId);
            }
        }
        if ($this->name) {
            $writer->writeElement('name', $this->name);
        }
        if ($this->description) {
            $writer->writeElement('description', $this->description);
        }
        if ($this->startingPersonId) {
            $writer->writeElement('startingPersonId', $this->startingPersonId);
        }
        if ($this->hidden !== null) {
            $writer->writeElement('hidden', $this->hidden ? 'true' : 'false');
        }
        if ($this->isPrivate !== null) {
            $writer->writeElement('private', $this->isPrivate ? 'true' : 'false');
        }
        if ($this->collectionId) {
            $writer->writeElement('collectionId', $this->collectionId);
        }
        if ($this->ownerAccess) {
            $writer->writeElement('ownerAccess', $this->ownerAccess);
        }
        if ($this->groupAccess) {
            $writer->writeElement('groupAccess', $this->groupAccess);
        }
        if ($this->allAccess) {
            $writer->writeElement('allAccess', $this->allAccess);
        }
    }
}
