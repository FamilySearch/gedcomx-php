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
 * Enumeration of name form ordering types.
 *
 * Name forms can be ordered differently depending on cultural conventions.
 * Eurotypic ordering places the given name first, followed by the surname
 * (e.g., "John Smith"). Sinotypic ordering places the surname first, followed
 * by the given name (e.g., "Smith John" or in Chinese: 李明).
 *
 * This is particularly important for international genealogy where different
 * cultures have different conventions for name ordering.
 *
 * @package Gedcomx\Extensions\FamilySearch\Platform\Tree
 */
class NameFormOrder
{
    /**
     * Eurotypic name ordering (Western style).
     *
     * Given name comes first, followed by surname.
     * Example: "John Smith"
     *
     * This is the standard ordering for most Western European languages
     * and cultures, including English, Spanish, French, German, etc.
     */
    const EUROTYPIC = "http://familysearch.org/v1/Eurotypic";

    /**
     * Sinotypic name ordering (Eastern style).
     *
     * Surname comes first, followed by given name.
     * Example: "Smith John" or in Chinese: "李明" (Li Ming)
     *
     * This is the standard ordering for Chinese, Japanese, Korean, Vietnamese,
     * and Hungarian names, among others.
     */
    const SINOTYPIC = "http://familysearch.org/v1/Sinotypic";
}
