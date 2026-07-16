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
 * Class GroupMember
 *
 * A member of a FamilySearch group.
 *
 * Groups in FamilySearch allow multiple users to collaborate on genealogical
 * research. Each group has members with specific roles and permissions.
 *
 * @package Gedcomx\Extensions\FamilySearch\Platform\Tree
 */
class GroupMember
{
    /**
     * The id of the group.
     *
     * @var string
     */
    private $groupId;

    /**
     * The cisId of the group member.
     *
     * The CIS (Customer Identity System) ID is the unique identifier for
     * the user in the FamilySearch system.
     *
     * @var string
     */
    private $cisId;

    /**
     * The contact name of the group member.
     *
     * The display name for the member, used in group member lists and
     * collaboration interfaces.
     *
     * @var string
     */
    private $contactName;

    /**
     * The status of the group member.
     *
     * Indicates the member's current state within the group (e.g., active,
     * invited, removed).
     *
     * @var string
     */
    private $status;

    /**
     * Constructs a GroupMember from a (parsed) JSON hash or XML reader.
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
    public function getGroupId()
    {
        return $this->groupId;
    }

    /**
     * Set the group id.
     *
     * @param string $groupId
     */
    public function setGroupId($groupId)
    {
        $this->groupId = $groupId;
    }

    /**
     * Get the cisId of the group member.
     *
     * @return string
     */
    public function getCisId()
    {
        return $this->cisId;
    }

    /**
     * Set the cisId of the group member.
     *
     * @param string $cisId
     */
    public function setCisId($cisId)
    {
        $this->cisId = $cisId;
    }

    /**
     * Get the contact name of the group member.
     *
     * @return string
     */
    public function getContactName()
    {
        return $this->contactName;
    }

    /**
     * Set the contact name of the group member.
     *
     * @param string $contactName
     */
    public function setContactName($contactName)
    {
        $this->contactName = $contactName;
    }

    /**
     * Get the status of the group member.
     *
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set the status of the group member.
     *
     * @param string $status
     */
    public function setStatus($status)
    {
        $this->status = $status;
    }

    /**
     * Returns the associative array for this GroupMember.
     *
     * @return array
     */
    public function toArray()
    {
        $a = array();
        if ($this->groupId) {
            $a["groupId"] = $this->groupId;
        }
        if ($this->cisId) {
            $a["cisId"] = $this->cisId;
        }
        if ($this->contactName) {
            $a["contactName"] = $this->contactName;
        }
        if ($this->status) {
            $a["status"] = $this->status;
        }
        return $a;
    }

    /**
     * Returns the JSON string for this GroupMember.
     *
     * @return string
     */
    public function toJson()
    {
        return json_encode($this->toArray());
    }

    /**
     * Initializes this GroupMember from an associative array.
     *
     * @param array $o
     */
    public function initFromArray($o)
    {
        if (isset($o['groupId'])) {
            $this->groupId = $o["groupId"];
        }
        if (isset($o['cisId'])) {
            $this->cisId = $o["cisId"];
        }
        if (isset($o['contactName'])) {
            $this->contactName = $o["contactName"];
        }
        if (isset($o['status'])) {
            $this->status = $o["status"];
        }
    }

    /**
     * Initializes this GroupMember from an XML reader.
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
     * Sets a known child element of GroupMember from an XML reader.
     *
     * @param \XMLReader $xml The reader.
     *
     * @return bool Whether a child element was set.
     */
    protected function setKnownChildElement($xml)
    {
        return false;
    }

    /**
     * Sets a known attribute of GroupMember from an XML reader.
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
     * Writes this GroupMember to an XML writer.
     *
     * @param \XMLWriter $writer The XML writer.
     * @param bool $includeNamespaces Whether to write out the namespaces in the element.
     */
    public function toXml($writer, $includeNamespaces = true)
    {
        $writer->startElementNS('fs', 'groupMember', null);
        if ($includeNamespaces) {
            $writer->writeAttributeNs('xmlns', 'fs', null, 'http://familysearch.org/v1/');
        }
        $this->writeXmlContents($writer);
        $writer->endElement();
    }

    /**
     * Writes the contents of this GroupMember to an XML writer.
     * The startElement is expected to be already provided.
     *
     * @param \XMLWriter $writer The XML writer.
     */
    public function writeXmlContents($writer)
    {
        if ($this->groupId) {
            $writer->writeElement('groupId', $this->groupId);
        }
        if ($this->cisId) {
            $writer->writeElement('cisId', $this->cisId);
        }
        if ($this->contactName) {
            $writer->writeElement('contactName', $this->contactName);
        }
        if ($this->status) {
            $writer->writeElement('status', $this->status);
        }
    }
}
