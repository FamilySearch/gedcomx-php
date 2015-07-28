<?php

namespace Gedcomx\Extensions\FamilySearch\Rs\Client\Helpers;

use Gedcomx\Extensions\FamilySearch\FamilySearchPlatform;
use GuzzleHttp\Psr7\Request;

/**
 * A helper class to set accept and content-type headers for REST API requests.
 * Class FamilySearchRequest
 *
 * @package Gedcomx\Extensions\FamilySearch\Rs\Client\Helpers
 */
class FamilySearchRequest
{
    /**
     * Applies the FamilySearch specific JSON accept and content-type headers on the specified request.
     *
     * @param Request $request
     */
    public static function applyFamilySearchMediaType(Request &$request)
    {
        $request->withHeader('Accept', FamilySearchPlatform::JSON_MEDIA_TYPE);
        $request->withHeader('Content-Type', FamilySearchPlatform::JSON_MEDIA_TYPE);
    }
}