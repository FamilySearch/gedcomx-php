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
 * A vocabulary concept.
 *
 * @package Gedcomx\Extensions\FamilySearch\Platform\Vocab
 */
class VocabConcept extends HypermediaEnabledData
{
    /**
     * The vocabulary concept description.
     *
     * @var string
     */
    private $description;

    /**
     * The vocabulary concept note.
     *
     * @var string
     */
    private $note;

    /**
     * The GEDCOM X URI associated with the concept.
     *
     * @var string
     */
    private $gedcomxUri;

    /**
     * The terms associated with the concept.
     *
     * @var VocabTerm[]
     */
    private $vocabTerms;

    /**
     * The attributes associated with the concept.
     *
     * @var VocabConceptAttribute[]
     */
    private $attributes;

    /**
     * The definitions associated with the concept.
     *
     * @var TextValue[]
     */
    private $definitions;

    /**
     * Constructs a VocabConcept from a (parsed) JSON hash
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
     * Get the vocabulary concept description.
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set the vocabulary concept description.
     *
     * @param string $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * Get the vocabulary concept note.
     *
     * @return string
     */
    public function getNote()
    {
        return $this->note;
    }

    /**
     * Set the vocabulary concept note.
     *
     * @param string $note
     */
    public function setNote($note)
    {
        $this->note = $note;
    }

    /**
     * Get the GEDCOM X URI associated with this vocabulary concept.
     *
     * @return string
     */
    public function getGedcomxUri()
    {
        return $this->gedcomxUri;
    }

    /**
     * Set the GEDCOM X URI associated with this vocabulary concept.
     *
     * @param string $gedcomxUri
     */
    public function setGedcomxUri($gedcomxUri)
    {
        $this->gedcomxUri = $gedcomxUri;
    }

    /**
     * Get the vocabulary terms associated with this vocabulary concept.
     *
     * @return VocabTerm[]
     */
    public function getVocabTerms()
    {
        return $this->vocabTerms;
    }

    /**
     * Set the vocabulary terms associated with this vocabulary concept.
     *
     * @param VocabTerm[] $vocabTerms
     */
    public function setVocabTerms($vocabTerms)
    {
        $this->vocabTerms = $vocabTerms;
    }

    /**
     * Add a vocabulary term.
     *
     * @param VocabTerm $vocabTerm
     */
    public function addVocabTerm(VocabTerm $vocabTerm)
    {
        if ($this->vocabTerms === null) {
            $this->vocabTerms = array();
        }
        $this->vocabTerms[] = $vocabTerm;
    }

    /**
     * Get the attributes associated with this vocabulary concept.
     *
     * @return VocabConceptAttribute[]
     */
    public function getAttributes()
    {
        return $this->attributes;
    }

    /**
     * Set the attributes associated with this vocabulary concept.
     *
     * @param VocabConceptAttribute[] $attributes
     */
    public function setAttributes($attributes)
    {
        $this->attributes = $attributes;
    }

    /**
     * Get the definitions associated with this vocabulary concept.
     *
     * @return TextValue[]
     */
    public function getDefinitions()
    {
        return $this->definitions;
    }

    /**
     * Set the definitions associated with this vocabulary concept.
     *
     * @param TextValue[] $definitions
     */
    public function setDefinitions($definitions)
    {
        $this->definitions = $definitions;
    }

    /**
     * Accept a visitor.
     *
     * @param \Gedcomx\Extensions\FamilySearch\Rs\Client\FamilySearchPlatformModelVisitor $visitor
     */
    public function accept($visitor)
    {
        $visitor->visitVocabConcept($this);
    }

    /**
     * Embed another VocabConcept into this one by merging vocabulary terms.
     *
     * This method merges vocabulary terms by ID. If a term with the same ID exists,
     * it calls embed on that term. Otherwise, it adds the new term.
     *
     * @param \Gedcomx\Links\HypermediaEnabledData $vocabConcept
     */
    public function embed(\Gedcomx\Links\HypermediaEnabledData $vocabConcept)
    {
        // Ensure it's a VocabConcept for our specific logic
        if (!($vocabConcept instanceof VocabConcept)) {
            parent::embed($vocabConcept);
            return;
        }

        $vocabTerms = $vocabConcept->getVocabTerms();
        if ($vocabTerms !== null) {
            foreach ($vocabTerms as $vocabTerm) {
                $found = false;
                if ($vocabTerm->getId() !== null) {
                    if ($this->getVocabTerms() !== null) {
                        foreach ($this->getVocabTerms() as $target) {
                            if ($vocabTerm->getId() === $target->getId()) {
                                $target->embed($vocabTerm);
                                $found = true;
                                break;
                            }
                        }
                    }
                }

                if (!$found) {
                    $this->addVocabTerm($vocabTerm);
                }
            }
        }

        parent::embed($vocabConcept);
    }

    /**
     * Returns the associative array for this VocabConcept
     *
     * @return array
     */
    public function toArray()
    {
        $a = parent::toArray();
        if ($this->description) {
            $a["description"] = $this->description;
        }
        if ($this->note) {
            $a["note"] = $this->note;
        }
        if ($this->gedcomxUri) {
            $a["gedcomxUri"] = $this->gedcomxUri;
        }
        if ($this->vocabTerms) {
            $ab = array();
            foreach ($this->vocabTerms as $i => $x) {
                $ab[$i] = $x->toArray();
            }
            $a['vocabTerms'] = $ab;
        }
        if ($this->attributes) {
            $ab = array();
            foreach ($this->attributes as $i => $x) {
                $ab[$i] = $x->toArray();
            }
            $a['attributes'] = $ab;
        }
        if ($this->definitions) {
            $ab = array();
            foreach ($this->definitions as $i => $x) {
                $ab[$i] = $x->toArray();
            }
            $a['definitions'] = $ab;
        }
        return $a;
    }

    /**
     * Initializes this VocabConcept from an associative array
     *
     * @param array $o
     */
    public function initFromArray($o)
    {
        if (isset($o['description'])) {
            $this->description = $o["description"];
        }
        if (isset($o['note'])) {
            $this->note = $o["note"];
        }
        if (isset($o['gedcomxUri'])) {
            $this->gedcomxUri = $o["gedcomxUri"];
        }
        $this->vocabTerms = array();
        if (isset($o['vocabTerms'])) {
            foreach ($o['vocabTerms'] as $i => $x) {
                $this->vocabTerms[$i] = new VocabTerm($x);
            }
        }
        $this->attributes = array();
        if (isset($o['attributes'])) {
            foreach ($o['attributes'] as $i => $x) {
                $this->attributes[$i] = new VocabConceptAttribute($x);
            }
        }
        $this->definitions = array();
        if (isset($o['definitions'])) {
            foreach ($o['definitions'] as $i => $x) {
                $this->definitions[$i] = new TextValue($x);
            }
        }
        parent::initFromArray($o);
    }

    /**
     * Sets a known child element of VocabConcept from an XML reader.
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
        else if (($xml->localName == 'description') && ($xml->namespaceURI == 'http://familysearch.org/v1/')) {
            $this->description = $xml->readString();
            $happened = true;
        }
        else if (($xml->localName == 'note') && ($xml->namespaceURI == 'http://familysearch.org/v1/')) {
            $this->note = $xml->readString();
            $happened = true;
        }
        else if (($xml->localName == 'gedcomxUri') && ($xml->namespaceURI == 'http://familysearch.org/v1/')) {
            $this->gedcomxUri = $xml->readString();
            $happened = true;
        }
        else if (($xml->localName == 'vocabTerm') && ($xml->namespaceURI == 'http://familysearch.org/v1/')) {
            if ($this->vocabTerms == null) {
                $this->vocabTerms = array();
            }
            $this->vocabTerms[] = new VocabTerm($xml);
            $happened = true;
        }
        else if (($xml->localName == 'attribute') && ($xml->namespaceURI == 'http://familysearch.org/v1/')) {
            if ($this->attributes == null) {
                $this->attributes = array();
            }
            $this->attributes[] = new VocabConceptAttribute($xml);
            $happened = true;
        }
        else if (($xml->localName == 'definition') && ($xml->namespaceURI == 'http://familysearch.org/v1/')) {
            if ($this->definitions == null) {
                $this->definitions = array();
            }
            $this->definitions[] = new TextValue($xml);
            $happened = true;
        }
        return $happened;
    }

    /**
     * Sets a known attribute of VocabConcept from an XML reader.
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
     * Writes this VocabConcept to an XML writer.
     *
     * @param \XMLWriter $writer The XML writer.
     * @param bool $includeNamespaces Whether to write out the namespaces in the element.
     */
    public function toXml($writer, $includeNamespaces = true)
    {
        $writer->startElementNS('fs', 'vocabConcept', null);
        if ($includeNamespaces) {
            $writer->writeAttributeNs('xmlns', 'fs', null, 'http://familysearch.org/v1/');
        }
        $this->writeXmlContents($writer);
        $writer->endElement();
    }

    /**
     * Writes the contents of this VocabConcept to an XML writer. The startElement is expected to be already provided.
     *
     * @param \XMLWriter $writer The XML writer.
     */
    public function writeXmlContents($writer)
    {
        parent::writeXmlContents($writer);
        if ($this->description) {
            $writer->writeElementNs('fs', 'description', null, $this->description);
        }
        if ($this->note) {
            $writer->writeElementNs('fs', 'note', null, $this->note);
        }
        if ($this->gedcomxUri) {
            $writer->writeElementNs('fs', 'gedcomxUri', null, $this->gedcomxUri);
        }
        if ($this->vocabTerms) {
            foreach ($this->vocabTerms as $vocabTerm) {
                $writer->startElementNs('fs', 'vocabTerm', null);
                $vocabTerm->writeXmlContents($writer);
                $writer->endElement();
            }
        }
        if ($this->attributes) {
            foreach ($this->attributes as $attribute) {
                $writer->startElementNs('fs', 'attribute', null);
                $attribute->writeXmlContents($writer);
                $writer->endElement();
            }
        }
        if ($this->definitions) {
            foreach ($this->definitions as $definition) {
                $writer->startElementNs('fs', 'definition', null);
                $definition->writeXmlContents($writer);
                $writer->endElement();
            }
        }
    }
}
