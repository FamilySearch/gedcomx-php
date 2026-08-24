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

namespace Gedcomx\Extensions\FamilySearch\Platform\Ordinances;

use Gedcomx\Common\ResourceReference;

/**
 * A participant in an ordinance.
 *
 * @package Gedcomx\Extensions\FamilySearch\Platform\Ordinances
 */
class OrdinanceParticipant
{
    /**
     * The role type for this participant in the ordinance.
     *
     * @var string
     */
    private $roleType;

    /**
     * The sex type for this participant in the ordinance.
     *
     * @var string
     */
    private $sexType;

    /**
     * The participant associated with the ordinance.
     *
     * @var ResourceReference
     */
    private $participant;

    /**
     * The full name of the person, generally in the native name form.
     *
     * @var string
     */
    private $fullName;

    /**
     * Constructs an OrdinanceParticipant from a (parsed) JSON hash
     *
     * @param mixed $o Either an array (JSON) or an XMLReader.
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
     * The role type for this participant in the ordinance.
     *
     * @return string
     */
    public function getRoleType()
    {
        return $this->roleType;
    }

    /**
     * The role type for this participant in the ordinance.
     *
     * @param string $roleType
     */
    public function setRoleType($roleType)
    {
        $this->roleType = $roleType;
    }

    /**
     * Get the known role type enumeration value.
     *
     * @return string|null
     */
    public function getKnownRoleType()
    {
        if ($this->roleType) {
            // Extract the enum value from the URI
            $parts = explode('/', $this->roleType);
            return end($parts);
        }
        return null;
    }

    /**
     * Set the role type from a known enumeration value.
     *
     * @param string $knownType One of the OrdinanceRoleType constants
     */
    public function setKnownRoleType($knownType)
    {
        $this->roleType = $knownType;
    }

    /**
     * The sex type for this participant in the ordinance.
     *
     * @return string
     */
    public function getSexType()
    {
        return $this->sexType;
    }

    /**
     * The sex type for this participant in the ordinance.
     *
     * @param string $sexType
     */
    public function setSexType($sexType)
    {
        $this->sexType = $sexType;
    }

    /**
     * Get the known sex type enumeration value.
     *
     * @return string|null
     */
    public function getKnownSexType()
    {
        if ($this->sexType) {
            // Extract the enum value from the URI
            $parts = explode('/', $this->sexType);
            return end($parts);
        }
        return null;
    }

    /**
     * Set the sex type from a known enumeration value.
     *
     * @param string $knownType One of the OrdinanceSexType constants
     */
    public function setKnownSexType($knownType)
    {
        $this->sexType = $knownType;
    }

    /**
     * The participant associated with the ordinance.
     *
     * @return ResourceReference
     */
    public function getParticipant()
    {
        return $this->participant;
    }

    /**
     * The participant associated with the ordinance.
     *
     * @param ResourceReference $participant
     */
    public function setParticipant($participant)
    {
        $this->participant = $participant;
    }

    /**
     * The full name of the person, generally in the native name form.
     *
     * @return string
     */
    public function getFullName()
    {
        return $this->fullName;
    }

    /**
     * The full name of the person, generally in the native name form.
     *
     * @param string $fullName
     */
    public function setFullName($fullName)
    {
        $this->fullName = $fullName;
    }

    /**
     * Returns the associative array for this OrdinanceParticipant
     *
     * @return array
     */
    public function toArray()
    {
        $a = array();
        if ($this->roleType) {
            $a["roleType"] = $this->roleType;
        }
        if ($this->sexType) {
            $a["sexType"] = $this->sexType;
        }
        if ($this->participant) {
            $a["participant"] = $this->participant->toArray();
        }
        if ($this->fullName) {
            $a["fullName"] = $this->fullName;
        }
        return $a;
    }

    /**
     * Returns the JSON string for this OrdinanceParticipant
     *
     * @return string
     */
    public function toJson()
    {
        return json_encode($this->toArray());
    }

    /**
     * Initializes this OrdinanceParticipant from an associative array
     *
     * @param array $o
     */
    public function initFromArray($o)
    {
        if (isset($o['roleType'])) {
            $this->roleType = $o["roleType"];
        }
        if (isset($o['sexType'])) {
            $this->sexType = $o["sexType"];
        }
        if (isset($o['participant'])) {
            $this->participant = new ResourceReference($o["participant"]);
        }
        if (isset($o['fullName'])) {
            $this->fullName = $o["fullName"];
        }
    }

    /**
     * Initializes this OrdinanceParticipant from an XML reader.
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
     * Sets a known child element of OrdinanceParticipant from an XML reader.
     *
     * @param \XMLReader $xml The reader.
     *
     * @return bool Whether a child element was set.
     */
    protected function setKnownChildElement($xml)
    {
        $happened = false;
        if (($xml->localName == 'participant') && ($xml->namespaceURI == 'http://familysearch.org/v1/')) {
            $child = new ResourceReference($xml);
            $this->participant = $child;
            $happened = true;
        }
        else if (($xml->localName == 'fullName') && ($xml->namespaceURI == 'http://familysearch.org/v1/')) {
            $this->fullName = $xml->readString();
            $happened = true;
        }
        return $happened;
    }

    /**
     * Sets a known attribute of OrdinanceParticipant from an XML reader.
     *
     * @param \XMLReader $xml The reader.
     *
     * @return bool Whether an attribute was set.
     */
    protected function setKnownAttribute($xml)
    {
        if (($xml->localName == 'roleType') && (empty($xml->namespaceURI))) {
            $this->roleType = $xml->value;
            return true;
        }
        if (($xml->localName == 'sexType') && (empty($xml->namespaceURI))) {
            $this->sexType = $xml->value;
            return true;
        }

        return false;
    }

    /**
     * Writes this OrdinanceParticipant to an XML writer.
     *
     * @param \XMLWriter $writer The XML writer.
     * @param bool $includeNamespaces Whether to write out the namespaces in the element.
     */
    public function toXml($writer, $includeNamespaces = true)
    {
        $writer->startElementNS('fs', 'participant', null);
        if ($includeNamespaces) {
            $writer->writeAttributeNs('xmlns', 'fs', null, 'http://familysearch.org/v1/');
        }
        $this->writeXmlContents($writer);
        $writer->endElement();
    }

    /**
     * Writes the contents of this OrdinanceParticipant to an XML writer. The startElement is expected to be already provided.
     *
     * @param \XMLWriter $writer The XML writer.
     */
    public function writeXmlContents($writer)
    {
        if ($this->roleType) {
            $writer->writeAttribute('roleType', $this->roleType);
        }
        if ($this->sexType) {
            $writer->writeAttribute('sexType', $this->sexType);
        }
        if ($this->participant) {
            $writer->startElementNs('fs', 'participant', null);
            $this->participant->writeXmlContents($writer);
            $writer->endElement();
        }
        if ($this->fullName) {
            $writer->writeElementNs('fs', 'fullName', null, $this->fullName);
        }
    }
}
