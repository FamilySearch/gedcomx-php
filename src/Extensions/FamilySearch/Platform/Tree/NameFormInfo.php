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
 * Class NameFormInfo
 *
 * Information about a name form, specifically its ordering convention.
 *
 * A name form represents a single representation of a person's name, and may
 * have different orderings depending on cultural conventions. This class
 * provides metadata about how the name parts should be ordered.
 *
 * @package Gedcomx\Extensions\FamilySearch\Platform\Tree
 */
class NameFormInfo
{
    /**
     * The ordering of the name form.
     *
     * Specifies whether this name form uses Eurotypic (Western) ordering
     * where given name comes first, or Sinotypic (Eastern) ordering where
     * surname comes first.
     *
     * @var string
     */
    private $order;

    /**
     * Constructs a NameFormInfo from a (parsed) JSON hash or XML reader.
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
     * The ordering of the name form.
     *
     * @return string The URI representing the name form order (Eurotypic or Sinotypic).
     */
    public function getOrder()
    {
        return $this->order;
    }

    /**
     * The ordering of the name form.
     *
     * @param string $order The URI representing the name form order.
     */
    public function setOrder($order)
    {
        $this->order = $order;
    }

    /**
     * Returns the associative array for this NameFormInfo.
     *
     * @return array
     */
    public function toArray()
    {
        $a = array();
        if ($this->order) {
            $a["order"] = $this->order;
        }
        return $a;
    }

    /**
     * Returns the JSON string for this NameFormInfo.
     *
     * @return string
     */
    public function toJson()
    {
        return json_encode($this->toArray());
    }

    /**
     * Initializes this NameFormInfo from an associative array.
     *
     * @param array $o
     */
    public function initFromArray($o)
    {
        if (isset($o['order'])) {
            $this->order = $o["order"];
        }
    }

    /**
     * Initializes this NameFormInfo from an XML reader.
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
     * Sets a known child element of NameFormInfo from an XML reader.
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
     * Sets a known attribute of NameFormInfo from an XML reader.
     *
     * @param \XMLReader $xml The reader.
     *
     * @return bool Whether an attribute was set.
     */
    protected function setKnownAttribute($xml)
    {
        if (($xml->localName == 'order') && (empty($xml->namespaceURI))) {
            $this->order = $xml->value;
            return true;
        }

        return false;
    }

    /**
     * Writes this NameFormInfo to an XML writer.
     *
     * @param \XMLWriter $writer The XML writer.
     * @param bool $includeNamespaces Whether to write out the namespaces in the element.
     */
    public function toXml($writer, $includeNamespaces = true)
    {
        $writer->startElementNS('fs', 'nameFormInfo', null);
        if ($includeNamespaces) {
            $writer->writeAttributeNs('xmlns', 'fs', null, 'http://familysearch.org/v1/');
        }
        $this->writeXmlContents($writer);
        $writer->endElement();
    }

    /**
     * Writes the contents of this NameFormInfo to an XML writer.
     * The startElement is expected to be already provided.
     *
     * @param \XMLWriter $writer The XML writer.
     */
    public function writeXmlContents($writer)
    {
        if ($this->order) {
            $writer->writeAttribute('order', $this->order);
        }
    }
}
