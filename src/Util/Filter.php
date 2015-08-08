<?php

namespace Gedcomx\Util;

/**
 * An interface for manipulating or reporting on a REST API client and request just before the specified client executes the specified request.
 */
interface Filter
{
    /**
     * When overridden in a class this method is used to manipulate or report on the specified REST API request just before the client executes the request.
     * @param array|RequestInterface $requests
     * @return array|RequestInterface
     */
    function apply($requests);
}