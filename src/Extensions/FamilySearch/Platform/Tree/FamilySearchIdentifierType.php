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
 * Enumeration of FamilySearch-specific identifier types.
 *
 * These identifier types are used to categorize various identifiers within the
 * FamilySearch ecosystem, including Family Tree persons, memory persons, and
 * child-and-parents relationships.
 *
 * @package Gedcomx\Extensions\FamilySearch\Platform\Tree
 */
class FamilySearchIdentifierType
{
    /**
     * The identifier for a child-and-parents relationship pair.
     *
     * In the FamilySearch Family Tree, this represents the unique identifier
     * for the relationship between a child and their parent(s). This is distinct
     * from individual person identifiers.
     */
    const CHILD_AND_PARENTS_RELATIONSHIP = "http://familysearch.org/v1/ChildAndParentsRelationship";

    /**
     * The identifier for a person in FamilySearch Memories.
     *
     * FamilySearch Memories allows users to attach photos, documents, and stories
     * to persons. This identifier type is used for person references within the
     * Memories system.
     */
    const MEMORY_PERSON = "http://familysearch.org/v1/MemoryPerson";

    /**
     * The identifier for a person in the FamilySearch Family Tree.
     *
     * This is the primary identifier type for persons in the collaborative
     * FamilySearch Family Tree. These identifiers are used throughout the
     * FamilySearch platform to reference individuals.
     */
    const FAMILY_TREE_PERSON = "http://familysearch.org/v1/FamilyTreePerson";

    /**
     * Unknown or unrecognized identifier type.
     */
    const OTHER = "http://familysearch.org/v1/OTHER";
}
