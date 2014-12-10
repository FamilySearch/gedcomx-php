<?php

	namespace Gedcomx\Rs\Client\Util;

	use Gedcomx\Rs\Client\Exception\NullValueException;

	/**
	 * Represents a generic search parameter.
	 *
	 * Class SearchParameter
	 *
	 * @package Gedcomx\Rs\Client\Util
	 */
	class SearchParameter
	{
		private $prefix;
		private $name;
		private $value;
		private $exact;

		/**
		 * Constructs a new search parameter using the specified parameters.
		 * @param $prefix
		 * @param $name
		 * @param $value
		 * @param $exact
		 *
		 * @throws \Gedcomx\Rs\Client\Exception\NullValueException
		 */
		function __construct($prefix, $name, $value, $exact)
		{
			if ($name == null) {
				throw new NullValueException("Parameter 'name' cannot not be null");
			}

			$this->prefix = $prefix;
			$this->exact = $exact;
			$this->value = $value;
			$this->name = $name;
		}

		/**
		 * Gets the prefix to apply to the search parameter. This is used for controlling whether a parameter is required or not.
		 * The prefix can take on three forms:
		 *     "+": The parameter search value should be found in the search results
		 *     null: The parameter search filter is optional
		 *     "-": The parameter search value should not found in the search results (i.e., perform a NOT seaarch)
		 *
		 * @return mixed
		 */
		function getPrefix()
		{
			return $this->prefix;
		}

		/**
		 * Gets the name of the current search parameter.
		 *
		 * @return mixed
		 */
		function getName()
		{
			return $this->name;
		}

		/**
		 * Gets the value of the current search parameter.
		 *
		 * @return mixed
		 */
		function getValue()
		{
			return $this->value;
		}

		/**
		 * Gets a value indicating whether the current search parameter requires exact value match results.
		 * If this value is true, search results will only return values that exactly match the search parameter value.
		 *
		 * @return mixed
		 */
		function isExact()
		{
			return $this->exact;
		}

		/**
		 * Returns a string that is a syntactically conformant search query that can be used in REST API search requests.
		 *
		 * @return string
		 */
		function __toString()
		{
			$stringOut = '';
			if ($this->prefix != null) {
				$stringOut .= $this->prefix;
			}
			$stringOut .= $this->name;
			if ($this->value != null) {
				$stringOut .= ':';
				$stringEscaped = str_replace(array('\n', '\t', '\f', '\013'), ' ', $this->value);
				$stringEscaped = str_replace('"', '\"', $stringEscaped);
				$quote = strpos($stringEscaped, " ") !== false ? '"' : '';
				$stringOut .= $quote . $stringEscaped . $quote;
				if (!$this->exact) {
					$stringOut .= '~';
				}
			}

			return $stringOut;
		}
	}