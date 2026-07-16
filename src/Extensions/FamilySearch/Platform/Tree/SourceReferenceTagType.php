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
 * Enumeration of source reference tag types.
 *
 * Source reference tags categorize what type of information a source provides
 * or verifies. This helps users understand what aspects of a person's record
 * are supported by each source in the FamilySearch Family Tree.
 *
 * @package Gedcomx\Extensions\FamilySearch\Platform\Tree
 */
class SourceReferenceTagType
{
    /**
     * Source reference tag type: Name.
     *
     * Indicates that the source provides or verifies information about
     * a person's name. This could include given names, surnames, nicknames,
     * married names, or other name variations.
     */
    const NAME = "http://gedcomx.org/Name";

    /**
     * Source reference tag type: Gender.
     *
     * Indicates that the source provides or verifies information about
     * a person's gender. This is particularly important when historical
     * records use gender-ambiguous names or when correcting gender
     * information in the tree.
     */
    const GENDER = "http://gedcomx.org/Gender";

    /**
     * Unknown or custom source reference tag type.
     */
    const OTHER = "http://familysearch.org/v1/OTHER";
}
