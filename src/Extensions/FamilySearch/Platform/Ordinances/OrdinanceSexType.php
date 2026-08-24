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
 * Enumeration of known ordinance sex types.
 *
 * Represents the sex/gender classification for individuals in ordinance records.
 * Used in ordinances where gender-specific information is relevant.
 *
 * @package Gedcomx\Extensions\FamilySearch\Platform\Ordinances
 */
class OrdinanceSexType
{
    /**
     * Male sex type.
     */
    const MALE = "http://familysearch.org/v1/Male";

    /**
     * Female sex type.
     */
    const FEMALE = "http://familysearch.org/v1/Female";

    /**
     * Unknown sex type.
     * Used when the sex/gender of the individual cannot be determined.
     */
    const UNKNOWN = "http://familysearch.org/v1/Unknown";

    /**
     * Unknown or unrecognized sex type.
     */
    const OTHER = "http://familysearch.org/v1/OTHER";
}
