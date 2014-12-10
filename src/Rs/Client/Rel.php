<?php

    namespace Gedcomx\Rs\Client;

    /**
     * A collection of rel links to assist in looking up resource links.
     * When a resource is consumed, it typically returns a set of hypermedia links that enable additional actions on the resource. While
     * resources typically provide links, not all links will be available on a given resource (such as paging links on a person resource).
     * The links exposed in this class are a set of predefined constants, which can be used to determine if a link is available on a
     * given resource.
     */
    class Rel
    {
        /****************************
         * Standard well-known RELs *
         ****************************/
        /**
         * The link that points to oneself.
         */
        const SELF = "self";
        /**
         * A link that points to the next item, such as in a collection or result set.
         */
        const NEXT = "next";
        /**
         * A link that points to the previous item, such as in a collection or result set.
         */
        const PREV = "prev";
        /**
         * A link that points to the previous item, such as in a collection or result set.
         */
        const PREVIOUS = PREV;
        /**
         * A link that points to the first item, such as in a collection or result set.
         */
        const FIRST = "first";
        /**
         * A link that points to the last item, such as in a collection or result set.
         */
        const LAST = "last";

        /**************************
         * GEDCOM-X specific rels *
         **************************/
        /**
         * A link that points to the agent resource.
         */
        const AGENT = "agent";
        /**
         * A link that points to the ancestry resource.
         */
        const ANCESTRY = "ancestry";
        /**
         * A link that points to the artifacts resource.
         */
        const ARTIFACTS = "artifacts";
        /**
         * A link that points to the children resource.
         */
        const CHILDREN = "children";
        /**
         * A link that points to the child relationships resource.
         */
        const CHILD_RELATIONSHIPS = "child-relationships";
        /**
         * A link that points to the collection resource.
         */
        const COLLECTION = "collection";
        /**
         * A link that points to the subcollections resource.
         */
        const SUBCOLLECTIONS = "subcollections";
        /**
         * A link that points to the conclusion resource.
         */
        const CONCLUSION = "conclusion";
        /**
         * A link that points to the conclusions resource.
         */
        const CONCLUSIONS = "conclusions";
        /**
         * A link that points to the current user person resource.
         */
        const CURRENT_USER_PERSON = "current-user-person";
        /**
         * A link that points to the current user resources resource.
         */
        const CURRENT_USER_RESOURCES = "current-user-resources";
        /**
         * A link that points to the descendancy resource.
         */
        const DESCENDANCY = "descendancy";
        /**
         * A link that points to the description resource.
         */
        const DESCRIPTION = "description";
        /**
         * A link that points to the evidence reference resource.
         */
        const EVIDENCE_REFERENCE = "evidence-reference";
        /**
         * A link that points to the evidence references resource.
         */
        const EVIDENCE_REFERENCES = "evidence-references";
        /**
         * A link that points to the image records resource.
         */
        const IMAGE_RECORDS = "image-records";
        /**
         * A link that points to the matches resource.
         */
        const MATCHES = "matches";
        /**
         * A link that points to the media reference resource.
         */
        const MEDIA_REFERENCE = "media-reference";
        /**
         * A link that points to the media references resource.
         */
        const MEDIA_REFERENCES = "media-references";
        /**
         * A link that points to the note resource.
         */
        const NOTE = "note";
        /**
         * A link that points to the notes resource.
         */
        const NOTES = "notes";
        /**
         * A link that points to the OAuth2 authorization resource.
         */
        const OAUTH2_AUTHORIZE = "http://oauth.net/core/2.0/endpoint/authorize";
        /**
         * A link that points to the OAuth2 token resource.
         */
        const OAUTH2_TOKEN = "http://oauth.net/core/2.0/endpoint/token";
        /**
         * A link that points to the parent relationships resource.
         */
        const PARENT_RELATIONSHIPS = "parent-relationships";
        /**
         * A link that points to the parents resource.
         */
        const PARENTS = "parents";
        /**
         * A link that points to the person1 resource.
         */
        const PERSON1 = "person1";
        /**
         * A link that points to the person2 resource.
         */
        const PERSON2 = "person2";
        /**
         * A link that points to the person resource.
         */
        const PERSON = "person";
        /**
         * A link that points to the persons resource.
         */
        const PERSONS = "persons";
        /**
         * A link that points to the person search resource.
         */
        const PERSON_SEARCH = "person-search";
        /**
         * A link that points to the place resource.
         */
        const PLACE = "place";
        /**
         * A link that points to the place search resource.
         */
        const PLACE_SEARCH = "place-search";
        /**
         * The A link that points to the place type groups resource.
         */
        const PLACE_TYPE_GROUPS = "place-type-groups";
        /**
         * A link that points to the place type group resource.
         */
        const PLACE_TYPE_GROUP = "place-type-group";
        /**
         * A link that points to the place types resource.
         */
        const PLACE_TYPES = "place-types";
        /**
         * A link that points to the place type resource.
         */
        const PLACE_TYPE = "place-type";
        /**
         * A link that points to the place group resource.
         */
        const PLACE_GROUP = "place-group";
        /**
         * A link that points to the place description resource.
         */
        const PLACE_DESCRIPTION = "place-description";
        /**
         * A link that points to the profile resource.
         */
        const PROFILE = "profile";
        /**
         * A link that points to the record resource.
         */
        const RECORD = "record";
        /**
         * A link that points to the records resource.
         */
        const RECORDS = "records";
        /**
         * A link that points to the relationship resource.
         */
        const RELATIONSHIP = "relationship";
        /**
         * A link that points to the relationships resource.
         */
        const RELATIONSHIPS = "relationships";
        /**
         * A link that points to the source descriptions resource.
         */
        const SOURCE_DESCRIPTIONS = "source-descriptions";
        /**
         * A link that points to the source reference resource.
         */
        const SOURCE_REFERENCE = "source-reference";
        /**
         * A link that points to the source references resource.
         */
        const SOURCE_REFERENCES = "source-references";
        /**
         * A link that points to the source references query resource.
         */
        const SOURCE_REFERENCES_QUERY = "source-references-query";
        /**
         * A link that points to the spouses resource.
         */
        const SPOUSES = "spouses";
        /**
         * A link that points to the spouse relationships resource.
         */
        const SPOUSE_RELATIONSHIPS = "spouse-relationships";
        /**
         * A link that points to the discussion reference resource.
         */
        const DISCUSSION_REFERENCE = "discussion-reference";
        /**
         * A link that points to the discussion references resource.
         */
        const DISCUSSION_REFERENCES = "discussion-references";
    }