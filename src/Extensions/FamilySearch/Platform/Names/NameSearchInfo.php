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

namespace Gedcomx\Extensions\FamilySearch\Platform\Names;

/**
 * Information about a Names search result.
 *
 * @package Gedcomx\Extensions\FamilySearch\Platform\Names
 */
class NameSearchInfo
{
    /**
     * The text of the search result.
     *
     * @var string
     */
    private $text;

    /**
     * The name id of the search result.
     *
     * @var string
     */
    private $nameId;

    /**
     * The name part type for the text of the search result.
     *
     * @var string
     */
    private $namePartType;

    /**
     * The weight of the search result.
     *
     * @var integer
     */
    private $weight;

    /**
     * Constructs a NameSearchInfo from a (parsed) JSON hash
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
     * The text of the search result.
     *
     * @return string
     */
    public function getText()
    {
        return $this->text;
    }

    /**
     * The text of the search result.
     *
     * @param string $text
     */
    public function setText($text)
    {
        $this->text = $text;
    }

    /**
     * The name id of the search result.
     *
     * @return string
     */
    public function getNameId()
    {
        return $this->nameId;
    }

    /**
     * The name id of the search result.
     *
     * @param string $nameId
     */
    public function setNameId($nameId)
    {
        $this->nameId = $nameId;
    }

    /**
     * The name part type for the text of the search result.
     *
     * @return string
     */
    public function getNamePartType()
    {
        return $this->namePartType;
    }

    /**
     * The name part type for the text of the search result.
     *
     * @param string $namePartType
     */
    public function setNamePartType($namePartType)
    {
        $this->namePartType = $namePartType;
    }

    /**
     * Get the known name part type enumeration value.
     *
     * @return string|null
     */
    public function getKnownNamePartType()
    {
        if ($this->namePartType) {
            $parts = explode('/', $this->namePartType);
            return end($parts);
        }
        return null;
    }

    /**
     * Set the name part type from a known enumeration value.
     *
     * @param string $knownType One of the NamePartType constants from Gedcomx\Types\NamePartType
     */
    public function setKnownNamePartType($knownType)
    {
        $this->namePartType = $knownType;
    }

    /**
     * The weight of the search result.
     *
     * @return integer
     */
    public function getWeight()
    {
        return $this->weight;
    }

    /**
     * The weight of the search result.
     *
     * @param integer $weight
     */
    public function setWeight($weight)
    {
        $this->weight = $weight;
    }

    /**
     * Returns the associative array for this NameSearchInfo
     *
     * @return array
     */
    public function toArray()
    {
        $a = array();
        if ($this->text) {
            $a["text"] = $this->text;
        }
        if ($this->nameId) {
            $a["nameId"] = $this->nameId;
        }
        if ($this->namePartType) {
            $a["namePartType"] = $this->namePartType;
        }
        if ($this->weight !== null) {
            $a["weight"] = $this->weight;
        }
        return $a;
    }

    /**
     * Returns the JSON string for this NameSearchInfo
     *
     * @return string
     */
    public function toJson()
    {
        return json_encode($this->toArray());
    }

    /**
     * Initializes this NameSearchInfo from an associative array
     *
     * @param array $o
     */
    public function initFromArray($o)
    {
        if (isset($o['text'])) {
            $this->text = $o["text"];
        }
        if (isset($o['nameId'])) {
            $this->nameId = $o["nameId"];
        }
        if (isset($o['namePartType'])) {
            $this->namePartType = $o["namePartType"];
        }
        if (isset($o['weight'])) {
            $this->weight = $o["weight"];
        }
    }

    /**
     * Initializes this NameSearchInfo from an XML reader.
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
     * Sets a known child element of NameSearchInfo from an XML reader.
     *
     * @param \XMLReader $xml The reader.
     *
     * @return bool Whether a child element was set.
     */
    protected function setKnownChildElement($xml)
    {
        $happened = false;
        if (($xml->localName == 'text') && ($xml->namespaceURI == 'http://familysearch.org/v1/')) {
            $this->text = $xml->readString();
            $happened = true;
        }
        else if (($xml->localName == 'nameId') && ($xml->namespaceURI == 'http://familysearch.org/v1/')) {
            $this->nameId = $xml->readString();
            $happened = true;
        }
        else if (($xml->localName == 'weight') && ($xml->namespaceURI == 'http://familysearch.org/v1/')) {
            $this->weight = (int)$xml->readString();
            $happened = true;
        }
        return $happened;
    }

    /**
     * Sets a known attribute of NameSearchInfo from an XML reader.
     *
     * @param \XMLReader $xml The reader.
     *
     * @return bool Whether an attribute was set.
     */
    protected function setKnownAttribute($xml)
    {
        if (($xml->localName == 'namePartType') && (empty($xml->namespaceURI))) {
            $this->namePartType = $xml->value;
            return true;
        }

        return false;
    }

    /**
     * Writes this NameSearchInfo to an XML writer.
     *
     * @param \XMLWriter $writer The XML writer.
     * @param bool $includeNamespaces Whether to write out the namespaces in the element.
     */
    public function toXml($writer, $includeNamespaces = true)
    {
        $writer->startElementNS('fs', 'namesSearchInfo', null);
        if ($includeNamespaces) {
            $writer->writeAttributeNs('xmlns', 'fs', null, 'http://familysearch.org/v1/');
        }
        $this->writeXmlContents($writer);
        $writer->endElement();
    }

    /**
     * Writes the contents of this NameSearchInfo to an XML writer. The startElement is expected to be already provided.
     *
     * @param \XMLWriter $writer The XML writer.
     */
    public function writeXmlContents($writer)
    {
        if ($this->namePartType) {
            $writer->writeAttribute('namePartType', $this->namePartType);
        }
        if ($this->text) {
            $writer->writeElementNs('fs', 'text', null, $this->text);
        }
        if ($this->nameId) {
            $writer->writeElementNs('fs', 'nameId', null, $this->nameId);
        }
        if ($this->weight !== null) {
            $writer->writeElementNs('fs', 'weight', null, $this->weight);
        }
    }
}
