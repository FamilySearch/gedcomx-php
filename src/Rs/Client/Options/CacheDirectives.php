<?php

	namespace Gedcomx\Rs\Client\Options;

	use Gedcomx\Rs\Client\GedcomxApplicationState;
	use Guzzle\Http\Message\Request;

	/**
	 * A REST API request options helper providing cache control features. This is similar to the Preconditions class,
	 * but applies inverse logic.
	 *
	 * Class CacheDirectives
	 * @package Gedcomx\Rs\Client\Options
	 */
	class CacheDirectives implements StateTransitionOption
	{

		private $etag;
		private $lastModified;

        /**
		 * Constructs a cache directives class using the specified state. If the ETag (entity tag) specified here does
		 * not match the server's ETag for a resource, the resource will be returned; otherwise, a not-modified status
		 * is returned. The same applies to last modified. If the server's last modified date for a resource is greater
		 * than the last modified specified here, the resource will be returned; otherwise, a not-modified status is
		 * returned.
		 *
		 * @param GedcomxApplicationState $state
         */
		public function __construct(GedcomxApplicationState $state)
		{
			$this->etag = $state->getETag();
			$this->lastModified = $state->getLastModified();
		}

        /**
		 * Applies the ETag or last modified cache control headers to the specified REST API request. If the ETag
		 * (entity tag) specified here does not match the server's ETag for a resource, the resource will be returned;
		 * otherwise, a not-modified status is returned. The same applies to last modified. If the server's last
		 * modified date for a resource is greater than the last modified specified here, the resource will be returned;
		 * otherwise, a not-modified status is returned.
		 *
		 * @param Request $request
         */
		public function apply(Request $request) {
			if ($this->etag !== null) {
				$request->setHeader(HeaderParameter::IF_NONE_MATCH, $this->etag);
			}

			if ($this->lastModified !==  null) {
				$request->setHeader(HeaderParameter::IF_MODIFIED_SINCE, $this->lastModified);
			}
		}
	}