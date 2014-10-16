<?php

	namespace Gedcomx\Extensions\FamilySearch\Rs\Client\Helpers;

	use Gedcomx\Extensions\FamilySearch\FamilySearchPlatform;
	use Guzzle\Http\Message\Request;

	class FamilySearchRequest {
		/**
		 * @param Request $request
		 */
	public static function applyFamilySearchMediaType(Request &$request)
	{
		$request->setHeader('Accept', FamilySearchPlatform::JSON_MEDIA_TYPE);
		$request->setHeader('Content-Type', FamilySearchPlatform::JSON_MEDIA_TYPE);
	}
}