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
 * Enumeration of known ordinance reservation claim types.
 *
 * Indicates how an ordinance reservation was claimed or created.
 * Different claim types may have different rules and expiration policies.
 *
 * @package Gedcomx\Extensions\FamilySearch\Platform\Ordinances
 */
class OrdinanceReservationClaimType
{
    /**
     * Default reservation claim type.
     * Standard ordinance reservation.
     */
    const DEFAULT_TYPE = "http://familysearch.org/v1/Default";

    /**
     * Family Group claim type.
     * Reservation claimed as part of a family group.
     */
    const FAMILY_GROUP = "http://familysearch.org/v1/FamilyGroup";

    /**
     * Instant Name claim type.
     * Reservation claimed through instant name feature.
     */
    const INSTANT_NAME = "http://familysearch.org/v1/InstantName";

    /**
     * Shared Ready claim type.
     * Reservation claimed from shared church inventory.
     */
    const SHARED_READY = "http://familysearch.org/v1/SharedReady";

    /**
     * Unknown or unrecognized claim type.
     */
    const OTHER = "http://familysearch.org/v1/OTHER";
}
