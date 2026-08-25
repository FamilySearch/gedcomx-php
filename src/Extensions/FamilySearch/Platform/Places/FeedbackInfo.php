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

use Gedcomx\Common\ResourceReference;

/**
 * Information about a place feedback submission.
 *
 * @package Gedcomx\Extensions\FamilySearch\Platform\Places
 */
class FeedbackInfo
{
    /**
     * The resolution of the feedback.
     *
     * @var string
     */
    private $resolution;

    /**
     * The status of the feedback.
     *
     * @var string
     */
    private $status;

    /**
     * A reference to the place that was created based on this feedback, if any.
     *
     * @var ResourceReference
     */
    private $place;

    /**
     * Additional details about the resolution.
     *
     * @var string
     */
    private $details;

    /**
     * Constructs a FeedbackInfo from a (parsed) JSON hash
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
     * The resolution of the feedback.
     *
     * @return string
     */
    public function getResolution()
    {
        return $this->resolution;
    }

    /**
     * The resolution of the feedback.
     *
     * @param string $resolution
     */
    public function setResolution($resolution)
    {
        $this->resolution = $resolution;
    }

    /**
     * The status of the feedback.
     *
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * The status of the feedback.
     *
     * @param string $status
     */
    public function setStatus($status)
    {
        $this->status = $status;
    }

    /**
     * A reference to the place that was created based on this feedback, if any.
     *
     * @return ResourceReference
     */
    public function getPlace()
    {
        return $this->place;
    }

    /**
     * A reference to the place that was created based on this feedback, if any.
     *
     * @param ResourceReference $place
     */
    public function setPlace($place)
    {
        $this->place = $place;
    }

    /**
     * Some additional details about the resolution.
     *
     * @return string
     */
    public function getDetails()
    {
        return $this->details;
    }

    /**
     * Some additional details about the resolution.
     *
     * @param string $details
     */
    public function setDetails($details)
    {
        $this->details = $details;
    }

    /**
     * Returns the associative array for this FeedbackInfo
     *
     * @return array
     */
    public function toArray()
    {
        $a = array();
        if ($this->resolution) {
            $a["resolution"] = $this->resolution;
        }
        if ($this->status) {
            $a["status"] = $this->status;
        }
        if ($this->place) {
            $a["place"] = $this->place->toArray();
        }
        if ($this->details) {
            $a["details"] = $this->details;
        }
        return $a;
    }

    /**
     * Returns the JSON string for this FeedbackInfo
     *
     * @return string
     */
    public function toJson()
    {
        return json_encode($this->toArray());
    }

    /**
     * Initializes this FeedbackInfo from an associative array
     *
     * @param array $o
     */
    public function initFromArray($o)
    {
        if (isset($o['resolution'])) {
            $this->resolution = $o["resolution"];
        }
        if (isset($o['status'])) {
            $this->status = $o["status"];
        }
        if (isset($o['place'])) {
            $this->place = new ResourceReference($o["place"]);
        }
        if (isset($o['details'])) {
            $this->details = $o["details"];
        }
    }

    /**
     * Initializes this FeedbackInfo from an XML reader.
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
     * Sets a known child element of FeedbackInfo from an XML reader.
     *
     * @param \XMLReader $xml The reader.
     *
     * @return bool Whether a child element was set.
     */
    protected function setKnownChildElement($xml)
    {
        $happened = false;
        if (($xml->localName == 'place') && ($xml->namespaceURI == 'http://familysearch.org/v1/')) {
            $child = new ResourceReference($xml);
            $this->place = $child;
            $happened = true;
        }
        else if (($xml->localName == 'details') && ($xml->namespaceURI == 'http://familysearch.org/v1/')) {
            $this->details = $xml->readString();
            $happened = true;
        }
        return $happened;
    }

    /**
     * Sets a known attribute of FeedbackInfo from an XML reader.
     *
     * @param \XMLReader $xml The reader.
     *
     * @return bool Whether an attribute was set.
     */
    protected function setKnownAttribute($xml)
    {
        if (($xml->localName == 'resolution') && (empty($xml->namespaceURI))) {
            $this->resolution = $xml->value;
            return true;
        }
        if (($xml->localName == 'status') && (empty($xml->namespaceURI))) {
            $this->status = $xml->value;
            return true;
        }

        return false;
    }

    /**
     * Writes this FeedbackInfo to an XML writer.
     *
     * @param \XMLWriter $writer The XML writer.
     * @param bool $includeNamespaces Whether to write out the namespaces in the element.
     */
    public function toXml($writer, $includeNamespaces = true)
    {
        $writer->startElementNS('fs', 'feedbackInfo', null);
        if ($includeNamespaces) {
            $writer->writeAttributeNs('xmlns', 'fs', null, 'http://familysearch.org/v1/');
        }
        $this->writeXmlContents($writer);
        $writer->endElement();
    }

    /**
     * Writes the contents of this FeedbackInfo to an XML writer. The startElement is expected to be already provided.
     *
     * @param \XMLWriter $writer The XML writer.
     */
    public function writeXmlContents($writer)
    {
        if ($this->resolution) {
            $writer->writeAttribute('resolution', $this->resolution);
        }
        if ($this->status) {
            $writer->writeAttribute('status', $this->status);
        }
        if ($this->place) {
            $writer->startElementNs('fs', 'place', null);
            $this->place->writeXmlContents($writer);
            $writer->endElement();
        }
        if ($this->details) {
            $writer->writeElementNs('fs', 'details', null, $this->details);
        }
    }
}
