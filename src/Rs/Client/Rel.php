<?php


namespace Gedcomx\Rs\Client;


class Rel
{

    const SELF = 'self';
    const NEXT = 'next';
    const PREV = 'pref';
    const PREVIOUS = Rel::PREV;
    const FIRST = 'first';
    const LAST = 'last';

    const AGENT = 'agent';
    const ANCESTRY = 'ancestry';
    const ARTIFACTS = 'artifacts';
    const CHILDREN = 'children';
    const CHILD_RELATIONSHIPS = 'child-relationships';
    const COLLECTION = 'collection';
    const COLLECTIONS = 'collections';
    const CONCLUSION = 'conclusion';
    const CONCLUSIONS = 'conclusions';
    const CURRENT_USER_PERSON = 'current-user-person';
    const CURRENT_USER_RESOURCES = 'current-user-resources';
    const DESCENDANCY = 'descendancy';
    const DESCRIPTION = 'description';
    const EVIDENCE_REFERENCE = 'evidence-reference';
    const EVIDENCE_REFERENCES = 'evidence-references';
    const MEDIA_REFERENCE = 'media-reference';
    const MEDIA_REFERENCES = 'media-references';
    const NOTE = 'note';
    const NOTES = 'notes';
    const OAUTH2_AUTHORIZE = 'http://oauth.net/core/2.0/endpoint/authorize';
    const OAUTH2_TOKEN = 'http://oauth.net/core/2.0/endpoint/token';
    const PARENT_RELATIONSHIPS = 'parent-relationships';
    const PARENTS = 'parents';
    const PERSON1 = 'person1';
    const PERSON2 = 'person2';
    const PERSON = 'person';
    const PERSONS = 'persons';
    const PERSON_SEARCH = 'person-search';
    const PROFILE = 'profile';
    const RECORD = 'record';
    const RECORDS = 'records';
    const RELATIONSHIP = 'relationship';
    const RELATIONSHIPS = 'relationships';
    const SOURCE_DESCRIPTIONS = 'source-descriptions';
    const SOURCE_REFERENCE = 'source-reference';
    const SOURCE_REFERENCES = 'source-references';
    const SPOUSES = 'spouses';
    const SPOUSE_RELATIONSHIPS = 'spouse-relationships';
    const DISCUSSION_REFERENCE = "discussion-reference";
    const DISCUSSION_REFERENCES = "discussion-references";
}