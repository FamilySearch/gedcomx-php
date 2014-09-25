<?php

	namespace Gedcomx\Rs\Client\Options;

	use Gedcomx\Rs\Client\GedcomxApplicationState;
	use Gedcomx\Rs\Client\StateTransitionOption;
	use Guzzle\Http\Message\Request;

	class CacheDirectives implements StateTransitionOption
	{

		private $etag;
		private $lastModified;

		public function __construct(GedcomxApplicationState $state)
		{
			$this->etag = $state->getETag();
			$this->lastModified = $state->getLastModified();
		}

		public function apply(Request $request) {
			if ($this->etag !== null) {
				$request->setHeader(HeaderParameter::IF_NONE_MATCH, $this->etag);
			}

			if ($this->lastModified !==  null) {
				$request->setHeader(HeaderParameter::IF_MODIFIED_SINCE, $this->lastModified);
			}
		}
	}