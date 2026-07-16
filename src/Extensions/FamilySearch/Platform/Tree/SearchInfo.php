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
 * Class SearchInfo
 *
 * Information about search results, including hit counts.
 *
 * This class provides metadata about search operations performed in the
 * FamilySearch Family Tree, including the total number of matches found
 * and how many of those are considered "close" matches with high confidence.
 *
 * @package Gedcomx\Extensions\FamilySearch\Platform\Tree
 */
class SearchInfo
{
    /**
     * The total number of search hits.
     *
     * This represents all matches found for the search query, including
     * both high-confidence matches and lower-confidence possibilities.
     *
     * @var int
     */
    private $totalHits;

    /**
     * The number of close hits.
     *
     * This represents the subset of total hits that are considered
     * high-confidence matches. These are matches that are more likely
     * to be correct based on the search algorithm's scoring.
     *
     * @var int
     */
    private $closeHits;

    /**
     * Constructs a SearchInfo from a (parsed) JSON hash or XML reader.
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
     * The total number of search hits.
     *
     * @return int
     */
    public function getTotalHits()
    {
        return $this->totalHits;
    }

    /**
     * The total number of search hits.
     *
     * @param int $totalHits
     */
    public function setTotalHits($totalHits)
    {
        $this->totalHits = $totalHits;
    }

    /**
     * The number of close hits.
     *
     * @return int
     */
    public function getCloseHits()
    {
        return $this->closeHits;
    }

    /**
     * The number of close hits.
     *
     * @param int $closeHits
     */
    public function setCloseHits($closeHits)
    {
        $this->closeHits = $closeHits;
    }

    /**
     * Returns the associative array for this SearchInfo.
     *
     * @return array
     */
    public function toArray()
    {
        $a = array();
        if ($this->totalHits !== null) {
            $a["totalHits"] = $this->totalHits;
        }
        if ($this->closeHits !== null) {
            $a["closeHits"] = $this->closeHits;
        }
        return $a;
    }

    /**
     * Returns the JSON string for this SearchInfo.
     *
     * @return string
     */
    public function toJson()
    {
        return json_encode($this->toArray());
    }

    /**
     * Initializes this SearchInfo from an associative array.
     *
     * @param array $o
     */
    public function initFromArray($o)
    {
        if (isset($o['totalHits'])) {
            $this->totalHits = $o["totalHits"];
        }
        if (isset($o['closeHits'])) {
            $this->closeHits = $o["closeHits"];
        }
    }

    /**
     * Initializes this SearchInfo from an XML reader.
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
     * Sets a known child element of SearchInfo from an XML reader.
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
     * Sets a known attribute of SearchInfo from an XML reader.
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
     * Writes this SearchInfo to an XML writer.
     *
     * @param \XMLWriter $writer The XML writer.
     * @param bool $includeNamespaces Whether to write out the namespaces in the element.
     */
    public function toXml($writer, $includeNamespaces = true)
    {
        $writer->startElementNS('fs', 'searchInfo', null);
        if ($includeNamespaces) {
            $writer->writeAttributeNs('xmlns', 'fs', null, 'http://familysearch.org/v1/');
        }
        $this->writeXmlContents($writer);
        $writer->endElement();
    }

    /**
     * Writes the contents of this SearchInfo to an XML writer.
     * The startElement is expected to be already provided.
     *
     * @param \XMLWriter $writer The XML writer.
     */
    public function writeXmlContents($writer)
    {
        if ($this->totalHits !== null) {
            $writer->writeElement('totalHits', $this->totalHits);
        }
        if ($this->closeHits !== null) {
            $writer->writeElement('closeHits', $this->closeHits);
        }
    }
}
