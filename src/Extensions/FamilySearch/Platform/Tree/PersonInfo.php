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
 * Class PersonInfo
 *
 * Extra information about a person in the FamilySearch Family Tree.
 *
 * This class provides metadata about a person's permissions and visibility
 * settings, including whether the current user can edit the person and what
 * access restrictions are in place.
 *
 * @package Gedcomx\Extensions\FamilySearch\Platform\Tree
 */
class PersonInfo
{
    /**
     * True if the person is editable by the current user; false otherwise.
     *
     * This indicates whether the currently authenticated user has permission
     * to make changes to this person's information in the Family Tree.
     *
     * @var bool
     */
    private $canUserEdit = false;

    /**
     * True if the person is visible to all sessions authenticated from any client; false otherwise.
     *
     * This controls the broadest level of visibility. When false, the person
     * may have restricted access based on who is viewing or from what application.
     *
     * @var bool
     */
    private $visibleToAll = true;

    /**
     * True if the person is only visible to sessions authenticated from a FamilySearch client; false otherwise.
     *
     * This provides a middle tier of visibility restriction, allowing access only
     * through official FamilySearch applications while still restricting third-party access.
     *
     * @var bool
     */
    private $visibleToAllWhenUsingFamilySearchApps = true;

    /**
     * The tree id for this person.
     *
     * Note: This attribute is prototype only and may be removed or changed at any time.
     * Used to identify which tree (in multi-tree scenarios) this person belongs to.
     *
     * @var string
     */
    private $treeId;

    /**
     * Constructs a PersonInfo from a (parsed) JSON hash or XML reader.
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
     * True if the person is editable by the current user; false otherwise.
     *
     * @return bool
     */
    public function getCanUserEdit()
    {
        return $this->canUserEdit;
    }

    /**
     * True if the person is editable by the current user; false otherwise.
     *
     * @param bool $canUserEdit
     */
    public function setCanUserEdit($canUserEdit)
    {
        $this->canUserEdit = $canUserEdit;
    }

    /**
     * True if the person is visible to all sessions authenticated from any client; false otherwise.
     *
     * @return bool
     */
    public function getVisibleToAll()
    {
        return $this->visibleToAll;
    }

    /**
     * True if the person is visible to all sessions authenticated from any client; false otherwise.
     *
     * @param bool $visibleToAll
     */
    public function setVisibleToAll($visibleToAll)
    {
        $this->visibleToAll = $visibleToAll;
    }

    /**
     * True if the person is only visible to sessions authenticated from a FamilySearch client; false otherwise.
     *
     * @return bool
     */
    public function getVisibleToAllWhenUsingFamilySearchApps()
    {
        return $this->visibleToAllWhenUsingFamilySearchApps;
    }

    /**
     * True if the person is only visible to sessions authenticated from a FamilySearch client; false otherwise.
     *
     * @param bool $visibleToAllWhenUsingFamilySearchApps
     */
    public function setVisibleToAllWhenUsingFamilySearchApps($visibleToAllWhenUsingFamilySearchApps)
    {
        $this->visibleToAllWhenUsingFamilySearchApps = $visibleToAllWhenUsingFamilySearchApps;
    }

    /**
     * The tree id for this person.
     * Note: This attribute is prototype only and may be removed or changed at any time.
     *
     * @return string
     */
    public function getTreeId()
    {
        return $this->treeId;
    }

    /**
     * The tree id for this person.
     * Note: This attribute is prototype only and may be removed or changed at any time.
     *
     * @param string $treeId
     */
    public function setTreeId($treeId)
    {
        $this->treeId = $treeId;
    }

    /**
     * Returns the associative array for this PersonInfo.
     *
     * @return array
     */
    public function toArray()
    {
        $a = array();
        if ($this->canUserEdit !== null) {
            $a["canUserEdit"] = $this->canUserEdit;
        }
        if ($this->visibleToAll !== null) {
            $a["visibleToAll"] = $this->visibleToAll;
        }
        if ($this->visibleToAllWhenUsingFamilySearchApps !== null) {
            $a["visibleToAllWhenUsingFamilySearchApps"] = $this->visibleToAllWhenUsingFamilySearchApps;
        }
        if ($this->treeId) {
            $a["treeId"] = $this->treeId;
        }
        return $a;
    }

    /**
     * Returns the JSON string for this PersonInfo.
     *
     * @return string
     */
    public function toJson()
    {
        return json_encode($this->toArray());
    }

    /**
     * Initializes this PersonInfo from an associative array.
     *
     * @param array $o
     */
    public function initFromArray($o)
    {
        if (isset($o['canUserEdit'])) {
            $this->canUserEdit = $o["canUserEdit"];
        }
        if (isset($o['visibleToAll'])) {
            $this->visibleToAll = $o["visibleToAll"];
        }
        if (isset($o['visibleToAllWhenUsingFamilySearchApps'])) {
            $this->visibleToAllWhenUsingFamilySearchApps = $o["visibleToAllWhenUsingFamilySearchApps"];
        }
        if (isset($o['treeId'])) {
            $this->treeId = $o["treeId"];
        }
    }

    /**
     * Initializes this PersonInfo from an XML reader.
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
     * Sets a known child element of PersonInfo from an XML reader.
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
     * Sets a known attribute of PersonInfo from an XML reader.
     *
     * @param \XMLReader $xml The reader.
     *
     * @return bool Whether an attribute was set.
     */
    protected function setKnownAttribute($xml)
    {
        if (($xml->localName == 'canUserEdit') && (empty($xml->namespaceURI))) {
            $this->canUserEdit = (strtolower($xml->value) === 'true' || $xml->value === '1');
            return true;
        }
        if (($xml->localName == 'visibleToAll') && (empty($xml->namespaceURI))) {
            $this->visibleToAll = (strtolower($xml->value) === 'true' || $xml->value === '1');
            return true;
        }
        if (($xml->localName == 'visibleToAllWhenUsingFamilySearchApps') && (empty($xml->namespaceURI))) {
            $this->visibleToAllWhenUsingFamilySearchApps = (strtolower($xml->value) === 'true' || $xml->value === '1');
            return true;
        }
        if (($xml->localName == 'treeId') && (empty($xml->namespaceURI))) {
            $this->treeId = $xml->value;
            return true;
        }

        return false;
    }

    /**
     * Writes this PersonInfo to an XML writer.
     *
     * @param \XMLWriter $writer The XML writer.
     * @param bool $includeNamespaces Whether to write out the namespaces in the element.
     */
    public function toXml($writer, $includeNamespaces = true)
    {
        $writer->startElementNS('fs', 'personInfo', null);
        if ($includeNamespaces) {
            $writer->writeAttributeNs('xmlns', 'fs', null, 'http://familysearch.org/v1/');
        }
        $this->writeXmlContents($writer);
        $writer->endElement();
    }

    /**
     * Writes the contents of this PersonInfo to an XML writer.
     * The startElement is expected to be already provided.
     *
     * @param \XMLWriter $writer The XML writer.
     */
    public function writeXmlContents($writer)
    {
        if ($this->canUserEdit !== null) {
            $writer->writeAttribute('canUserEdit', $this->canUserEdit ? 'true' : 'false');
        }
        if ($this->visibleToAll !== null) {
            $writer->writeAttribute('visibleToAll', $this->visibleToAll ? 'true' : 'false');
        }
        if ($this->visibleToAllWhenUsingFamilySearchApps !== null) {
            $writer->writeAttribute('visibleToAllWhenUsingFamilySearchApps', $this->visibleToAllWhenUsingFamilySearchApps ? 'true' : 'false');
        }
        if ($this->treeId) {
            $writer->writeAttribute('treeId', $this->treeId);
        }
    }
}
