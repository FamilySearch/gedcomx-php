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

namespace Gedcomx\Extensions\FamilySearch\Platform\Vocab;

/**
 * A vocabulary concept attribute.
 *
 * @package Gedcomx\Extensions\FamilySearch\Platform\Vocab
 */
class VocabConceptAttribute
{
    /**
     * The vocabulary concept attribute id.
     *
     * @var string
     */
    private $id;

    /**
     * The vocabulary concept attribute name.
     *
     * @var string
     */
    private $name;

    /**
     * The vocabulary concept attribute value.
     *
     * @var string
     */
    private $value;

    /**
     * Constructs a VocabConceptAttribute from a (parsed) JSON hash
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
     * Get the vocabulary concept attribute id.
     *
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set the vocabulary concept attribute id.
     *
     * @param string $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * Get the vocabulary concept attribute name.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set the vocabulary concept attribute name.
     *
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * Get the vocabulary concept attribute value.
     *
     * @return string
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Set the vocabulary concept attribute value.
     *
     * @param string $value
     */
    public function setValue($value)
    {
        $this->value = $value;
    }

    /**
     * Returns the associative array for this VocabConceptAttribute
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
        if ($this->value) {
            $a["value"] = $this->value;
        }
        return $a;
    }

    /**
     * Returns the JSON string for this VocabConceptAttribute
     *
     * @return string
     */
    public function toJson()
    {
        return json_encode($this->toArray());
    }

    /**
     * Initializes this VocabConceptAttribute from an associative array
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
        if (isset($o['value'])) {
            $this->value = $o["value"];
        }
    }

    /**
     * Initializes this VocabConceptAttribute from an XML reader.
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
     * Sets a known child element of VocabConceptAttribute from an XML reader.
     *
     * @param \XMLReader $xml The reader.
     *
     * @return bool Whether a child element was set.
     */
    protected function setKnownChildElement($xml)
    {
        $happened = false;
        if (($xml->localName == 'id') && ($xml->namespaceURI == 'http://familysearch.org/v1/')) {
            $this->id = $xml->readString();
            $happened = true;
        }
        else if (($xml->localName == 'name') && ($xml->namespaceURI == 'http://familysearch.org/v1/')) {
            $this->name = $xml->readString();
            $happened = true;
        }
        else if (($xml->localName == 'value') && ($xml->namespaceURI == 'http://familysearch.org/v1/')) {
            $this->value = $xml->readString();
            $happened = true;
        }
        return $happened;
    }

    /**
     * Sets a known attribute of VocabConceptAttribute from an XML reader.
     *
     * @param \XMLReader $xml The reader.
     *
     * @return bool Whether an attribute was set.
     */
    protected function setKnownAttribute($xml)
    {
        // No attributes for VocabConceptAttribute
        return false;
    }

    /**
     * Writes this VocabConceptAttribute to an XML writer.
     *
     * @param \XMLWriter $writer The XML writer.
     * @param bool $includeNamespaces Whether to write out the namespaces in the element.
     */
    public function toXml($writer, $includeNamespaces = true)
    {
        $writer->startElementNS('fs', 'vocabConceptAttribute', null);
        if ($includeNamespaces) {
            $writer->writeAttributeNs('xmlns', 'fs', null, 'http://familysearch.org/v1/');
        }
        $this->writeXmlContents($writer);
        $writer->endElement();
    }

    /**
     * Writes the contents of this VocabConceptAttribute to an XML writer. The startElement is expected to be already provided.
     *
     * @param \XMLWriter $writer The XML writer.
     */
    public function writeXmlContents($writer)
    {
        if ($this->id) {
            $writer->writeElementNs('fs', 'id', null, $this->id);
        }
        if ($this->name) {
            $writer->writeElementNs('fs', 'name', null, $this->name);
        }
        if ($this->value) {
            $writer->writeElementNs('fs', 'value', null, $this->value);
        }
    }
}
