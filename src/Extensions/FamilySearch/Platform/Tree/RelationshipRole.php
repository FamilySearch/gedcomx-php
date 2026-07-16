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
 * Enumeration of relationship roles in the FamilySearch Family Tree.
 *
 * These roles define the positions individuals hold within family relationships.
 * The FamilySearch Family Tree uses a neutral numbering system (Parent1/Parent2,
 * Spouse1/Spouse2) to avoid gender assumptions and accommodate diverse family
 * structures.
 *
 * @package Gedcomx\Extensions\FamilySearch\Platform\Tree
 */
class RelationshipRole
{
    /**
     * The first parent in a child-and-parents relationship.
     *
     * Parent1 is typically (but not always) the mother in traditional records,
     * but the FamilySearch Family Tree uses this neutral terminology to avoid
     * making assumptions about gender roles or family structure.
     */
    const PARENT1 = "http://familysearch.org/v1/Parent1";

    /**
     * The second parent in a child-and-parents relationship.
     *
     * Parent2 is typically (but not always) the father in traditional records,
     * but the FamilySearch Family Tree uses this neutral terminology to avoid
     * making assumptions about gender roles or family structure.
     */
    const PARENT2 = "http://familysearch.org/v1/Parent2";

    /**
     * The child in a child-and-parents relationship.
     *
     * Represents the offspring in the parent-child relationship. A person can
     * be a child in one relationship and a parent in another.
     */
    const CHILD = "http://familysearch.org/v1/Child";

    /**
     * The first spouse in a couple relationship.
     *
     * Spouse1 represents one partner in a couple relationship. The numbering
     * is arbitrary and does not imply any hierarchy or traditional gender roles.
     */
    const SPOUSE1 = "http://familysearch.org/v1/Spouse1";

    /**
     * The second spouse in a couple relationship.
     *
     * Spouse2 represents the other partner in a couple relationship. The numbering
     * is arbitrary and does not imply any hierarchy or traditional gender roles.
     */
    const SPOUSE2 = "http://familysearch.org/v1/Spouse2";

    /**
     * Unknown or unrecognized relationship role.
     */
    const OTHER = "http://familysearch.org/v1/OTHER";
}
