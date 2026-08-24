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
 * Enumeration of known ordinance rollup status values.
 *
 * Ordinance rollup status provides a simplified, aggregated view of ordinance status
 * across multiple ordinances. The priority from highest to lowest is:
 * RESERVED_SHARED_READY -> READY -> RESERVED -> NEED_MORE_INFORMATION -> COMPLETED.
 *
 * @package Gedcomx\Extensions\FamilySearch\Platform\Ordinances
 */
class OrdinanceRollupStatus
{
    /**
     * This rollup status for the ordinance indicates it has been completed.
     */
    const ROLLED_UP_COMPLETED = "http://familysearch.org/v1/RolledUpCompleted";

    /**
     * This rollup status for the ordinance indicates it cannot be reserved by the current user because more information is needed about the person.
     */
    const ROLLED_UP_NEED_MORE_INFORMATION = "http://familysearch.org/v1/RolledUpNeedMoreInformation";

    /**
     * This rollup status for the ordinance indicates it is not available.
     */
    const ROLLED_UP_NOT_AVAILABLE = "http://familysearch.org/v1/RolledUpNotAvailable";

    /**
     * This rollup status for the ordinance indicates it can be reserved by the current user.
     */
    const ROLLED_UP_READY = "http://familysearch.org/v1/RolledUpReady";

    /**
     * This rollup status for the ordinance indicates it has been reserved.
     */
    const ROLLED_UP_RESERVED = "http://familysearch.org/v1/RolledUpReserved";

    /**
     * This rollup status for the ordinance indicates it was reserved and shared with or assigned to Church inventory. A secondary reservation is available.
     */
    const ROLLED_UP_RESERVED_SHARED_READY = "http://familysearch.org/v1/RolledUpReservedSharedReady";

    /**
     * Unknown or unrecognized rollup status.
     */
    const OTHER = "http://familysearch.org/v1/OTHER";
}
