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
 * Enumeration of third party access restrictions that can be set on a tree.
 *
 * These settings control what third-party applications can access a tree
 * and its data. Access restrictions can be set differently for the tree owner
 * and for group members.
 *
 * @package Gedcomx\Extensions\FamilySearch\Platform\Tree
 */
class ThirdPartyAccess
{
    /**
     * Allows the group owner or group members to access the tree from any third-party application.
     *
     * This is the most permissive setting, allowing access from any application
     * that has been granted API access to FamilySearch. Group owner access cannot
     * be more restrictive than group member access.
     */
    const ANY_APPS = "http://familysearch.org/v1/AnyApps";

    /**
     * Restricts the group owner or group members to access tree only from applications
     * owned by the company that owns the application that created the tree.
     *
     * This provides a middle tier of access control, allowing only applications
     * from the same organization to access the tree data.
     */
    const COMPANY_APPS = "http://familysearch.org/v1/CompanyApps";

    /**
     * Disallows the group members access to the tree from all third-party applications.
     *
     * This is the most restrictive setting. Note that owner access cannot be set
     * to None - the tree owner must maintain at least some level of third-party access.
     */
    const NONE = "http://familysearch.org/v1/None";

    /**
     * Unknown or custom access level.
     */
    const OTHER = "http://familysearch.org/v1/OTHER";
}
