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
 * Class Group
 *
 * A FamilySearch group for collaborative genealogy work.
 *
 * Groups allow multiple users to collaborate on genealogical research,
 * sharing access to specific trees and coordinating their work. Each
 * group has metadata, associated trees, and a list of members.
 *
 * @package Gedcomx\Extensions\FamilySearch\Platform\Tree
 */
class Group
{
    /**
     * The group id.
     *
     * Unique identifier for this group in the FamilySearch system.
     *
     * @var string
     */
    private $id;

    /**
     * The group name.
     *
     * Display name for the group, visible to all members.
     *
     * @var string
     */
    private $name;

    /**
     * The group description.
     *
     * Detailed description of the group's purpose and focus area.
     *
     * @var string
     */
    private $description;

    /**
     * The group code of conduct.
     *
     * Rules and guidelines for group member behavior and collaboration.
     *
     * @var string
     */
    private $codeOfConduct;

    /**
     * The ids of the trees associated with the group.
     *
     * Array of tree identifiers that this group has access to for
     * collaborative work.
     *
     * @var string[]
     */
    private $treeIds;

    /**
     * The members of the group.
     *
     * Array of GroupMember objects representing the users who are
     * part of this collaboration group.
     *
     * @var GroupMember[]
     */
    private $members;

    /**
     * Constructs a Group from a (parsed) JSON hash or XML reader.
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
     * Get the group id.
     *
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set the group id.
     *
     * @param string $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * Get the group name.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set the group name.
     *
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * Get the group description.
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set the group description.
     *
     * @param string $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * Get the group code of conduct.
     *
     * @return string
     */
    public function getCodeOfConduct()
    {
        return $this->codeOfConduct;
    }

    /**
     * Set the group code of conduct.
     *
     * @param string $codeOfConduct
     */
    public function setCodeOfConduct($codeOfConduct)
    {
        $this->codeOfConduct = $codeOfConduct;
    }

    /**
     * Get the ids of the trees associated with the group.
     *
     * @return string[]
     */
    public function getTreeIds()
    {
        return $this->treeIds;
    }

    /**
     * Set the ids of the trees associated with the group.
     *
     * @param string[] $treeIds
     */
    public function setTreeIds($treeIds)
    {
        $this->treeIds = $treeIds;
    }

    /**
     * Get the members of the group.
     *
     * @return GroupMember[]
     */
    public function getMembers()
    {
        return $this->members;
    }

    /**
     * Set the members of the group.
     *
     * @param GroupMember[] $members
     */
    public function setMembers($members)
    {
        $this->members = $members;
    }

    /**
     * Add a member to the group.
     *
     * @param GroupMember $member
     */
    public function addMember($member)
    {
        if ($this->members === null) {
            $this->members = array();
        }
        $this->members[] = $member;
    }

    /**
     * Returns the associative array for this Group.
     *
     * @return array
     */
    public function toArray()
    {
        $a = array();
        if ($this->id) {
            $a["id"] = $this->id;
        }
        if ($this->name) {
            $a["name"] = $this->name;
        }
        if ($this->description) {
            $a["description"] = $this->description;
        }
        if ($this->codeOfConduct) {
            $a["codeOfConduct"] = $this->codeOfConduct;
        }
        if ($this->treeIds) {
            $a["treeIds"] = $this->treeIds;
        }
        if ($this->members) {
            $ab = array();
            foreach ($this->members as $i => $x) {
                $ab[$i] = $x->toArray();
            }
            $a["members"] = $ab;
        }
        return $a;
    }

    /**
     * Returns the JSON string for this Group.
     *
     * @return string
     */
    public function toJson()
    {
        return json_encode($this->toArray());
    }

    /**
     * Initializes this Group from an associative array.
     *
     * @param array $o
     */
    public function initFromArray($o)
    {
        if (isset($o['id'])) {
            $this->id = $o["id"];
        }
        if (isset($o['name'])) {
            $this->name = $o["name"];
        }
        if (isset($o['description'])) {
            $this->description = $o["description"];
        }
        if (isset($o['codeOfConduct'])) {
            $this->codeOfConduct = $o["codeOfConduct"];
        }
        if (isset($o['treeIds'])) {
            $this->treeIds = $o["treeIds"];
        }
        if (isset($o['members'])) {
            $this->members = array();
            foreach ($o['members'] as $i => $x) {
                $this->members[$i] = new GroupMember($x);
            }
        }
    }

    /**
     * Initializes this Group from an XML reader.
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
     * Sets a known child element of Group from an XML reader.
     *
     * @param \XMLReader $xml The reader.
     *
     * @return bool Whether a child element was set.
     */
    protected function setKnownChildElement($xml)
    {
        $happened = false;
        if (($xml->localName == 'member') && ($xml->namespaceURI == 'http://familysearch.org/v1/')) {
            if ($this->members === null) {
                $this->members = array();
            }
            $child = new GroupMember($xml);
            array_push($this->members, $child);
            $happened = true;
        }
        else if (($xml->localName == 'treeId') && ($xml->namespaceURI == 'http://familysearch.org/v1/')) {
            if ($this->treeIds === null) {
                $this->treeIds = array();
            }
            $this->treeIds[] = $xml->readString();
            $happened = true;
        }
        return $happened;
    }

    /**
     * Sets a known attribute of Group from an XML reader.
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
     * Writes this Group to an XML writer.
     *
     * @param \XMLWriter $writer The XML writer.
     * @param bool $includeNamespaces Whether to write out the namespaces in the element.
     */
    public function toXml($writer, $includeNamespaces = true)
    {
        $writer->startElementNS('fs', 'group', null);
        if ($includeNamespaces) {
            $writer->writeAttributeNs('xmlns', 'fs', null, 'http://familysearch.org/v1/');
        }
        $this->writeXmlContents($writer);
        $writer->endElement();
    }

    /**
     * Writes the contents of this Group to an XML writer.
     * The startElement is expected to be already provided.
     *
     * @param \XMLWriter $writer The XML writer.
     */
    public function writeXmlContents($writer)
    {
        if ($this->id) {
            $writer->writeElement('id', $this->id);
        }
        if ($this->name) {
            $writer->writeElement('name', $this->name);
        }
        if ($this->description) {
            $writer->writeElement('description', $this->description);
        }
        if ($this->codeOfConduct) {
            $writer->writeElement('codeOfConduct', $this->codeOfConduct);
        }
        if ($this->treeIds) {
            foreach ($this->treeIds as $treeId) {
                $writer->writeElement('treeId', $treeId);
            }
        }
        if ($this->members) {
            foreach ($this->members as $member) {
                $writer->startElementNS('fs', 'member', null);
                $member->writeXmlContents($writer);
                $writer->endElement();
            }
        }
    }
}
