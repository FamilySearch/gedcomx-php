<?php

	namespace Gedcomx\Rs\Client\Options;

	use Gedcomx\Rs\Client\GedcomxApplicationState;
	use GuzzleHttp\Psr7\Request;

	/**
	 * A REST API request options helper providing precondition features. This is similar to the CacheDirectives class, but applies inverse logic.
	 *
	 * Class Preconditions
	 *
	 * @package Gedcomx\Rs\Client\Options
	 */
	class Preconditions implements StateTransitionOption
	{
		private $etag;
		private $lastModified;

		/**
		 * Sets the last modified date to send to the server for evaluation. If the last modified date specified here
		 * matches the server's last modified date for a resource, the resource will be returned; otherwise, a
		 * precondition-failed status is returned.
		 * @param \DateTime $lastModified
		 */
        public function setLastModified(\DateTime $lastModified)
        {
            $this->lastModified = $lastModified;
        }
        /**
		 * Sets the ETag to send to the server for evaluation. If the ETag (entity tag) specified here matches the
		 * server's ETag for a resource, the resource will be returned; otherwise, a precondition-failed status is
		 * returned.
		 *
		 * @param string $etag
         */
        public function setEtag($etag)
        {
            $this->etag = $etag;
        }
        /**
		 * Sets the last modified date and ETag to send to the server for evaluation. If the ETag (entity tag) specified
		 * here matches the server's ETag for a resource, the resource will be returned; otherwise, a
		 * precondition-failed status is returned. The same applies to last modified. If the server's last modified date
		 * for a resource is equal to the last modified specified here, the resource will be returned; otherwise, a
		 * precondition-failed status is returned.
		 *
		 * @param string $etag
         * @param \DateTime $lastModified
         */
		public function setBothConditions($etag, \DateTime $lastModified)
		{
            $this->etag = $etag;
            $this->lastModified = $lastModified;
		}

        /**
		 * Sets the state to use for specifying the last modified date and ETag. The last modified date and ETag values
		 * used in the preconditions will come from the specified state instance.
		 *
         * @param GedcomxApplicationState $state
         */
        public function setState(GedcomxApplicationState $state)
        {
            $this->etag = $state->getETag();
            $this->lastModified = $state->getLastModified();
        }

        /**
		 * Applies the ETag or last modified cache control headers to the specified REST API request. The cache control
		 * headers are applied conditionally. The ETag and last modified values will only be applied if they are not
		 * null. Furthermore, the application of each are independent of the other. This could, therefore, only apply a
		 * last modified cache control header and not an ETag cache control header if the ETag property of this instance
		 * were null and last modified was not.
		 *
		 * @param Request $request
		 * @param Request $request
         */
		public function apply(Request $request)
		{
			$newRequest = $request;
			if ($this->etag !== null) {
				$newRequest = $request->withHeader(HeaderParameter::IF_MATCH, $this->etag);
			}
			if ($this->lastModified !== null) {
				$newRequest = $request->withHeader(HeaderParameter::IF_UNMODIFIED_SINCE, $this->lastModified);
			}
			return $newRequest;
		}

	}