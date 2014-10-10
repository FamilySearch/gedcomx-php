<?php

	namespace Gedcomx\Rs\Client\Options;

	use Gedcomx\Rs\Client\GedcomxApplicationState;
	use Guzzle\Http\Message\Request;

	class Preconditions implements StateTransitionOption
	{
		private $etag;
		private $lastModified;

        public function setLastModified(\DateTime $lastModified)
        {
            $this->lastModified = $lastModified;
        }
        /**
         * @param string $etag
         */
        public function setEtag($etag)
        {
            $this->etag = $etag;
        }
        /**
         * @param string    $etag
         * @param \DateTime $lastModified
         */
		public function setBothConditions($etag, \DateTime $lastModified)
		{
            $this->etag = $etag;
            $this->lastModified = $lastModified;
		}

        /**
         * @param GedcomxApplicationState $state
         */
        public function setState(GedcomxApplicationState $state)
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