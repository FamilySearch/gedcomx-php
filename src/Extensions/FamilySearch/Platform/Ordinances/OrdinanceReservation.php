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
 * An ordinance reservation.
 *
 * @package Gedcomx\Extensions\FamilySearch\Platform\Ordinances
 */
class OrdinanceReservation
{
    /**
     * The owner associated with the ordinance reservation.
     *
     * @var ResourceReference
     */
    private $owner;

    /**
     * The reservation timestamp for the ordinance reservation.
     *
     * @var \DateTime
     */
    private $reserveDate;

    /**
     * The update timestamp for the ordinance reservation.
     *
     * @var \DateTime
     */
    private $updateDate;

    /**
     * The expiration timestamp for the ordinance reservation.
     *
     * @var \DateTime
     */
    private $expirationDate;

    /**
     * The claim type indicating how this reservation was reserved.
     *
     * @var string
     */
    private $claimType;

    /**
     * The assignee type this reservation is assigned to.
     *
     * @var string
     */
    private $assigneeType;

    /**
     * Constructs an OrdinanceReservation from a (parsed) JSON hash
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
     * The owner associated with the ordinance reservation.
     *
     * @return ResourceReference
     */
    public function getOwner()
    {
        return $this->owner;
    }

    /**
     * The owner associated with the ordinance reservation.
     *
     * @param ResourceReference $owner
     */
    public function setOwner($owner)
    {
        $this->owner = $owner;
    }

    /**
     * The reservation timestamp for the ordinance reservation.
     *
     * @return \DateTime
     */
    public function getReserveDate()
    {
        return $this->reserveDate;
    }

    /**
     * The reservation timestamp for the ordinance reservation.
     *
     * @param \DateTime $reserveDate
     */
    public function setReserveDate($reserveDate)
    {
        $this->reserveDate = $reserveDate;
    }

    /**
     * The update timestamp for the ordinance reservation.
     *
     * @return \DateTime
     */
    public function getUpdateDate()
    {
        return $this->updateDate;
    }

    /**
     * The update timestamp for the ordinance reservation.
     *
     * @param \DateTime $updateDate
     */
    public function setUpdateDate($updateDate)
    {
        $this->updateDate = $updateDate;
    }

    /**
     * The expiration timestamp for the ordinance reservation.
     *
     * @return \DateTime
     */
    public function getExpirationDate()
    {
        return $this->expirationDate;
    }

    /**
     * The expiration timestamp for the ordinance reservation.
     *
     * @param \DateTime $expirationDate
     */
    public function setExpirationDate($expirationDate)
    {
        $this->expirationDate = $expirationDate;
    }

    /**
     * The claim type indicating how this reservation was reserved.
     *
     * @return string
     */
    public function getClaimType()
    {
        return $this->claimType;
    }

    /**
     * The claim type indicating how this reservation was reserved.
     *
     * @param string $claimType
     */
    public function setClaimType($claimType)
    {
        $this->claimType = $claimType;
    }

    /**
     * Get the known claim type enumeration value.
     *
     * @return string|null
     */
    public function getKnownClaimType()
    {
        if ($this->claimType) {
            $parts = explode('/', $this->claimType);
            return end($parts);
        }
        return null;
    }

    /**
     * Set the claim type from a known enumeration value.
     *
     * @param string $knownType One of the OrdinanceReservationClaimType constants
     */
    public function setKnownClaimType($knownType)
    {
        $this->claimType = $knownType;
    }

    /**
     * The assignee type this reservation is assigned to.
     *
     * @return string
     */
    public function getAssigneeType()
    {
        return $this->assigneeType;
    }

    /**
     * The assignee type this reservation is assigned to.
     *
     * @param string $assigneeType
     */
    public function setAssigneeType($assigneeType)
    {
        $this->assigneeType = $assigneeType;
    }

    /**
     * Get the known assignee type enumeration value.
     *
     * @return string|null
     */
    public function getKnownAssigneeType()
    {
        if ($this->assigneeType) {
            $parts = explode('/', $this->assigneeType);
            return end($parts);
        }
        return null;
    }

    /**
     * Set the assignee type from a known enumeration value.
     *
     * @param string $knownType One of the OrdinanceReservationAssigneeType constants
     */
    public function setKnownAssigneeType($knownType)
    {
        $this->assigneeType = $knownType;
    }

    /**
     * Returns the associative array for this OrdinanceReservation
     *
     * @return array
     */
    public function toArray()
    {
        $a = array();
        if ($this->owner) {
            $a["owner"] = $this->owner->toArray();
        }
        if ($this->reserveDate) {
            $a["reserveDate"] = $this->reserveDate->format(\DateTime::ATOM);
        }
        if ($this->updateDate) {
            $a["updateDate"] = $this->updateDate->format(\DateTime::ATOM);
        }
        if ($this->expirationDate) {
            $a["expirationDate"] = $this->expirationDate->format(\DateTime::ATOM);
        }
        if ($this->claimType) {
            $a["claimType"] = $this->claimType;
        }
        if ($this->assigneeType) {
            $a["assigneeType"] = $this->assigneeType;
        }
        return $a;
    }

    /**
     * Returns the JSON string for this OrdinanceReservation
     *
     * @return string
     */
    public function toJson()
    {
        return json_encode($this->toArray());
    }

    /**
     * Initializes this OrdinanceReservation from an associative array
     *
     * @param array $o
     */
    public function initFromArray($o)
    {
        if (isset($o['owner'])) {
            $this->owner = new ResourceReference($o["owner"]);
        }
        if (isset($o['reserveDate'])) {
            $this->reserveDate = new \DateTime($o["reserveDate"]);
        }
        if (isset($o['updateDate'])) {
            $this->updateDate = new \DateTime($o["updateDate"]);
        }
        if (isset($o['expirationDate'])) {
            $this->expirationDate = new \DateTime($o["expirationDate"]);
        }
        if (isset($o['claimType'])) {
            $this->claimType = $o["claimType"];
        }
        if (isset($o['assigneeType'])) {
            $this->assigneeType = $o["assigneeType"];
        }
    }

    /**
     * Initializes this OrdinanceReservation from an XML reader.
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
     * Sets a known child element of OrdinanceReservation from an XML reader.
     *
     * @param \XMLReader $xml The reader.
     *
     * @return bool Whether a child element was set.
     */
    protected function setKnownChildElement($xml)
    {
        $happened = false;
        if (($xml->localName == 'owner') && ($xml->namespaceURI == 'http://familysearch.org/v1/')) {
            $child = new ResourceReference($xml);
            $this->owner = $child;
            $happened = true;
        }
        else if (($xml->localName == 'reserveDate') && ($xml->namespaceURI == 'http://familysearch.org/v1/')) {
            $this->reserveDate = new \DateTime($xml->readString());
            $happened = true;
        }
        else if (($xml->localName == 'updateDate') && ($xml->namespaceURI == 'http://familysearch.org/v1/')) {
            $this->updateDate = new \DateTime($xml->readString());
            $happened = true;
        }
        else if (($xml->localName == 'expirationDate') && ($xml->namespaceURI == 'http://familysearch.org/v1/')) {
            $this->expirationDate = new \DateTime($xml->readString());
            $happened = true;
        }
        return $happened;
    }

    /**
     * Sets a known attribute of OrdinanceReservation from an XML reader.
     *
     * @param \XMLReader $xml The reader.
     *
     * @return bool Whether an attribute was set.
     */
    protected function setKnownAttribute($xml)
    {
        if (($xml->localName == 'claimType') && (empty($xml->namespaceURI))) {
            $this->claimType = $xml->value;
            return true;
        }
        if (($xml->localName == 'assigneeType') && (empty($xml->namespaceURI))) {
            $this->assigneeType = $xml->value;
            return true;
        }

        return false;
    }

    /**
     * Writes this OrdinanceReservation to an XML writer.
     *
     * @param \XMLWriter $writer The XML writer.
     * @param bool $includeNamespaces Whether to write out the namespaces in the element.
     */
    public function toXml($writer, $includeNamespaces = true)
    {
        $writer->startElementNS('fs', 'reservation', null);
        if ($includeNamespaces) {
            $writer->writeAttributeNs('xmlns', 'fs', null, 'http://familysearch.org/v1/');
        }
        $this->writeXmlContents($writer);
        $writer->endElement();
    }

    /**
     * Writes the contents of this OrdinanceReservation to an XML writer. The startElement is expected to be already provided.
     *
     * @param \XMLWriter $writer The XML writer.
     */
    public function writeXmlContents($writer)
    {
        if ($this->claimType) {
            $writer->writeAttribute('claimType', $this->claimType);
        }
        if ($this->assigneeType) {
            $writer->writeAttribute('assigneeType', $this->assigneeType);
        }
        if ($this->owner) {
            $writer->startElementNs('fs', 'owner', null);
            $this->owner->writeXmlContents($writer);
            $writer->endElement();
        }
        if ($this->reserveDate) {
            $writer->writeElementNs('fs', 'reserveDate', null, $this->reserveDate->format(\DateTime::ATOM));
        }
        if ($this->updateDate) {
            $writer->writeElementNs('fs', 'updateDate', null, $this->updateDate->format(\DateTime::ATOM));
        }
        if ($this->expirationDate) {
            $writer->writeElementNs('fs', 'expirationDate', null, $this->expirationDate->format(\DateTime::ATOM));
        }
    }
}
