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
 * Enumeration of known ordinance status values.
 *
 * Represents the various states an ordinance can be in, from available to completed.
 *
 * @package Gedcomx\Extensions\FamilySearch\Platform\Ordinances
 */
class OrdinanceStatus
{
    /**
     * The ordinance is not needed because the person was born in the covenant.
     */
    const BORN_IN_COVENANT = "http://familysearch.org/v1/BornInCovenant";

    /**
     * The ordinance has been completed.
     */
    const COMPLETED = "http://familysearch.org/v1/Completed";

    /**
     * The ordinance cannot be reserved because more information is needed about the person.
     */
    const NEED_MORE_INFORMATION = "http://familysearch.org/v1/NeedMoreInformation";

    /**
     * The ordinance cannot be reserved without special permission.
     */
    const NEED_PERMISSION = "http://familysearch.org/v1/NeedPermission";

    /**
     * The ordinance is not available to be reserved.
     */
    const NOT_AVAILABLE = "http://familysearch.org/v1/NotAvailable";

    /**
     * The ordinance cannot be reserved because it is not needed according to the policies of the Church.
     */
    const NOT_NEEDED = "http://familysearch.org/v1/NotNeeded";

    /**
     * The ordinance cannot currently be reserved, but it is expected that the ordinance will eventually become Ready after a period of time.
     */
    const NOT_READY = "http://familysearch.org/v1/NotReady";

    /**
     * The ordinance can be reserved.
     */
    const READY = "http://familysearch.org/v1/Ready";

    /**
     * The ordinance has been reserved.
     */
    const RESERVED = "http://familysearch.org/v1/Reserved";

    /**
     * The ordinance has been reserved and printed. It is currently in progress of completion.
     */
    const RESERVED_PRINTED = "http://familysearch.org/v1/ReservedPrinted";

    /**
     * The ordinance has been reserved and is waiting for prerequisite ordinances to be completed.
     */
    const RESERVED_WAITING = "http://familysearch.org/v1/ReservedWaiting";

    /**
     * The ordinance has been reserved and shared with or assigned to Church inventory.
     */
    const RESERVED_SHARED = "http://familysearch.org/v1/ReservedShared";

    /**
     * The ordinance was reserved and shared with or assigned to Church inventory. A secondary reservation is available.
     */
    const RESERVED_SHARED_READY = "http://familysearch.org/v1/ReservedSharedReady";

    /**
     * The ordinance was reserved, shared with or assigned to Church inventory, and has been reserved and printed.
     */
    const RESERVED_SHARED_PRINTED = "http://familysearch.org/v1/ReservedSharedPrinted";

    /**
     * Unknown or unrecognized ordinance status.
     */
    const OTHER = "http://familysearch.org/v1/OTHER";
}
