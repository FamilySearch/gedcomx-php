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
 * Enumeration of FamilySearch-specific fact qualifiers.
 *
 * Fact qualifiers provide additional context or classification for facts
 * recorded in the FamilySearch Family Tree. These qualifiers help distinguish
 * between different interpretations or uses of fact data.
 *
 * @package Gedcomx\Extensions\FamilySearch\Platform\Tree
 */
class FamilyTreeFactQualifierType
{
    /**
     * A fact is qualified as an 'event'.
     *
     * This qualifier indicates that a fact represents a specific event that
     * occurred at a particular point in time, as opposed to a continuous state
     * or characteristic. For example, a birth is an event, while a residence
     * might be either an event (moved to a location) or a state (lived there
     * for a period).
     */
    const EVENT = "http://familysearch.org/v1/Event";

    /**
     * Unknown or unrecognized qualifier type.
     */
    const OTHER = "http://familysearch.org/v1/OTHER";
}
