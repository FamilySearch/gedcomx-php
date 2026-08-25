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
 * The actions that can be performed on an ordinance.
 *
 * @package Gedcomx\Extensions\FamilySearch\Platform\Ordinances
 */
class OrdinanceActions
{
    /**
     * True if this ordinance is reservable; false otherwise.
     *
     * @var boolean
     */
    private $reservable = false;

    /**
     * True if this ordinance is unreservable; false otherwise.
     *
     * @var boolean
     */
    private $unReservable = false;

    /**
     * True if this ordinance is shareable; false otherwise.
     *
     * @var boolean
     */
    private $shareable = false;

    /**
     * True if this ordinance is unshareable; false otherwise.
     *
     * @var boolean
     */
    private $unShareable = false;

    /**
     * True if this ordinance is printable; false otherwise.
     *
     * @var boolean
     */
    private $printable = false;

    /**
     * Constructs an OrdinanceActions from a (parsed) JSON hash
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
     * True if this ordinance is reservable; false otherwise.
     *
     * @return boolean
     */
    public function isReservable()
    {
        return $this->reservable;
    }

    /**
     * True if this ordinance is reservable; false otherwise.
     *
     * @param boolean $reservable
     */
    public function setReservable($reservable)
    {
        $this->reservable = $reservable;
    }

    /**
     * True if this ordinance is unreservable; false otherwise.
     *
     * @return boolean
     */
    public function isUnReservable()
    {
        return $this->unReservable;
    }

    /**
     * True if this ordinance is unreservable; false otherwise.
     *
     * @param boolean $unReservable
     */
    public function setUnReservable($unReservable)
    {
        $this->unReservable = $unReservable;
    }

    /**
     * True if this ordinance is shareable; false otherwise.
     *
     * @return boolean
     */
    public function isShareable()
    {
        return $this->shareable;
    }

    /**
     * True if this ordinance is shareable; false otherwise.
     *
     * @param boolean $shareable
     */
    public function setShareable($shareable)
    {
        $this->shareable = $shareable;
    }

    /**
     * True if this ordinance is unshareable; false otherwise.
     *
     * @return boolean
     */
    public function isUnShareable()
    {
        return $this->unShareable;
    }

    /**
     * True if this ordinance is unshareable; false otherwise.
     *
     * @param boolean $unShareable
     */
    public function setUnShareable($unShareable)
    {
        $this->unShareable = $unShareable;
    }

    /**
     * True if this ordinance is printable; false otherwise.
     *
     * @return boolean
     */
    public function isPrintable()
    {
        return $this->printable;
    }

    /**
     * True if this ordinance is printable; false otherwise.
     *
     * @param boolean $printable
     */
    public function setPrintable($printable)
    {
        $this->printable = $printable;
    }

    /**
     * Returns the associative array for this OrdinanceActions
     *
     * @return array
     */
    public function toArray()
    {
        $a = array();
        if ($this->reservable) {
            $a["reservable"] = $this->reservable;
        }
        if ($this->unReservable) {
            $a["unReservable"] = $this->unReservable;
        }
        if ($this->shareable) {
            $a["shareable"] = $this->shareable;
        }
        if ($this->unShareable) {
            $a["unShareable"] = $this->unShareable;
        }
        if ($this->printable) {
            $a["printable"] = $this->printable;
        }
        return $a;
    }

    /**
     * Returns the JSON string for this OrdinanceActions
     *
     * @return string
     */
    public function toJson()
    {
        return json_encode($this->toArray());
    }

    /**
     * Initializes this OrdinanceActions from an associative array
     *
     * @param array $o
     */
    public function initFromArray($o)
    {
        if (isset($o['reservable'])) {
            $this->reservable = $o["reservable"];
        }
        if (isset($o['unReservable'])) {
            $this->unReservable = $o["unReservable"];
        }
        if (isset($o['shareable'])) {
            $this->shareable = $o["shareable"];
        }
        if (isset($o['unShareable'])) {
            $this->unShareable = $o["unShareable"];
        }
        if (isset($o['printable'])) {
            $this->printable = $o["printable"];
        }
    }

    /**
     * Initializes this OrdinanceActions from an XML reader.
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
     * Sets a known child element of OrdinanceActions from an XML reader.
     *
     * @param \XMLReader $xml The reader.
     *
     * @return bool Whether a child element was set.
     */
    protected function setKnownChildElement($xml)
    {
        // No child elements for OrdinanceActions
        return false;
    }

    /**
     * Sets a known attribute of OrdinanceActions from an XML reader.
     *
     * @param \XMLReader $xml The reader.
     *
     * @return bool Whether an attribute was set.
     */
    protected function setKnownAttribute($xml)
    {
        if (($xml->localName == 'reservable') && (empty($xml->namespaceURI))) {
            $this->reservable = filter_var($xml->value, FILTER_VALIDATE_BOOLEAN);
            return true;
        }
        if (($xml->localName == 'unReservable') && (empty($xml->namespaceURI))) {
            $this->unReservable = filter_var($xml->value, FILTER_VALIDATE_BOOLEAN);
            return true;
        }
        if (($xml->localName == 'shareable') && (empty($xml->namespaceURI))) {
            $this->shareable = filter_var($xml->value, FILTER_VALIDATE_BOOLEAN);
            return true;
        }
        if (($xml->localName == 'unShareable') && (empty($xml->namespaceURI))) {
            $this->unShareable = filter_var($xml->value, FILTER_VALIDATE_BOOLEAN);
            return true;
        }
        if (($xml->localName == 'printable') && (empty($xml->namespaceURI))) {
            $this->printable = filter_var($xml->value, FILTER_VALIDATE_BOOLEAN);
            return true;
        }

        return false;
    }

    /**
     * Writes this OrdinanceActions to an XML writer.
     *
     * @param \XMLWriter $writer The XML writer.
     * @param bool $includeNamespaces Whether to write out the namespaces in the element.
     */
    public function toXml($writer, $includeNamespaces = true)
    {
        $writer->startElementNS('fs', 'actions', null);
        if ($includeNamespaces) {
            $writer->writeAttributeNs('xmlns', 'fs', null, 'http://familysearch.org/v1/');
        }
        $this->writeXmlContents($writer);
        $writer->endElement();
    }

    /**
     * Writes the contents of this OrdinanceActions to an XML writer. The startElement is expected to be already provided.
     *
     * @param \XMLWriter $writer The XML writer.
     */
    public function writeXmlContents($writer)
    {
        if ($this->reservable) {
            $writer->writeAttribute('reservable', $this->reservable ? 'true' : 'false');
        }
        if ($this->unReservable) {
            $writer->writeAttribute('unReservable', $this->unReservable ? 'true' : 'false');
        }
        if ($this->shareable) {
            $writer->writeAttribute('shareable', $this->shareable ? 'true' : 'false');
        }
        if ($this->unShareable) {
            $writer->writeAttribute('unShareable', $this->unShareable ? 'true' : 'false');
        }
        if ($this->printable) {
            $writer->writeAttribute('printable', $this->printable ? 'true' : 'false');
        }
    }
}
