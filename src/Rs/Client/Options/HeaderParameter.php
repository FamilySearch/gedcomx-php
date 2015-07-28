<?php

	namespace Gedcomx\Rs\Client\Options;

	use GuzzleHttp\Psr7\Request;

	/**
	 * This is a helper class for managing headers in REST API requests.
	 *
	 * Class HeaderParameter
	 *
	 * @package Gedcomx\Rs\Client\Options
	 */
	class HeaderParameter implements StateTransitionOption
	{
		/**
		 * The accept language header
		 */
		const LANG = "Accept-Language";
		/**
		 * The locale header
		 */
		const LOCALE = self::LANG;
		/**
		 * The if-none-match header
		 */
		const IF_NONE_MATCH = "If-None-Match";
		/**
		 * The if-modified-since header
		 */
		const IF_MODIFIED_SINCE = "If-Modified-Since";
		/**
		 * The if-match header
		 */
		const IF_MATCH = "If-Match";
		/**
		 * The if-unmodified-since header
		 */
		const IF_UNMODIFIED_SINCE = "If-Unmodified-Since";
		/**
		 * The ETag (entity tag) header
		 */
		const ETAG = "ETag";
		/**
		 * The last-modified header
		 */
		const LAST_MODIFIED = "Last-Modified";

		private $replace;
		private $name;
		private $value;

		/**
		 * Constructs a header parameter with the specified values.
		 *
		 * @param boolean $replace
		 * @param string $name
		 * @param string $value,...
		 */
		public function __construct($replace, $name, $value)
		{
			$this->replace = $replace;
			$this->name = $name;
			if (func_num_args() > 3) {
				$args = func_get_args();
				array_shift(array_shift($args));
				$this->value = $args;
			} else {
				$this->value = $value;
			}
		}

        /**
		 * This method adds the current header parameters to the REST API request.
		 *
         * @param Request $request
         */
		public function apply(Request $request)
		{
			if ($this->replace) {
				$request->withHeader($this->name, $this->value);
			} else {
				$request->addHeader($this->name, $this->value);
			}
		}

        /**
		 * Creates an accept-language header parameter.
		 *
         * @param string $value
         *
         * @return HeaderParameter
         */
		public static function lang($value)
		{
			return new HeaderParameter(true, self::LANG, $value);
		}

        /**
		 * Creates an accept-language header parameter.
		 *
         * @param string $value
         *
         * @return HeaderParameter
         */
		public static function locale($value)
		{
			return new HeaderParameter(true, self::LOCALE, $value);
		}
	}