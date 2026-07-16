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
 * Identifiers for collections that might contain match results.
 *
 * When performing duplicate detection or record matching in FamilySearch,
 * results can come from various collections. This enumeration identifies
 * the different collections that might contain matching records.
 *
 * @package Gedcomx\Extensions\FamilySearch\Platform\Tree
 */
class MatchCollection
{
    /**
     * The FamilySearch Public Family Tree collection.
     *
     * The main collaborative tree where users contribute and improve
     * genealogical information.
     */
    const TREE = "https://familysearch.org/platform/collections/tree";

    /**
     * The FamilySearch Record Set collection.
     *
     * Historical records that have been indexed and made searchable,
     * including vital records, census records, and other original sources.
     */
    const RECORDS = "https://familysearch.org/platform/collections/records";

    /**
     * The FamilySearch User-Submitted Trees collection (formerly LLS).
     *
     * Legacy trees submitted by users before the Public Family Tree.
     */
    const LLS = "https://familysearch.org/platform/collections/trees";

    /**
     * The FamilySearch CETs (deprecated).
     *
     * Community Extracted Trees - an older collection that has been superseded.
     *
     * @deprecated since 4.2.0
     */
    const CET = "https://familysearch.org/platform/collections/cet";

    /**
     * The FamilySearch User Trees collection.
     *
     * Individual user-maintained trees separate from the collaborative tree.
     */
    const USER_TREES = "https://familysearch.org/platform/collections/user_trees";

    /**
     * The FamilySearch Temple System collection.
     *
     * Records related to LDS temple ordinances.
     */
    const TSS = "https://familysearch.org/platform/collections/temple";

    /**
     * The FamilySearch Assisted Tree Building System.
     *
     * System for suggesting and building family relationships automatically.
     */
    const ATB = "https://familysearch.org/platform/collections/atb";

    /**
     * Unknown or custom collection.
     */
    const OTHER = "https://familysearch.org/platform/collections/OTHER";
}
