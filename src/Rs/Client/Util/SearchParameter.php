<?php

	namespace Gedcomx\Rs\Client\Util;

	use Gedcomx\Rs\Client\Exception\NullValueException;

	class SearchParameter
	{
		private $prefix;
		private $name;
		private $value;
		private $exact;

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

		function getPrefix()
		{
			return $this->prefix;
		}

		function getName()
		{
			return $this->name;
		}

		function getValue()
		{
			return $this->value;
		}

		function isExact()
		{
			return $this->exact;
		}

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