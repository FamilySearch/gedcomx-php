<?php


namespace Gedcomx\Rs\Api;


class Rel
{

    const SELF = 'self';
    const NEXT = 'next';
    const PREV = 'pref';
    const PREVIOUS = Rel::PREV;
    const FIRST = 'first';
    const LAST = 'last';
    const OAUTH2_AUTHORIZE = 'http://oauth.net/core/2.0/endpoint/authorize';
    const OAUTH2_TOKEN = 'http://oauth.net/core/2.0/endpoint/token';
    const CURRENT_USER_PERSON = 'current-user-person';

}