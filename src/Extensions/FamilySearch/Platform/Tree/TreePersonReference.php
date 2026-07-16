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

use Gedcomx\Common\Attribution;
use Gedcomx\Common\ResourceReference;
use Gedcomx\Links\HypermediaEnabledData;

/**
 * Class TreePersonReference
 *
 * A reference to another person in a different FamilySearch tree that may be
 * a representation of the same individual.
 *
 * In FamilySearch, different trees (such as private user trees, group trees,
 * and the public tree) may contain representations of the same person. This
 * class provides a way to link between these representations, facilitating
 * data sharing and duplicate detection across tree boundaries.
 *
 * @package Gedcomx\Extensions\FamilySearch\Platform\Tree
 */
class TreePersonReference extends HypermediaEnabledData
{
    /**
     * The person referenced in another tree.
     *
     * A ResourceReference pointing to the person record in the other tree
     * that represents the same individual.
     *
     * @var ResourceReference
     */
    private $treePerson;

    /**
     * The tree containing the person referenced.
     *
     * A ResourceReference identifying which tree contains the referenced
     * person record.
     *
     * @var ResourceReference
     */
    private $tree;

    /**
     * The attribution metadata for this tree person reference.
     *
     * Information about who created this reference link and when, useful
     * for tracking the history of cross-tree connections.
     *
     * @var Attribution
     */
    private $attribution;

    /**
     * Constructs a TreePersonReference from a (parsed) JSON hash or XML reader.
     *
     * @param mixed $o Either an array (JSON) or an XMLReader.
     *
     * @throws \Exception
     */
    public function __construct($o = null)
    {
        parent::__construct($o);
    }

    /**
     * Gets the reference to the person in the tree.
     *
     * @return ResourceReference
     */
    public function getTreePerson()
    {
        return $this->treePerson;
    }

    /**
     * Sets the reference to the person in the tree.
     *
     * @param ResourceReference $treePerson
     */
    public function setTreePerson($treePerson)
    {
        $this->treePerson = $treePerson;
    }

    /**
     * Gets the reference to the tree containing the person.
     *
     * @return ResourceReference
     */
    public function getTree()
    {
        return $this->tree;
    }

    /**
     * Sets the reference to the tree containing the person.
     *
     * @param ResourceReference $tree
     */
    public function setTree($tree)
    {
        $this->tree = $tree;
    }

    /**
     * Gets the attribution metadata for this tree person reference.
     *
     * @return Attribution
     */
    public function getAttribution()
    {
        return $this->attribution;
    }

    /**
     * Sets the attribution metadata for this tree person reference.
     *
     * @param Attribution $attribution
     */
    public function setAttribution($attribution)
    {
        $this->attribution = $attribution;
    }

    /**
     * Returns the associative array for this TreePersonReference.
     *
     * @return array
     */
    public function toArray()
    {
        $a = parent::toArray();
        if ($this->treePerson) {
            $a["treePerson"] = $this->treePerson->toArray();
        }
        if ($this->tree) {
            $a["tree"] = $this->tree->toArray();
        }
        if ($this->attribution) {
            $a["attribution"] = $this->attribution->toArray();
        }
        return $a;
    }

    /**
     * Returns the JSON string for this TreePersonReference.
     *
     * @return string
     */
    public function toJson()
    {
        return json_encode($this->toArray());
    }

    /**
     * Initializes this TreePersonReference from an associative array.
     *
     * @param array $o
     */
    public function initFromArray($o)
    {
        parent::initFromArray($o);
        if (isset($o['treePerson'])) {
            $this->treePerson = new ResourceReference($o["treePerson"]);
        }
        if (isset($o['tree'])) {
            $this->tree = new ResourceReference($o["tree"]);
        }
        if (isset($o['attribution'])) {
            $this->attribution = new Attribution($o["attribution"]);
        }
    }

    /**
     * Initializes this TreePersonReference from an XML reader.
     *
     * @param \XMLReader $xml The reader to use to initialize this object.
     */
    public function initFromReader($xml)
    {
        parent::initFromReader($xml);
    }

    /**
     * Sets a known child element of TreePersonReference from an XML reader.
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

        if (($xml->localName == 'treePerson') && ($xml->namespaceURI == 'http://familysearch.org/v1/')) {
            $child = new ResourceReference($xml);
            $this->treePerson = $child;
            return true;
        }
        else if (($xml->localName == 'tree') && ($xml->namespaceURI == 'http://familysearch.org/v1/')) {
            $child = new ResourceReference($xml);
            $this->tree = $child;
            return true;
        }
        else if (($xml->localName == 'attribution') && ($xml->namespaceURI == 'http://gedcomx.org/v1/')) {
            $child = new Attribution($xml);
            $this->attribution = $child;
            return true;
        }
        return false;
    }

    /**
     * Sets a known attribute of TreePersonReference from an XML reader.
     *
     * @param \XMLReader $xml The reader.
     *
     * @return bool Whether an attribute was set.
     */
    protected function setKnownAttribute($xml)
    {
        return parent::setKnownAttribute($xml);
    }

    /**
     * Writes this TreePersonReference to an XML writer.
     *
     * @param \XMLWriter $writer The XML writer.
     * @param bool $includeNamespaces Whether to write out the namespaces in the element.
     */
    public function toXml($writer, $includeNamespaces = true)
    {
        $writer->startElementNS('fs', 'tree-person-reference', null);
        if ($includeNamespaces) {
            $writer->writeAttributeNs('xmlns', 'fs', null, 'http://familysearch.org/v1/');
            $writer->writeAttributeNs('xmlns', 'gx', null, 'http://gedcomx.org/v1/');
        }
        $this->writeXmlContents($writer);
        $writer->endElement();
    }

    /**
     * Writes the contents of this TreePersonReference to an XML writer.
     * The startElement is expected to be already provided.
     *
     * @param \XMLWriter $writer The XML writer.
     */
    public function writeXmlContents($writer)
    {
        parent::writeXmlContents($writer);
        if ($this->treePerson) {
            $writer->startElementNS('fs', 'treePerson', null);
            $this->treePerson->writeXmlContents($writer);
            $writer->endElement();
        }
        if ($this->tree) {
            $writer->startElementNS('fs', 'tree', null);
            $this->tree->writeXmlContents($writer);
            $writer->endElement();
        }
        if ($this->attribution) {
            $writer->startElementNS('gx', 'attribution', null);
            $this->attribution->writeXmlContents($writer);
            $writer->endElement();
        }
    }
}
