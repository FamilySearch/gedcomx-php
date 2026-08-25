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
 * A list of vocabulary concepts.
 *
 * @package Gedcomx\Extensions\FamilySearch\Platform\Vocab
 */
class VocabConcepts
{
    /**
     * The list of vocabulary concepts.
     *
     * @var VocabConcept[]
     */
    private $vocabConcepts;

    /**
     * Constructs a VocabConcepts from a (parsed) JSON hash
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
     * Get the list of vocabulary concepts.
     *
     * @return VocabConcept[]
     */
    public function getVocabConcepts()
    {
        return $this->vocabConcepts;
    }

    /**
     * Set the list of vocabulary concepts.
     *
     * @param VocabConcept[] $vocabConcepts
     */
    public function setVocabConcepts($vocabConcepts)
    {
        $this->vocabConcepts = $vocabConcepts;
    }

    /**
     * Returns the associative array for this VocabConcepts
     *
     * @return array
     */
    public function toArray()
    {
        $a = array();
        if ($this->vocabConcepts) {
            $ab = array();
            foreach ($this->vocabConcepts as $i => $x) {
                $ab[$i] = $x->toArray();
            }
            $a['vocabConcepts'] = $ab;
        }
        return $a;
    }

    /**
     * Returns the JSON string for this VocabConcepts
     *
     * @return string
     */
    public function toJson()
    {
        return json_encode($this->toArray());
    }

    /**
     * Initializes this VocabConcepts from an associative array
     *
     * @param array $o
     */
    public function initFromArray($o)
    {
        $this->vocabConcepts = array();
        if (isset($o['vocabConcepts'])) {
            foreach ($o['vocabConcepts'] as $i => $x) {
                $this->vocabConcepts[$i] = new VocabConcept($x);
            }
        }
    }

    /**
     * Initializes this VocabConcepts from an XML reader.
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
     * Sets a known child element of VocabConcepts from an XML reader.
     *
     * @param \XMLReader $xml The reader.
     *
     * @return bool Whether a child element was set.
     */
    protected function setKnownChildElement($xml)
    {
        $happened = false;
        if (($xml->localName == 'vocabConcept') && ($xml->namespaceURI == 'http://familysearch.org/v1/')) {
            if ($this->vocabConcepts == null) {
                $this->vocabConcepts = array();
            }
            $this->vocabConcepts[] = new VocabConcept($xml);
            $happened = true;
        }
        return $happened;
    }

    /**
     * Sets a known attribute of VocabConcepts from an XML reader.
     *
     * @param \XMLReader $xml The reader.
     *
     * @return bool Whether an attribute was set.
     */
    protected function setKnownAttribute($xml)
    {
        // No attributes for VocabConcepts
        return false;
    }

    /**
     * Writes this VocabConcepts to an XML writer.
     *
     * @param \XMLWriter $writer The XML writer.
     * @param bool $includeNamespaces Whether to write out the namespaces in the element.
     */
    public function toXml($writer, $includeNamespaces = true)
    {
        $writer->startElementNS('fs', 'vocabConcepts', null);
        if ($includeNamespaces) {
            $writer->writeAttributeNs('xmlns', 'fs', null, 'http://familysearch.org/v1/');
        }
        $this->writeXmlContents($writer);
        $writer->endElement();
    }

    /**
     * Writes the contents of this VocabConcepts to an XML writer. The startElement is expected to be already provided.
     *
     * @param \XMLWriter $writer The XML writer.
     */
    public function writeXmlContents($writer)
    {
        if ($this->vocabConcepts) {
            foreach ($this->vocabConcepts as $vocabConcept) {
                $writer->startElementNs('fs', 'vocabConcept', null);
                $vocabConcept->writeXmlContents($writer);
                $writer->endElement();
            }
        }
    }
}
