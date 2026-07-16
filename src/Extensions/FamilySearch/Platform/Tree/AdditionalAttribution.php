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

/**
 * Class AdditionalAttribution
 *
 * Extension element for capturing additional attributions on an already-attributed object.
 *
 * In genealogical data, objects like persons, relationships, and facts typically have
 * a primary attribution indicating who created or last modified the data. However, there
 * are cases where multiple contributors have made significant contributions that should
 * be acknowledged separately. AdditionalAttribution provides a way to record these
 * supplementary attributions.
 *
 * ## Use Cases
 *
 * **Multiple Contributors:**
 * When several people have contributed to building out a person's profile, you may want
 * to attribute specific contributions to specific people beyond the primary contributor.
 *
 * **Source Transcription:**
 * When one person transcribes a source and another person reviews and corrects it,
 * both contributors can be acknowledged.
 *
 * **Collaborative Research:**
 * In group research projects, you may want to track all contributors who have added
 * information to a record, not just the most recent editor.
 *
 * **Historical Context:**
 * Preserving the history of who contributed what helps maintain data provenance and
 * gives proper credit to genealogical researchers.
 *
 * ## Properties (Inherited from Attribution)
 *
 * All properties are inherited from the base Attribution class:
 * - `contributor` (ResourceReference) - Reference to the person who contributed
 * - `modified` (timestamp) - When this contribution was made
 * - `changeMessage` (string) - Optional message explaining the contribution
 *
 * ## Example Usage
 *
 * ```php
 * use Gedcomx\Common\ResourceReference;
 * use Gedcomx\Extensions\FamilySearch\Platform\Tree\AdditionalAttribution;
 *
 * // Create an additional attribution for a supplementary contributor
 * $additionalAttr = new AdditionalAttribution();
 *
 * $contributor = new ResourceReference();
 * $contributor->setResource('https://familysearch.org/platform/users/USER123');
 * $additionalAttr->setContributor($contributor);
 *
 * $additionalAttr->setModified(time() * 1000); // milliseconds
 * $additionalAttr->setChangeMessage('Added birth date from parish records');
 *
 * // This can then be attached to a person, fact, or relationship
 * ```
 *
 * ## XML Example
 *
 * ```xml
 * <fs:additionalAttribution xmlns:fs="http://familysearch.org/v1/">
 *   <contributor resource="https://familysearch.org/platform/users/USER123"/>
 *   <modified>1625097600000</modified>
 *   <changeMessage>Added birth date from parish records</changeMessage>
 * </fs:additionalAttribution>
 * ```
 *
 * ## JSON Example
 *
 * ```json
 * {
 *   "contributor": {
 *     "resource": "https://familysearch.org/platform/users/USER123"
 *   },
 *   "modified": 1625097600000,
 *   "changeMessage": "Added birth date from parish records"
 * }
 * ```
 *
 * @package Gedcomx\Extensions\FamilySearch\Platform\Tree
 */
class AdditionalAttribution extends Attribution
{
    /**
     * Constructs an AdditionalAttribution from a (parsed) JSON hash or XML reader.
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
     * Writes this AdditionalAttribution to an XML writer.
     *
     * Overrides the parent method to use the FamilySearch-specific element name.
     *
     * @param \XMLWriter $writer The XML writer.
     * @param bool $includeNamespaces Whether to write out the namespaces in the element.
     */
    public function toXml($writer, $includeNamespaces = true)
    {
        $writer->startElementNS('fs', 'additionalAttribution', null);
        if ($includeNamespaces) {
            $writer->writeAttributeNs('xmlns', 'fs', null, 'http://familysearch.org/v1/');
            $writer->writeAttributeNs('xmlns', 'gx', null, 'http://gedcomx.org/v1/');
        }
        $this->writeXmlContents($writer);
        $writer->endElement();
    }
}
