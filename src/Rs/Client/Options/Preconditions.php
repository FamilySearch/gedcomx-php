<?php

	namespace Gedcomx\Rs\Client\Options;

	use Gedcomx\Rs\Client\GedcomxApplicationState;
	use Gedcomx\Rs\Client\StateTransitionOption;
	use Guzzle\Http\Message\Request;

	class Preconditions implements StateTransitionOption
	{
		private $etag;
		private $lastModified;

        /**
         * @param GedcomxApplicationState $state
         */
		public function __construct(GedcomxApplicationState $state)
		{
			$this->etag = $state->getETag();
			$this->lastModified = $state->getLastModified();
		}

        /**
         * @param Request $request
         */
		public function apply(Request $request)
		{
			if ($this->etag !== null) {
				$request->addHeader(HeaderParameter::IF_MATCH, $this->etag);
			}

			if ($this->lastModified !== null) {
				$request->addHeader(HeaderParameter::IF_UNMODIFIED_SINCE, $this->lastModified);
			}
		}

	}