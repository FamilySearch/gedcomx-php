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
 * Identifiers for collections that might contain search results.
 *
 * When performing searches in FamilySearch, results can come from various
 * collections. This enumeration identifies the different searchable collections
 * and provides collection IDs for API usage.
 *
 * @package Gedcomx\Extensions\FamilySearch\Platform\Tree
 */
class SearchCollection
{
    /**
     * The FamilySearch Public Family Tree.
     * Collection ID: "0"
     */
    const TREE = "https://familysearch.org/platform/collections/tree";
    const TREE_ID = "0";

    /**
     * The FamilySearch CETs (deprecated).
     * Collection ID: "10"
     *
     * @deprecated since 4.2.0
     */
    const CET = "https://familysearch.org/platform/collections/cet";
    const CET_ID = "10";

    /**
     * The FamilySearch User Trees.
     * Collection ID: "10"
     */
    const USER_TREES = "https://familysearch.org/platform/collections/user_trees";
    const USER_TREES_ID = "10";

    /**
     * Unknown or custom collection.
     */
    const OTHER = "https://familysearch.org/platform/collections/OTHER";

    /**
     * Get the collection ID for a given collection constant.
     *
     * @param string $collection The collection constant value
     * @return string|null The collection ID, or null if unknown
     */
    public static function getId($collection)
    {
        switch ($collection) {
            case self::TREE:
                return self::TREE_ID;
            case self::CET:
                return self::CET_ID;
            case self::USER_TREES:
                return self::USER_TREES_ID;
            default:
                return null;
        }
    }
}
