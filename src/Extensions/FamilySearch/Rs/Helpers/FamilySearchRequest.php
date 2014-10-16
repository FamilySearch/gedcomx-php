<?php

	namespace Gedcomx\Extensions\FamilySearch\Rs\Client\Helpers;

	use Gedcomx\Extensions\FamilySearch\FamilySearchPlatform;
	use Guzzle\Http\Message\Request;

	trait FamilySearchRequest {
	/**
	 * @param string       $method  The http method.
	 * @param string|array $uri    optional: string with an href, or an array with template info
	 *
	 * @return Request The request.
	 */
	protected function createAuthenticatedFamilySearchRequest($method, $uri)
	{
		$request = $this->createAuthenticatedRequest($method, $uri);
		$request->setHeader('Accept', FamilySearchPlatform::JSON_MEDIA_TYPE);
		$request->setHeader('Content-Type', FamilySearchPlatform::JSON_MEDIA_TYPE);
		return $request;
	}
}