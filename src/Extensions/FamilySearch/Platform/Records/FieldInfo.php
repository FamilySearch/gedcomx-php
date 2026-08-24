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

namespace Gedcomx\Extensions\FamilySearch\Platform\Records;

/**
 * The field information.
 *
 * @package Gedcomx\Extensions\FamilySearch\Platform\Records
 */
class FieldInfo
{
    /**
     * The type of the field.
     *
     * @var string
     */
    private $fieldType;

    /**
     * The display label for the field.
     *
     * @var string
     */
    private $displayLabel;

    /**
     * True if the field is standard; false otherwise.
     *
     * @var boolean
     */
    private $standard;

    /**
     * True if the field is editable; false otherwise.
     *
     * @var boolean
     */
    private $editable;

    /**
     * True if the field is displayable; false otherwise.
     *
     * @var boolean
     */
    private $displayable;

    /**
     * The element types for the field.
     *
     * @var string[]
     */
    private $elementTypes;

    /**
     * The URI for the field.
     *
     * @var string
     */
    private $uri;

    /**
     * Constructs a FieldInfo from a (parsed) JSON hash
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
     * Get the type of the field.
     *
     * @return string
     */
    public function getFieldType()
    {
        return $this->fieldType;
    }

    /**
     * Set the type of the field.
     *
     * @param string $fieldType
     */
    public function setFieldType($fieldType)
    {
        $this->fieldType = $fieldType;
    }

    /**
     * Get the display label for the field.
     *
     * @return string
     */
    public function getDisplayLabel()
    {
        return $this->displayLabel;
    }

    /**
     * Set the display label for the field.
     *
     * @param string $displayLabel
     */
    public function setDisplayLabel($displayLabel)
    {
        $this->displayLabel = $displayLabel;
    }

    /**
     * True if the field is standard; false otherwise.
     *
     * @return boolean
     */
    public function isStandard()
    {
        return $this->standard;
    }

    /**
     * True if the field is standard; false otherwise.
     *
     * @param boolean $standard
     */
    public function setStandard($standard)
    {
        $this->standard = $standard;
    }

    /**
     * True if the field is editable; false otherwise.
     *
     * @return boolean
     */
    public function isEditable()
    {
        return $this->editable;
    }

    /**
     * True if the field is editable; false otherwise.
     *
     * @param boolean $editable
     */
    public function setEditable($editable)
    {
        $this->editable = $editable;
    }

    /**
     * True if the field is displayable; false otherwise.
     *
     * @return boolean
     */
    public function isDisplayable()
    {
        return $this->displayable;
    }

    /**
     * True if the field is displayable; false otherwise.
     *
     * @param boolean $displayable
     */
    public function setDisplayable($displayable)
    {
        $this->displayable = $displayable;
    }

    /**
     * The element types for the field.
     *
     * @return string[]
     */
    public function getElementTypes()
    {
        return $this->elementTypes;
    }

    /**
     * The element types for the field.
     *
     * @param string[] $elementTypes
     */
    public function setElementTypes($elementTypes)
    {
        $this->elementTypes = $elementTypes;
    }

    /**
     * The URI for the field.
     *
     * @return string
     */
    public function getUri()
    {
        return $this->uri;
    }

    /**
     * The URI for the field.
     *
     * @param string $uri
     */
    public function setUri($uri)
    {
        $this->uri = $uri;
    }

    /**
     * Returns the associative array for this FieldInfo
     *
     * @return array
     */
    public function toArray()
    {
        $a = array();
        if ($this->fieldType) {
            $a["fieldType"] = $this->fieldType;
        }
        if ($this->displayLabel) {
            $a["displayLabel"] = $this->displayLabel;
        }
        if ($this->standard !== null) {
            $a["standard"] = $this->standard;
        }
        if ($this->editable !== null) {
            $a["editable"] = $this->editable;
        }
        if ($this->displayable !== null) {
            $a["displayable"] = $this->displayable;
        }
        if ($this->elementTypes) {
            $a["elementTypes"] = $this->elementTypes;
        }
        if ($this->uri) {
            $a["uri"] = $this->uri;
        }
        return $a;
    }

    /**
     * Returns the JSON string for this FieldInfo
     *
     * @return string
     */
    public function toJson()
    {
        return json_encode($this->toArray());
    }

    /**
     * Initializes this FieldInfo from an associative array
     *
     * @param array $o
     */
    public function initFromArray($o)
    {
        if (isset($o['fieldType'])) {
            $this->fieldType = $o["fieldType"];
        }
        if (isset($o['displayLabel'])) {
            $this->displayLabel = $o["displayLabel"];
        }
        if (isset($o['standard'])) {
            $this->standard = $o["standard"];
        }
        if (isset($o['editable'])) {
            $this->editable = $o["editable"];
        }
        if (isset($o['displayable'])) {
            $this->displayable = $o["displayable"];
        }
        if (isset($o['elementTypes'])) {
            $this->elementTypes = $o["elementTypes"];
        }
        if (isset($o['uri'])) {
            $this->uri = $o["uri"];
        }
    }

    /**
     * Initializes this FieldInfo from an XML reader.
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
     * Sets a known child element of FieldInfo from an XML reader.
     *
     * @param \XMLReader $xml The reader.
     *
     * @return bool Whether a child element was set.
     */
    protected function setKnownChildElement($xml)
    {
        $happened = false;
        if (($xml->localName == 'fieldType') && ($xml->namespaceURI == 'http://familysearch.org/v1/')) {
            $this->fieldType = $xml->readString();
            $happened = true;
        }
        else if (($xml->localName == 'displayLabel') && ($xml->namespaceURI == 'http://familysearch.org/v1/')) {
            $this->displayLabel = $xml->readString();
            $happened = true;
        }
        else if (($xml->localName == 'standard') && ($xml->namespaceURI == 'http://familysearch.org/v1/')) {
            $this->standard = filter_var($xml->readString(), FILTER_VALIDATE_BOOLEAN);
            $happened = true;
        }
        else if (($xml->localName == 'editable') && ($xml->namespaceURI == 'http://familysearch.org/v1/')) {
            $this->editable = filter_var($xml->readString(), FILTER_VALIDATE_BOOLEAN);
            $happened = true;
        }
        else if (($xml->localName == 'displayable') && ($xml->namespaceURI == 'http://familysearch.org/v1/')) {
            $this->displayable = filter_var($xml->readString(), FILTER_VALIDATE_BOOLEAN);
            $happened = true;
        }
        else if (($xml->localName == 'elementTypes') && ($xml->namespaceURI == 'http://familysearch.org/v1/')) {
            if ($this->elementTypes == null) {
                $this->elementTypes = array();
            }
            $this->elementTypes[] = $xml->readString();
            $happened = true;
        }
        return $happened;
    }

    /**
     * Sets a known attribute of FieldInfo from an XML reader.
     *
     * @param \XMLReader $xml The reader.
     *
     * @return bool Whether an attribute was set.
     */
    protected function setKnownAttribute($xml)
    {
        if (($xml->localName == 'uri') && (empty($xml->namespaceURI))) {
            $this->uri = $xml->value;
            return true;
        }

        return false;
    }

    /**
     * Writes this FieldInfo to an XML writer.
     *
     * @param \XMLWriter $writer The XML writer.
     * @param bool $includeNamespaces Whether to write out the namespaces in the element.
     */
    public function toXml($writer, $includeNamespaces = true)
    {
        $writer->startElementNS('fs', 'fieldInfo', null);
        if ($includeNamespaces) {
            $writer->writeAttributeNs('xmlns', 'fs', null, 'http://familysearch.org/v1/');
        }
        $this->writeXmlContents($writer);
        $writer->endElement();
    }

    /**
     * Writes the contents of this FieldInfo to an XML writer. The startElement is expected to be already provided.
     *
     * @param \XMLWriter $writer The XML writer.
     */
    public function writeXmlContents($writer)
    {
        if ($this->uri) {
            $writer->writeAttribute('uri', $this->uri);
        }
        if ($this->fieldType) {
            $writer->writeElementNs('fs', 'fieldType', null, $this->fieldType);
        }
        if ($this->displayLabel) {
            $writer->writeElementNs('fs', 'displayLabel', null, $this->displayLabel);
        }
        if ($this->standard !== null) {
            $writer->writeElementNs('fs', 'standard', null, $this->standard ? 'true' : 'false');
        }
        if ($this->editable !== null) {
            $writer->writeElementNs('fs', 'editable', null, $this->editable ? 'true' : 'false');
        }
        if ($this->displayable !== null) {
            $writer->writeElementNs('fs', 'displayable', null, $this->displayable ? 'true' : 'false');
        }
        if ($this->elementTypes) {
            foreach ($this->elementTypes as $elementType) {
                $writer->writeElementNs('fs', 'elementTypes', null, $elementType);
            }
        }
    }

    /**
     * Serialize to string for PHP serialization.
     *
     * @return string
     */
    public function serialize()
    {
        return serialize($this->toArray());
    }

    /**
     * Unserialize from string for PHP serialization.
     *
     * @param string $data
     */
    public function unserialize($data)
    {
        $this->initFromArray(unserialize($data));
    }
}
