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

namespace Gedcomx\Extensions\FamilySearch\Platform\Places;

/**
 * Information about a place description.
 *
 * @package Gedcomx\Extensions\FamilySearch\Platform\Places
 */
class PlaceDescriptionInfo
{
    /**
     * The zoom level of this place description.
     *
     * @var integer
     */
    private $zoomLevel;

    /**
     * The type of this related place description. This attribute is only relevant for "related place descriptions."
     *
     * @var string
     */
    private $relatedType;

    /**
     * The sub-type of this related place description. This attribute is only relevant for "related place descriptions."
     *
     * @var string
     */
    private $relatedSubType;

    /**
     * Constructs a PlaceDescriptionInfo from a (parsed) JSON hash
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
     * Get the zoom level for this place description.
     *
     * @return integer
     */
    public function getZoomLevel()
    {
        return $this->zoomLevel;
    }

    /**
     * Set zoom level for this place description.
     *
     * @param integer $zoomLevel
     */
    public function setZoomLevel($zoomLevel)
    {
        $this->zoomLevel = $zoomLevel;
    }

    /**
     * Get the type of this place description. This attribute is only relevant for "related place descriptions."
     *
     * @return string
     */
    public function getRelatedType()
    {
        return $this->relatedType;
    }

    /**
     * Set the type of this related place description. This attribute is only relevant for "related place descriptions."
     *
     * @param string $relatedType
     */
    public function setRelatedType($relatedType)
    {
        $this->relatedType = $relatedType;
    }

    /**
     * Get the sub-type of this related place description. This attribute is only relevant for "related place descriptions."
     *
     * @return string
     */
    public function getRelatedSubType()
    {
        return $this->relatedSubType;
    }

    /**
     * Set the sub-type of this related place description. This attribute is only relevant for "related place descriptions."
     *
     * @param string $relatedSubType
     */
    public function setRelatedSubType($relatedSubType)
    {
        $this->relatedSubType = $relatedSubType;
    }

    /**
     * Returns the associative array for this PlaceDescriptionInfo
     *
     * @return array
     */
    public function toArray()
    {
        $a = array();
        if ($this->zoomLevel !== null) {
            $a["zoomLevel"] = $this->zoomLevel;
        }
        if ($this->relatedType) {
            $a["relatedType"] = $this->relatedType;
        }
        if ($this->relatedSubType) {
            $a["relatedSubType"] = $this->relatedSubType;
        }
        return $a;
    }

    /**
     * Returns the JSON string for this PlaceDescriptionInfo
     *
     * @return string
     */
    public function toJson()
    {
        return json_encode($this->toArray());
    }

    /**
     * Initializes this PlaceDescriptionInfo from an associative array
     *
     * @param array $o
     */
    public function initFromArray($o)
    {
        if (isset($o['zoomLevel'])) {
            $this->zoomLevel = $o["zoomLevel"];
        }
        if (isset($o['relatedType'])) {
            $this->relatedType = $o["relatedType"];
        }
        if (isset($o['relatedSubType'])) {
            $this->relatedSubType = $o["relatedSubType"];
        }
    }

    /**
     * Initializes this PlaceDescriptionInfo from an XML reader.
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
     * Sets a known child element of PlaceDescriptionInfo from an XML reader.
     *
     * @param \XMLReader $xml The reader.
     *
     * @return bool Whether a child element was set.
     */
    protected function setKnownChildElement($xml)
    {
        $happened = false;
        if (($xml->localName == 'zoomLevel') && ($xml->namespaceURI == 'http://familysearch.org/v1/')) {
            $this->zoomLevel = (int)$xml->readString();
            $happened = true;
        }
        else if (($xml->localName == 'relatedType') && ($xml->namespaceURI == 'http://familysearch.org/v1/')) {
            $this->relatedType = $xml->readString();
            $happened = true;
        }
        else if (($xml->localName == 'relatedSubType') && ($xml->namespaceURI == 'http://familysearch.org/v1/')) {
            $this->relatedSubType = $xml->readString();
            $happened = true;
        }
        return $happened;
    }

    /**
     * Sets a known attribute of PlaceDescriptionInfo from an XML reader.
     *
     * @param \XMLReader $xml The reader.
     *
     * @return bool Whether an attribute was set.
     */
    protected function setKnownAttribute($xml)
    {
        // No attributes for PlaceDescriptionInfo
        return false;
    }

    /**
     * Writes this PlaceDescriptionInfo to an XML writer.
     *
     * @param \XMLWriter $writer The XML writer.
     * @param bool $includeNamespaces Whether to write out the namespaces in the element.
     */
    public function toXml($writer, $includeNamespaces = true)
    {
        $writer->startElementNS('fs', 'placeDescriptionInfo', null);
        if ($includeNamespaces) {
            $writer->writeAttributeNs('xmlns', 'fs', null, 'http://familysearch.org/v1/');
        }
        $this->writeXmlContents($writer);
        $writer->endElement();
    }

    /**
     * Writes the contents of this PlaceDescriptionInfo to an XML writer. The startElement is expected to be already provided.
     *
     * @param \XMLWriter $writer The XML writer.
     */
    public function writeXmlContents($writer)
    {
        if ($this->zoomLevel !== null) {
            $writer->writeElementNs('fs', 'zoomLevel', null, $this->zoomLevel);
        }
        if ($this->relatedType) {
            $writer->writeElementNs('fs', 'relatedType', null, $this->relatedType);
        }
        if ($this->relatedSubType) {
            $writer->writeElementNs('fs', 'relatedSubType', null, $this->relatedSubType);
        }
    }
}
