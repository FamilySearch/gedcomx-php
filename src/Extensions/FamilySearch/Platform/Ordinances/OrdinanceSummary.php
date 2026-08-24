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

/**
 * Summary information regarding a user's ordinances.
 *
 * @package Gedcomx\Extensions\FamilySearch\Platform\Ordinances
 */
class OrdinanceSummary
{
    /**
     * The current number of reservations which have not been shared that a user has on their reservation list.
     *
     * @var integer
     */
    private $notSharedReservationCount;

    /**
     * The maximum number of reservations which have not been shared that a user is allowed to have on their reservation list.
     *
     * @var integer
     */
    private $notSharedReservationLimit;

    /**
     * The current number of shared reservations for a user.
     *
     * @var integer
     */
    private $sharedReservationCount;

    /**
     * Constructs an OrdinanceSummary from a (parsed) JSON hash
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
     * The current number of reservations which have not been shared that a user has on their reservation list.
     *
     * @return integer
     */
    public function getNotSharedReservationCount()
    {
        return $this->notSharedReservationCount;
    }

    /**
     * The current number of reservations which have not been shared that a user has on their reservation list.
     *
     * @param integer $notSharedReservationCount
     */
    public function setNotSharedReservationCount($notSharedReservationCount)
    {
        $this->notSharedReservationCount = $notSharedReservationCount;
    }

    /**
     * The maximum number of reservations which have not been shared that a user is allowed to have on their reservation list.
     *
     * @return integer
     */
    public function getNotSharedReservationLimit()
    {
        return $this->notSharedReservationLimit;
    }

    /**
     * The maximum number of reservations which have not been shared that a user is allowed to have on their reservation list.
     *
     * @param integer $notSharedReservationLimit
     */
    public function setNotSharedReservationLimit($notSharedReservationLimit)
    {
        $this->notSharedReservationLimit = $notSharedReservationLimit;
    }

    /**
     * The current number of shared reservations for a user.
     *
     * @return integer
     */
    public function getSharedReservationCount()
    {
        return $this->sharedReservationCount;
    }

    /**
     * The current number of shared reservations for a user.
     *
     * @param integer $sharedReservationCount
     */
    public function setSharedReservationCount($sharedReservationCount)
    {
        $this->sharedReservationCount = $sharedReservationCount;
    }

    /**
     * Returns the associative array for this OrdinanceSummary
     *
     * @return array
     */
    public function toArray()
    {
        $a = array();
        if ($this->notSharedReservationCount !== null) {
            $a["notSharedReservationCount"] = $this->notSharedReservationCount;
        }
        if ($this->notSharedReservationLimit !== null) {
            $a["notSharedReservationLimit"] = $this->notSharedReservationLimit;
        }
        if ($this->sharedReservationCount !== null) {
            $a["sharedReservationCount"] = $this->sharedReservationCount;
        }
        return $a;
    }

    /**
     * Returns the JSON string for this OrdinanceSummary
     *
     * @return string
     */
    public function toJson()
    {
        return json_encode($this->toArray());
    }

    /**
     * Initializes this OrdinanceSummary from an associative array
     *
     * @param array $o
     */
    public function initFromArray($o)
    {
        if (isset($o['notSharedReservationCount'])) {
            $this->notSharedReservationCount = $o["notSharedReservationCount"];
        }
        if (isset($o['notSharedReservationLimit'])) {
            $this->notSharedReservationLimit = $o["notSharedReservationLimit"];
        }
        if (isset($o['sharedReservationCount'])) {
            $this->sharedReservationCount = $o["sharedReservationCount"];
        }
    }

    /**
     * Initializes this OrdinanceSummary from an XML reader.
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
     * Sets a known child element of OrdinanceSummary from an XML reader.
     *
     * @param \XMLReader $xml The reader.
     *
     * @return bool Whether a child element was set.
     */
    protected function setKnownChildElement($xml)
    {
        $happened = false;
        if (($xml->localName == 'notSharedReservationCount') && ($xml->namespaceURI == 'http://familysearch.org/v1/')) {
            $this->notSharedReservationCount = (int)$xml->readString();
            $happened = true;
        }
        else if (($xml->localName == 'notSharedReservationLimit') && ($xml->namespaceURI == 'http://familysearch.org/v1/')) {
            $this->notSharedReservationLimit = (int)$xml->readString();
            $happened = true;
        }
        else if (($xml->localName == 'sharedReservationCount') && ($xml->namespaceURI == 'http://familysearch.org/v1/')) {
            $this->sharedReservationCount = (int)$xml->readString();
            $happened = true;
        }
        return $happened;
    }

    /**
     * Sets a known attribute of OrdinanceSummary from an XML reader.
     *
     * @param \XMLReader $xml The reader.
     *
     * @return bool Whether an attribute was set.
     */
    protected function setKnownAttribute($xml)
    {
        // No attributes for OrdinanceSummary
        return false;
    }

    /**
     * Writes this OrdinanceSummary to an XML writer.
     *
     * @param \XMLWriter $writer The XML writer.
     * @param bool $includeNamespaces Whether to write out the namespaces in the element.
     */
    public function toXml($writer, $includeNamespaces = true)
    {
        $writer->startElementNS('fs', 'ordinanceSummary', null);
        if ($includeNamespaces) {
            $writer->writeAttributeNs('xmlns', 'fs', null, 'http://familysearch.org/v1/');
        }
        $this->writeXmlContents($writer);
        $writer->endElement();
    }

    /**
     * Writes the contents of this OrdinanceSummary to an XML writer. The startElement is expected to be already provided.
     *
     * @param \XMLWriter $writer The XML writer.
     */
    public function writeXmlContents($writer)
    {
        if ($this->notSharedReservationCount !== null) {
            $writer->writeElementNs('fs', 'notSharedReservationCount', null, $this->notSharedReservationCount);
        }
        if ($this->notSharedReservationLimit !== null) {
            $writer->writeElementNs('fs', 'notSharedReservationLimit', null, $this->notSharedReservationLimit);
        }
        if ($this->sharedReservationCount !== null) {
            $writer->writeElementNs('fs', 'sharedReservationCount', null, $this->sharedReservationCount);
        }
    }
}
