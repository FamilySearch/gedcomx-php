<?php

	namespace Gedcomx\Rs\Client\Options;

	use Guzzle\Http\Message\Request;

	interface StateTransitionOption {

        /**
         * An option for modifying a state transition.
         *
         * @param Request $request The request object the Option will modify
         */
		public function apply(Request $request);

	}