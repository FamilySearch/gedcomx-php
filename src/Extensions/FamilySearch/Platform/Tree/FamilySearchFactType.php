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
 * Enumeration of FamilySearch-specific fact types.
 *
 * These fact types extend the standard GEDCOM X fact types with FamilySearch-specific
 * types used in the FamilySearch Family Tree.
 *
 * @package Gedcomx\Extensions\FamilySearch\Platform\Tree
 */
class FamilySearchFactType
{
    /**
     * Person fact type: Affiliation to something (e.g., organization, institution).
     */
    const AFFILIATION = "http://familysearch.org/v1/Affiliation";

    /**
     * Parent-Child fact type: A child's birth order relative to parents.
     * Used to indicate the order in which children were born.
     */
    const BIRTH_ORDER = "http://familysearch.org/v1/BirthOrder";

    /**
     * Couple fact type: Indicates that a couple never had children.
     * Used to document childless couples in genealogical records.
     */
    const COUPLE_NEVER_HAD_CHILDREN = "http://familysearch.org/v1/CoupleNeverHadChildren";

    /**
     * Person fact type: Person died before reaching age eight.
     * Significant in LDS genealogy for ordinance work.
     */
    const DIED_BEFORE_EIGHT = "http://familysearch.org/v1/DiedBeforeEight";

    /**
     * Person fact type: A brief biographical narrative or "life sketch" of the person.
     * Used to provide a summary of a person's life story.
     */
    const LIFE_SKETCH = "http://familysearch.org/v1/LifeSketch";

    /**
     * Couple fact type: Indicates that a couple lived together.
     * May represent cohabitation without formal marriage.
     */
    const LIVED_TOGETHER = "http://familysearch.org/v1/LivedTogether";

    /**
     * Person fact type: Indicates that a person had no children.
     * Documents the absence of offspring for genealogical completeness.
     */
    const NO_CHILDREN = "http://familysearch.org/v1/NoChildren";

    /**
     * Person fact type: Indicates that a person has no couple relationships.
     * Documents that the person never married or entered a couple relationship.
     */
    const NO_COUPLE_RELATIONSHIPS = "http://familysearch.org/v1/NoCoupleRelationships";

    /**
     * Person fact type: A title of nobility held by the person.
     * Examples include Duke, Baron, Earl, etc.
     */
    const TITLE_OF_NOBILITY = "http://familysearch.org/v1/TitleOfNobility";

    /**
     * Person fact type: The name of the tribe to which the person belongs.
     * Used primarily for indigenous peoples and tribal affiliations.
     */
    const TRIBE_NAME = "http://familysearch.org/v1/TribeName";

    /**
     * Unknown or unrecognized fact type.
     */
    const OTHER = "http://familysearch.org/v1/OTHER";
}
