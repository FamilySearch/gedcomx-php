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
 * Enumeration of known ordinance reservation assignee types.
 *
 * Indicates to whom or what entity an ordinance reservation is assigned.
 * Reservations can be assigned to personal inventory or church inventory.
 *
 * @package Gedcomx\Extensions\FamilySearch\Platform\Ordinances
 */
class OrdinanceReservationAssigneeType
{
    /**
     * Assigned to Church inventory.
     * The ordinance reservation is held in church general inventory.
     */
    const CHURCH = "http://churchofjesuschrist.org/Church";

    /**
     * Assigned to Personal inventory.
     * The ordinance reservation is held in a user's personal reservation list.
     */
    const PERSONAL = "http://churchofjesuschrist.org/Personal";

    /**
     * Unknown or unrecognized assignee type.
     */
    const OTHER = "http://churchofjesuschrist.org/OTHER";
}
