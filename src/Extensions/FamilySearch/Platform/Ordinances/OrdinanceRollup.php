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

namespace Gedcomx\Extensions\FamilySearch\Platform\Ordinances;

use Gedcomx\Conclusion\Conclusion;

/**
 * An ordinance rollup conclusion.
 *
 * Ordinance rollup provides a simplified, aggregated view of ordinance status
 * across multiple ordinances.
 *
 * @package Gedcomx\Extensions\FamilySearch\Platform\Ordinances
 */
class OrdinanceRollup extends Conclusion
{
    /**
     * The type of ordinance.
     *
     * @var string
     */
    private $type;

    /**
     * The rollup status of this ordinance.
     *
     * @var string
     */
    private $rollupStatus;

    /**
     * The type of ordinance.
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * The type of ordinance.
     *
     * @param string $type
     */
    public function setType($type)
    {
        $this->type = $type;
    }

    /**
     * Get the known ordinance type enumeration value.
     *
     * @return string|null
     */
    public function getKnownType()
    {
        if ($this->type) {
            $parts = explode('/', $this->type);
            return end($parts);
        }
        return null;
    }

    /**
     * Set the ordinance type from a known enumeration value.
     *
     * @param string $knownType One of the OrdinanceType constants
     */
    public function setKnownType($knownType)
    {
        $this->type = $knownType;
    }

    /**
     * The rollup status of this ordinance.
     *
     * @return string
     */
    public function getRollupStatus()
    {
        return $this->rollupStatus;
    }

    /**
     * The rollup status of this ordinance.
     *
     * @param string $rollupStatus
     */
    public function setRollupStatus($rollupStatus)
    {
        $this->rollupStatus = $rollupStatus;
    }

    /**
     * Get the known rollup status enumeration value.
     *
     * @return string|null
     */
    public function getKnownRollupStatus()
    {
        if ($this->rollupStatus) {
            $parts = explode('/', $this->rollupStatus);
            return end($parts);
        }
        return null;
    }

    /**
     * Set the rollup status from a known enumeration value.
     *
     * @param string $knownStatus One of the OrdinanceRollupStatus constants
     */
    public function setKnownRollupStatus($knownStatus)
    {
        $this->rollupStatus = $knownStatus;
    }

    /**
     * Returns the associative array for this OrdinanceRollup
     *
     * @return array
     */
    public function toArray()
    {
        $a = parent::toArray();
        if ($this->type) {
            $a["type"] = $this->type;
        }
        if ($this->rollupStatus) {
            $a["rollupStatus"] = $this->rollupStatus;
        }
        return $a;
    }

    /**
     * Initializes this OrdinanceRollup from an associative array
     *
     * @param array $o
     */
    public function initFromArray(array $o)
    {
        if (isset($o['type'])) {
            $this->type = $o["type"];
            unset($o['type']);
        }
        if (isset($o['rollupStatus'])) {
            $this->rollupStatus = $o["rollupStatus"];
            unset($o['rollupStatus']);
        }
        parent::initFromArray($o);
    }

    /**
     * Sets a known child element of OrdinanceRollup from an XML reader.
     *
     * @param \XMLReader $xml The reader.
     *
     * @return bool Whether a child element was set.
     */
    protected function setKnownChildElement(\XMLReader $xml)
    {
        $happened = parent::setKnownChildElement($xml);
        if ($happened) {
            return true;
        }
        // No additional child elements for OrdinanceRollup
        return false;
    }

    /**
     * Sets a known attribute of OrdinanceRollup from an XML reader.
     *
     * @param \XMLReader $xml The reader.
     *
     * @return bool Whether an attribute was set.
     */
    protected function setKnownAttribute(\XMLReader $xml)
    {
        if (($xml->localName == 'type') && (empty($xml->namespaceURI))) {
            $this->type = $xml->value;
            return true;
        }
        if (($xml->localName == 'rollupStatus') && (empty($xml->namespaceURI))) {
            $this->rollupStatus = $xml->value;
            return true;
        }

        return parent::setKnownAttribute($xml);
    }

    /**
     * Writes the contents of this OrdinanceRollup to an XML writer. The startElement is expected to be already provided.
     *
     * @param \XMLWriter $writer The XML writer.
     */
    public function writeXmlContents(\XMLWriter $writer)
    {
        if ($this->type) {
            $writer->writeAttribute('type', $this->type);
        }
        if ($this->rollupStatus) {
            $writer->writeAttribute('rollupStatus', $this->rollupStatus);
        }
        parent::writeXmlContents($writer);
    }

    /**
     * Writes this OrdinanceRollup to an XML writer.
     *
     * @param \XMLWriter $writer The XML writer.
     * @param bool $includeNamespaces Whether to write out the namespaces in the element.
     */
    public function toXml(\XMLWriter $writer, $includeNamespaces = true)
    {
        $writer->startElementNS('fs', 'ordinanceRollup', null);
        if ($includeNamespaces) {
            $writer->writeAttributeNs('xmlns', 'gx', null, 'http://gedcomx.org/v1/');
            $writer->writeAttributeNs('xmlns', 'fs', null, 'http://familysearch.org/v1/');
        }
        $this->writeXmlContents($writer);
        $writer->endElement();
    }
}
