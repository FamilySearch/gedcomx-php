<?php

    namespace Gedcomx\Rs\Client;

    class Rel
    {
        //Standard well-known RELs
        const SELF = "self";
        const NEXT = "next";
        const PREV = "prev";
        const PREVIOUS = PREV;
        const FIRST = "first";
        const LAST = "last";

        //GEDCOM-X specific rels.
        const AGENT = "agent";
        const ANCESTRY = "ancestry";
        const ARTIFACTS = "artifacts";
        const CHILDREN = "children";
        const CHILD_RELATIONSHIPS = "child-relationships";
        const COLLECTION = "collection";
        const SUBCOLLECTIONS = "subcollections";
        const CONCLUSION = "conclusion";
        const CONCLUSIONS = "conclusions";
        const CURRENT_USER_PERSON = "current-user-person";
        const CURRENT_USER_RESOURCES = "current-user-resources";
        const DESCENDANCY = "descendancy";
        const DESCRIPTION = "description";
        const EVIDENCE_REFERENCE = "evidence-reference";
        const EVIDENCE_REFERENCES = "evidence-references";
        const IMAGE_RECORDS = "image-records";
        const MATCHES = "matches";
        const MEDIA_REFERENCE = "media-reference";
        const MEDIA_REFERENCES = "media-references";
        const NOTE = "note";
        const NOTES = "notes";
        const OAUTH2_AUTHORIZE = "http://oauth.net/core/2.0/endpoint/authorize";
        const OAUTH2_TOKEN = "http://oauth.net/core/2.0/endpoint/token";
        const PARENT_RELATIONSHIPS = "parent-relationships";
        const PARENTS = "parents";
        const PERSON1 = "person1";
        const PERSON2 = "person2";
        const PERSON = "person";
        const PERSONS = "persons";
        const PERSON_SEARCH = "person-search";
        const PLACE = "place";
        const PLACE_SEARCH = "place-search";
        const PLACE_TYPE_GROUPS = "place-type-groups";
        const PLACE_TYPE_GROUP = "place-type-group";
        const PLACE_TYPES = "place-types";
        const PLACE_TYPE = "place-type";
        const PLACE_GROUP = "place-group";
        const PLACE_DESCRIPTION = "place-description";
        const PROFILE = "profile";
        const RECORD = "record";
        const RECORDS = "records";
        const RELATIONSHIP = "relationship";
        const RELATIONSHIPS = "relationships";
        const SOURCE_DESCRIPTIONS = "source-descriptions";
        const SOURCE_REFERENCE = "source-reference";
        const SOURCE_REFERENCES = "source-references";
        const SOURCE_REFERENCES_QUERY = "source-references-query";
        const SPOUSES = "spouses";
        const SPOUSE_RELATIONSHIPS = "spouse-relationships";
        const DISCUSSION_REFERENCE = "discussion-reference";
        const DISCUSSION_REFERENCES = "discussion-references";
    }