<?php

namespace Gedcomx\Extensions\FamilySearch\Rs\Client;

/**
 * A collection of rel links to assist in looking up resource links. When a resource is consumed, it typically
 * returns a set of hypermedia links that enable additional actions on the resource. Whileresources typically
 * provide links, not all links will be available on a given resource (such as paging links on a person resource).
 * The links exposed in this class are a set of predefined constants, which can be used to determine if a link is
 * available on a given resource.
 *
 * Class Rel
 *
 * @package Gedcomx\Extensions\FamilySearch\Rs\Client
 */
class Rel extends \Gedcomx\Rs\Client\Rel
{
    /**
     * A link that points to the change history resource.
     */
    const CHANGE_HISTORY = "change-history";
    /**
     * A link that points to the comment resource.
     */
    const COMMENT = "comment";
    /**
     * A link that points to the comments resource.
     */
    const COMMENTS = "comments";
    /**
     * A link that points to the current user resource.
     */
    const CURRENT_USER = "current-user";
    /**
     * A link that points to the current user history resource.
     */
    const CURRENT_USER_HISTORY = "current-user-history";
    /**
     * A link that points to the discussions resource.
     */
    const DISCUSSIONS = "discussions";
    /**
     * A link that points to the merge resource.
     */
    const MERGE = "merge";
    /**
     * A link that points to the merge mirror resource.
     */
    const MERGE_MIRROR = "merge-mirror";
    /**
     * A link that points to the normalized date resource.
     */
    const NORMALIZED_DATE = "normalized-date";
    /**
     * A link that points to the not a matches resource.
     */
    const NOT_A_MATCHES = "non-matches";
    /**
     * A link that points to the not a match resource.
     */
    const NOT_A_MATCH = "non-match";
    /**
     * A link that points to the person matches query resource.
     */
    const PERSON_MATCHES_QUERY = "person-matches-query";
    /**
     * A link that points to the portrait resource.
     */
    const PORTRAIT = "portrait";
    /**
     * A link that points to the portraits resource.
     */
    const PORTRAITS = "portraits";
    /**
     * A link that points to the restore resource.
     */
    const RESTORE = "restore";
    /**
     * A link that points to the father role resource.
     */
    const FATHER_ROLE = "father-role";
    /**
     * A link that points to the mother role resource.
     */
    const MOTHER_ROLE = "mother-role";
    /**
     * A link that points to the person with relationships resource.
     */
    const PERSON_WITH_RELATIONSHIPS = "person-with-relationships";
    /**
     * A link that points to the preferred spouse relationship resource.
     */
    const PREFERRED_SPOUSE_RELATIONSHIP = "preferred-spouse-relationship";
    /**
     * A link that points to the preferred parent relationship resource.
     */
    const PREFERRED_PARENT_RELATIONSHIP = "preferred-parent-relationship";
}