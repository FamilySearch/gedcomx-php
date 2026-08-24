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

/**
 * Enumeration of known ordinance role types.
 *
 * Represents the different roles that participants can have in an ordinance,
 * such as parent or spouse relationships.
 *
 * @package Gedcomx\Extensions\FamilySearch\Platform\Ordinances
 */
class OrdinanceRoleType
{
    /**
     * Parent role in the ordinance.
     * Used in sealing to parents ordinances.
     */
    const PARENT = "http://familysearch.org/v1/Parent";

    /**
     * Spouse role in the ordinance.
     * Used in sealing to spouse ordinances.
     */
    const SPOUSE = "http://familysearch.org/v1/Spouse";

    /**
     * Unknown or unrecognized role type.
     */
    const OTHER = "http://familysearch.org/v1/OTHER";
}
