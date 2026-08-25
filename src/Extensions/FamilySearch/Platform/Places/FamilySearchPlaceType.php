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

namespace Gedcomx\Extensions\FamilySearch\Platform\Places;

/**
 * Enumeration of different types of places that can be described in the FamilySearch place authority.
 *
 * @package Gedcomx\Extensions\FamilySearch\Platform\Places
 */
class FamilySearchPlaceType
{
    /**
     * A standard place.
     */
    const PLACE = "http://familysearch.org/v1/Place";

    /**
     * A place group - a collection or grouping of places.
     */
    const PLACE_GROUP = "http://familysearch.org/v1/PlaceGroup";

    /**
     * Unknown or unrecognized place type.
     */
    const OTHER = "http://familysearch.org/v1/OTHER";
}
