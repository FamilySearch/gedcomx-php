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

namespace Gedcomx\Extensions\FamilySearch\Platform\Users;

use Gedcomx\Common\TextValue;

/**
 * An element representing a text value that may be in a specific language.
 *
 * @package Gedcomx\Extensions\FamilySearch\Platform\Users
 */
class AgentName extends TextValue
{
    /**
     * The type of the name.
     *
     * @var string
     */
    private $type;

    /**
     * Constructs an AgentName.
     *
     * @param string|null $type The type URI.
     * @param string|null $name The name value.
     * @param string|null $lang The language code.
     */
    public function __construct($type = null, $name = null, $lang = null)
    {
        parent::__construct();

        if ($type !== null || $name !== null || $lang !== null) {
            $this->setType($type);
            $this->setLang($lang);
            $this->setValue($name);
        }
    }

    /**
     * Get the type of the name.
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set the type of the name.
     *
     * @param string $type
     * @return $this
     */
    public function setType($type)
    {
        $this->type = $type;
        return $this;
    }

    /**
     * Returns the associative array for this AgentName
     *
     * @return array
     */
    public function toArray()
    {
        $a = parent::toArray();
        if ($this->type) {
            $a["type"] = $this->type;
        }
        return $a;
    }

    /**
     * Initializes this AgentName from an associative array
     *
     * @param array $o
     */
    public function initFromArray($o)
    {
        parent::initFromArray($o);
        if (isset($o['type'])) {
            $this->type = $o["type"];
        }
    }

    /**
     * Sets a known attribute of AgentName from an XML reader.
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

        if (($xml->localName == 'type') && (empty($xml->namespaceURI))) {
            $this->type = $xml->value;
            return true;
        }

        return false;
    }

    /**
     * Writes the contents of this AgentName to an XML writer. The startElement is expected to be already provided.
     *
     * @param \XMLWriter $writer The XML writer.
     */
    public function writeXmlContents($writer)
    {
        if ($this->type) {
            $writer->writeAttribute('type', $this->type);
        }
        parent::writeXmlContents($writer);
    }
}
