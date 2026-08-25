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
 * A vocabulary translation.
 *
 * @package Gedcomx\Extensions\FamilySearch\Platform\Vocab
 */
class VocabTranslation extends HypermediaEnabledData
{
    /**
     * The translation of the vocabulary term.
     *
     * @var TextValue
     */
    private $translation;

    /**
     * Constructs a VocabTranslation from a (parsed) JSON hash or with text and language.
     *
     * @param mixed $text_or_array Either text string, an array (JSON), or an XMLReader.
     * @param string|null $lang The language code (only used when first param is string).
     */
    public function __construct($text_or_array = null, $lang = null)
    {
        $this->translation = new TextValue();

        if (is_string($text_or_array) && $lang !== null) {
            // Constructor called with (text, lang)
            $this->translation->setValue($text_or_array);
            $this->translation->setLang($lang);
        }
        else if (is_array($text_or_array)) {
            $this->initFromArray($text_or_array);
        }
        else if ($text_or_array instanceof \XMLReader) {
            $success = true;
            while ($success && $text_or_array->nodeType != \XMLReader::ELEMENT) {
                $success = $text_or_array->read();
            }
            if ($text_or_array->nodeType != \XMLReader::ELEMENT) {
                throw new \Exception("Unable to read XML: no start element found.");
            }

            $this->initFromReader($text_or_array);
        }
    }

    /**
     * Get the language of the vocabulary translation.
     *
     * @return string
     */
    public function getLang()
    {
        return $this->translation->getLang();
    }

    /**
     * Set the language for the vocabulary translation.
     *
     * @param string $lang
     */
    public function setLang($lang)
    {
        $this->translation->setLang($lang);
    }

    /**
     * Get the text for this vocabulary translation.
     *
     * @return string
     */
    public function getText()
    {
        return $this->translation->getValue();
    }

    /**
     * Set the text for this vocabulary translation.
     *
     * @param string $text
     */
    public function setText($text)
    {
        $this->translation->setValue($text);
    }

    /**
     * Accept a visitor.
     *
     * @param \Gedcomx\Extensions\FamilySearch\Rs\Client\FamilySearchPlatformModelVisitor $visitor
     */
    public function accept($visitor)
    {
        $visitor->visitVocabTranslation($this);
    }

    /**
     * Embed another VocabTranslation into this one.
     *
     * @param \Gedcomx\Links\HypermediaEnabledData $vocabTranslation
     */
    public function embed(\Gedcomx\Links\HypermediaEnabledData $vocabTranslation)
    {
        parent::embed($vocabTranslation);
    }

    /**
     * Returns the associative array for this VocabTranslation
     *
     * @return array
     */
    public function toArray()
    {
        $a = parent::toArray();
        if ($this->translation) {
            $a["translation"] = $this->translation->toArray();
        }
        return $a;
    }

    /**
     * Initializes this VocabTranslation from an associative array
     *
     * @param array $o
     */
    public function initFromArray($o)
    {
        if (isset($o['translation'])) {
            $this->translation = new TextValue($o["translation"]);
        }
        parent::initFromArray($o);
    }

    /**
     * Sets a known child element of VocabTranslation from an XML reader.
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
        else if (($xml->localName == 'translation') && ($xml->namespaceURI == 'http://familysearch.org/v1/')) {
            $this->translation = new TextValue($xml);
            $happened = true;
        }
        return $happened;
    }

    /**
     * Sets a known attribute of VocabTranslation from an XML reader.
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
     * Writes this VocabTranslation to an XML writer.
     *
     * @param \XMLWriter $writer The XML writer.
     * @param bool $includeNamespaces Whether to write out the namespaces in the element.
     */
    public function toXml($writer, $includeNamespaces = true)
    {
        $writer->startElementNS('fs', 'vocabTranslation', null);
        if ($includeNamespaces) {
            $writer->writeAttributeNs('xmlns', 'fs', null, 'http://familysearch.org/v1/');
        }
        $this->writeXmlContents($writer);
        $writer->endElement();
    }

    /**
     * Writes the contents of this VocabTranslation to an XML writer. The startElement is expected to be already provided.
     *
     * @param \XMLWriter $writer The XML writer.
     */
    public function writeXmlContents($writer)
    {
        parent::writeXmlContents($writer);
        if ($this->translation) {
            $writer->startElementNs('fs', 'translation', null);
            $this->translation->writeXmlContents($writer);
            $writer->endElement();
        }
    }
}
