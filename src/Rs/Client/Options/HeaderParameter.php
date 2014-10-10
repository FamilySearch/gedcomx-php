<?php

	namespace Gedcomx\Rs\Client\Options;

	use Guzzle\Http\Message\Request;

	class HeaderParameter implements StateTransitionOption
	{
		const LANG = "Accept-Language";
		const LOCALE = self::LANG;
		const IF_NONE_MATCH = "If-None-Match";
		const IF_MODIFIED_SINCE = "If-Modified-Since";
		const IF_MATCH = "If-Match";
		const IF_UNMODIFIED_SINCE = "If-Unmodified-Since";
		const ETAG = "ETag";
		const LAST_MODIFIED = "Last-Modified";

		private $replace;
		private $name;
		private $value;

		/**
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
         * @param Request $request
         */
		public function apply(Request $request)
		{
			if ($this->replace) {
				$request->setHeader($this->name, $this->value);
			} else {
				$request->addHeader($this->name, $this->value);
			}
		}

        /**
         * @param string $value
         *
         * @return HeaderParameter
         */
		public static function lang($value)
		{
			return new HeaderParameter(true, self::LANG, $value);
		}

        /**
         * @param string $value
         *
         * @return HeaderParameter
         */
		public static function locale($value)
		{
			return new HeaderParameter(true, self::LOCALE, $value);
		}

	}