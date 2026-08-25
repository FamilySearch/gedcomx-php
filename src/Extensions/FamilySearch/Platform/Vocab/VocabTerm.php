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

use Gedcomx\Common\TextValue;
use Gedcomx\Links\HypermediaEnabledData;

/**
 * A vocabulary term.
 *
 * @package Gedcomx\Extensions\FamilySearch\Platform\Vocab
 */
class VocabTerm extends HypermediaEnabledData
{
    /**
     * The vocabulary term type URI.
     *
     * @var string
     */
    private $typeUri;

    /**
     * The URI of the vocabulary concept this vocabulary term is associated with.
     *
     * @var string
     */
    private $conceptUri;

    /**
     * The URI of the sublist this vocabulary term is associated with.
     *
     * @var string
     */
    private $sublistUri;

    /**
     * The position of this vocabulary term within its associated sublist.
     *
     * @var integer
     */
    private $sublistPosition;

    /**
     * The values of the vocabulary term.
     *
     * @var TextValue[]
     */
    private $values;

    /**
     * Constructs a VocabTerm from a (parsed) JSON hash
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
     * Get the vocabulary term type URI.
     *
     * @return string
     */
    public function getTypeUri()
    {
        return $this->typeUri;
    }

    /**
     * Set the vocabulary term type URI.
     *
     * @param string $typeUri
     */
    public function setTypeUri($typeUri)
    {
        $this->typeUri = $typeUri;
    }

    /**
     * Get the URI of the vocabulary concept this vocabulary term is associated with.
     *
     * @return string
     */
    public function getVocabConcept()
    {
        return $this->conceptUri;
    }

    /**
     * Set the URI of the vocabulary concept this vocabulary term is associated with.
     *
     * @param string $vocabConceptUri
     */
    public function setVocabConceptUri($vocabConceptUri)
    {
        $this->conceptUri = $vocabConceptUri;
    }

    /**
     * Get the URI of the sublist this vocabulary term is associated with.
     *
     * @return string
     */
    public function getSublistUri()
    {
        return $this->sublistUri;
    }

    /**
     * Set the URI of the sublist this vocabulary term is associated with.
     *
     * @param string $sublistUri
     */
    public function setSublistUri($sublistUri)
    {
        $this->sublistUri = $sublistUri;
    }

    /**
     * Get the position of this vocabulary term within its associated sublist.
     *
     * @return integer
     */
    public function getSublistPosition()
    {
        return $this->sublistPosition;
    }

    /**
     * Set the position of this vocabulary term within its associated sublist.
     *
     * @param integer $sublistPosition
     */
    public function setSublistPosition($sublistPosition)
    {
        $this->sublistPosition = $sublistPosition;
    }

    /**
     * Get the vocabulary term values.
     *
     * @return TextValue[]
     */
    public function getValues()
    {
        return $this->values;
    }

    /**
     * Set the vocabulary term values.
     *
     * @param TextValue[] $values
     */
    public function setValues($values)
    {
        $this->values = $values;
    }

    /**
     * Accept a visitor.
     *
     * @param \Gedcomx\Extensions\FamilySearch\Rs\Client\FamilySearchPlatformModelVisitor $visitor
     */
    public function accept($visitor)
    {
        $visitor->visitVocabTerm($this);
    }

    /**
     * Embed another VocabTerm into this one.
     *
     * @param \Gedcomx\Links\HypermediaEnabledData $vocabTerm
     */
    public function embed(\Gedcomx\Links\HypermediaEnabledData $vocabTerm)
    {
        parent::embed($vocabTerm);
    }

    /**
     * Returns the associative array for this VocabTerm
     *
     * @return array
     */
    public function toArray()
    {
        $a = parent::toArray();
        if ($this->typeUri) {
            $a["typeUri"] = $this->typeUri;
        }
        if ($this->conceptUri) {
            $a["conceptUri"] = $this->conceptUri;
        }
        if ($this->sublistUri) {
            $a["sublistUri"] = $this->sublistUri;
        }
        if ($this->sublistPosition !== null) {
            $a["sublistPosition"] = $this->sublistPosition;
        }
        if ($this->values) {
            $ab = array();
            foreach ($this->values as $i => $x) {
                $ab[$i] = $x->toArray();
            }
            $a['values'] = $ab;
        }
        return $a;
    }

    /**
     * Initializes this VocabTerm from an associative array
     *
     * @param array $o
     */
    public function initFromArray($o)
    {
        if (isset($o['typeUri'])) {
            $this->typeUri = $o["typeUri"];
        }
        if (isset($o['conceptUri'])) {
            $this->conceptUri = $o["conceptUri"];
        }
        if (isset($o['sublistUri'])) {
            $this->sublistUri = $o["sublistUri"];
        }
        if (isset($o['sublistPosition'])) {
            $this->sublistPosition = $o["sublistPosition"];
        }
        $this->values = array();
        if (isset($o['values'])) {
            foreach ($o['values'] as $i => $x) {
                $this->values[$i] = new TextValue($x);
            }
        }
        parent::initFromArray($o);
    }

    /**
     * Sets a known child element of VocabTerm from an XML reader.
     *
     * @param \XMLReader $xml The reader.
     *
     * @return bool Whether a child element was set.
     */
    protected function setKnownChildElement($xml)
    {
        $happened = parent::setKnownChildElement($xml);
        if ($happened) {
            return true;
        }
        else if (($xml->localName == 'typeUri') && ($xml->namespaceURI == 'http://familysearch.org/v1/')) {
            $this->typeUri = $xml->readString();
            $happened = true;
        }
        else if (($xml->localName == 'conceptUri') && ($xml->namespaceURI == 'http://familysearch.org/v1/')) {
            $this->conceptUri = $xml->readString();
            $happened = true;
        }
        else if (($xml->localName == 'sublistUri') && ($xml->namespaceURI == 'http://familysearch.org/v1/')) {
            $this->sublistUri = $xml->readString();
            $happened = true;
        }
        else if (($xml->localName == 'sublistPosition') && ($xml->namespaceURI == 'http://familysearch.org/v1/')) {
            $this->sublistPosition = (int)$xml->readString();
            $happened = true;
        }
        else if (($xml->localName == 'value') && ($xml->namespaceURI == 'http://familysearch.org/v1/')) {
            if ($this->values == null) {
                $this->values = array();
            }
            $this->values[] = new TextValue($xml);
            $happened = true;
        }
        return $happened;
    }

    /**
     * Sets a known attribute of VocabTerm from an XML reader.
     *
     * @param \XMLReader $xml The reader.
     *
     * @return bool Whether an attribute was set.
     */
    protected function setKnownAttribute($xml)
    {
        if (parent::setKnownAttribute($xml)) {
            return true;
        }

        return false;
    }

    /**
     * Writes this VocabTerm to an XML writer.
     *
     * @param \XMLWriter $writer The XML writer.
     * @param bool $includeNamespaces Whether to write out the namespaces in the element.
     */
    public function toXml($writer, $includeNamespaces = true)
    {
        $writer->startElementNS('fs', 'vocabTerm', null);
        if ($includeNamespaces) {
            $writer->writeAttributeNs('xmlns', 'fs', null, 'http://familysearch.org/v1/');
        }
        $this->writeXmlContents($writer);
        $writer->endElement();
    }

    /**
     * Writes the contents of this VocabTerm to an XML writer. The startElement is expected to be already provided.
     *
     * @param \XMLWriter $writer The XML writer.
     */
    public function writeXmlContents($writer)
    {
        parent::writeXmlContents($writer);
        if ($this->typeUri) {
            $writer->writeElementNs('fs', 'typeUri', null, $this->typeUri);
        }
        if ($this->conceptUri) {
            $writer->writeElementNs('fs', 'conceptUri', null, $this->conceptUri);
        }
        if ($this->sublistUri) {
            $writer->writeElementNs('fs', 'sublistUri', null, $this->sublistUri);
        }
        if ($this->sublistPosition !== null) {
            $writer->writeElementNs('fs', 'sublistPosition', null, $this->sublistPosition);
        }
        if ($this->values) {
            foreach ($this->values as $value) {
                $writer->startElementNs('fs', 'value', null);
                $value->writeXmlContents($writer);
                $writer->endElement();
            }
        }
    }
}
