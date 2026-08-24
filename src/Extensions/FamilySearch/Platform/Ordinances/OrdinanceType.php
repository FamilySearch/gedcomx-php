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
 * Enumeration of known ordinance types.
 *
 * These represent the various LDS (Latter-day Saint) temple ordinances that can be
 * performed for individuals in genealogical records.
 *
 * @package Gedcomx\Extensions\FamilySearch\Platform\Ordinances
 */
class OrdinanceType
{
    /**
     * Baptism ordinance.
     * The first saving ordinance of the gospel.
     */
    const BAPTISM = "http://churchofjesuschrist.org/Baptism";

    /**
     * Confirmation ordinance.
     * Ordinance of receiving the gift of the Holy Ghost.
     */
    const CONFIRMATION = "http://churchofjesuschrist.org/Confirmation";

    /**
     * Initiatory ordinance.
     * Temple ordinance preparing individuals for the endowment.
     */
    const INITIATORY = "http://churchofjesuschrist.org/Initiatory";

    /**
     * Endowment ordinance.
     * Sacred temple ordinance of instruction and covenants.
     */
    const ENDOWMENT = "http://churchofjesuschrist.org/Endowment";

    /**
     * Sealing to Spouse ordinance.
     * Temple ordinance sealing a married couple for eternity.
     */
    const SEALING_TO_SPOUSE = "http://churchofjesuschrist.org/SealingToSpouse";

    /**
     * Sealing Child to Parents ordinance.
     * Temple ordinance sealing a child to their parents for eternity.
     */
    const SEALING_CHILD_TO_PARENTS = "http://churchofjesuschrist.org/SealingChildToParents";

    /**
     * Unknown or unrecognized ordinance type.
     */
    const OTHER = "http://churchofjesuschrist.org/OTHER";
}
