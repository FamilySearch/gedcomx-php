<?php

	namespace Gedcomx\Rs\Client\Options;

	use Guzzle\Http\Message\Request;

	/**
	 * Defines a method to manipulate and apply options to a REST API request before execution.
	 *
	 * Interface StateTransitionOption
	 *
	 * @package Gedcomx\Rs\Client\Options
	 */
	interface StateTransitionOption {

        /**
         * An option for modifying a state transition.
         *
         * @param Request $request The request object the Option will modify
         */
		public function apply(Request $request);

	}