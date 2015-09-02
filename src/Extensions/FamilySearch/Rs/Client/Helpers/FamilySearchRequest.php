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
     * @return array Accept and Content-Type headers
     */
    public static function getMediaTypes()
    {
        return [
            'Accept' => FamilySearchPlatform::JSON_MEDIA_TYPE,
            'Content-Type' => FamilySearchPlatform::JSON_MEDIA_TYPE
        ];
    }
}