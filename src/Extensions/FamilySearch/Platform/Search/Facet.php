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

namespace Gedcomx\Extensions\FamilySearch\Platform\Search;

/**
 * A facet.
 *
 * Represents a search facet with hierarchical structure - facets can contain nested facets.
 *
 * @package Gedcomx\Extensions\FamilySearch\Platform\Search
 */
class Facet
{
    /**
     * The localized name of this facet.
     *
     * @var string
     */
    private $displayName;

    /**
     * The localized count of this facet.
     *
     * @var string
     */
    private $displayCount;

    /**
     * The API parameters used to filter and count at this facet level.
     *
     * @var string
     */
    private $params;

    /**
     * The numeric count of this facet.
     *
     * @var integer
     */
    private $count;

    /**
     * The facets nested inside this facet (recursive structure).
     *
     * @var Facet[]
     */
    private $facets;

    /**
     * Constructs a Facet from a (parsed) JSON hash
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
     * Get the localized name of this facet.
     *
     * @return string
     */
    public function getDisplayName()
    {
        return $this->displayName;
    }

    /**
     * Set the localized name of this facet.
     *
     * @param string $displayName
     */
    public function setDisplayName($displayName)
    {
        $this->displayName = $displayName;
    }

    /**
     * Get the localized count of this facet.
     *
     * @return string
     */
    public function getDisplayCount()
    {
        return $this->displayCount;
    }

    /**
     * Set the localized count of this facet.
     *
     * @param string $displayCount
     */
    public function setDisplayCount($displayCount)
    {
        $this->displayCount = $displayCount;
    }

    /**
     * Get the API parameters used to filter at this facet level.
     *
     * @return string
     */
    public function getParams()
    {
        return $this->params;
    }

    /**
     * Set the API parameters used to filter at this facet level.
     *
     * @param string $params
     */
    public function setParams($params)
    {
        $this->params = $params;
    }

    /**
     * Get the numeric count of this facet.
     *
     * @return integer
     */
    public function getCount()
    {
        return $this->count;
    }

    /**
     * Set the numeric count of this facet.
     *
     * @param integer $count
     */
    public function setCount($count)
    {
        $this->count = $count;
    }

    /**
     * Get the facets nested inside this facet.
     *
     * @return Facet[]
     */
    public function getFacets()
    {
        return $this->facets;
    }

    /**
     * Set the facets nested inside this facet.
     *
     * @param Facet[] $facets
     */
    public function setFacets($facets)
    {
        $this->facets = $facets;
    }

    /**
     * Create an iterator for the facets nested inside this facet.
     *
     * Returns an iterator that can be used in foreach loops. If no facets exist,
     * returns an empty array iterator.
     *
     * @return \ArrayIterator
     */
    public function facets()
    {
        if ($this->facets === null) {
            return new \ArrayIterator(array());
        }
        return new \ArrayIterator($this->facets);
    }

    /**
     * Returns the associative array for this Facet
     *
     * @return array
     */
    public function toArray()
    {
        $a = array();
        if ($this->displayName) {
            $a["displayName"] = $this->displayName;
        }
        if ($this->displayCount) {
            $a["displayCount"] = $this->displayCount;
        }
        if ($this->params) {
            $a["params"] = $this->params;
        }
        if ($this->count !== null) {
            $a["count"] = $this->count;
        }
        if ($this->facets) {
            $ab = array();
            foreach ($this->facets as $i => $x) {
                $ab[$i] = $x->toArray();
            }
            $a['facets'] = $ab;
        }
        return $a;
    }

    /**
     * Returns the JSON string for this Facet
     *
     * @return string
     */
    public function toJson()
    {
        return json_encode($this->toArray());
    }

    /**
     * Initializes this Facet from an associative array
     *
     * @param array $o
     */
    public function initFromArray($o)
    {
        if (isset($o['displayName'])) {
            $this->displayName = $o["displayName"];
        }
        if (isset($o['displayCount'])) {
            $this->displayCount = $o["displayCount"];
        }
        if (isset($o['params'])) {
            $this->params = $o["params"];
        }
        if (isset($o['count'])) {
            $this->count = $o["count"];
        }
        $this->facets = array();
        if (isset($o['facets'])) {
            foreach ($o['facets'] as $i => $x) {
                $this->facets[$i] = new Facet($x);
            }
        }
    }

    /**
     * Initializes this Facet from an XML reader.
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
     * Sets a known child element of Facet from an XML reader.
     *
     * @param \XMLReader $xml The reader.
     *
     * @return bool Whether a child element was set.
     */
    protected function setKnownChildElement($xml)
    {
        $happened = false;
        if (($xml->localName == 'displayName') && ($xml->namespaceURI == 'http://familysearch.org/v1/')) {
            $this->displayName = $xml->readString();
            $happened = true;
        }
        else if (($xml->localName == 'displayCount') && ($xml->namespaceURI == 'http://familysearch.org/v1/')) {
            $this->displayCount = $xml->readString();
            $happened = true;
        }
        else if (($xml->localName == 'params') && ($xml->namespaceURI == 'http://familysearch.org/v1/')) {
            $this->params = $xml->readString();
            $happened = true;
        }
        else if (($xml->localName == 'count') && ($xml->namespaceURI == 'http://familysearch.org/v1/')) {
            $this->count = (int)$xml->readString();
            $happened = true;
        }
        else if (($xml->localName == 'facet') && ($xml->namespaceURI == 'http://familysearch.org/v1/')) {
            if ($this->facets == null) {
                $this->facets = array();
            }
            $this->facets[] = new Facet($xml);
            $happened = true;
        }
        return $happened;
    }

    /**
     * Sets a known attribute of Facet from an XML reader.
     *
     * @param \XMLReader $xml The reader.
     *
     * @return bool Whether an attribute was set.
     */
    protected function setKnownAttribute($xml)
    {
        // No attributes for Facet
        return false;
    }

    /**
     * Writes this Facet to an XML writer.
     *
     * @param \XMLWriter $writer The XML writer.
     * @param bool $includeNamespaces Whether to write out the namespaces in the element.
     */
    public function toXml($writer, $includeNamespaces = true)
    {
        $writer->startElementNS('fs', 'facet', null);
        if ($includeNamespaces) {
            $writer->writeAttributeNs('xmlns', 'fs', null, 'http://familysearch.org/v1/');
        }
        $this->writeXmlContents($writer);
        $writer->endElement();
    }

    /**
     * Writes the contents of this Facet to an XML writer. The startElement is expected to be already provided.
     *
     * @param \XMLWriter $writer The XML writer.
     */
    public function writeXmlContents($writer)
    {
        if ($this->displayName) {
            $writer->writeElementNs('fs', 'displayName', null, $this->displayName);
        }
        if ($this->displayCount) {
            $writer->writeElementNs('fs', 'displayCount', null, $this->displayCount);
        }
        if ($this->params) {
            $writer->writeElementNs('fs', 'params', null, $this->params);
        }
        if ($this->count !== null) {
            $writer->writeElementNs('fs', 'count', null, $this->count);
        }
        if ($this->facets) {
            foreach ($this->facets as $facet) {
                $writer->startElementNs('fs', 'facet', null);
                $facet->writeXmlContents($writer);
                $writer->endElement();
            }
        }
    }
}
