<?php

namespace Gedcomx\Rs\Client\Util;

/**
 * This is the base search query builder class and provides helper functions for building syntactically correct search query strings.
 *
 * Class GedcomxBaseSearchQueryBuilder
 *
 * @package Gedcomx\Rs\Client\Util
 */
class GedcomxBaseSearchQueryBuilder {
	/**
	 * The array of search parameters this builder will use.
	 * @var array
	 */
	protected $parameters;

	/**
	 * Constructs a new instance of GedcomxBaseSearchQueryBuilder.
	 */
	public function __construct(){
		$this->parameters = array();
	}

	/**
	 * Builds the query string to use for searching.
	 *
	 * @return string
	 */
	public function build(){
		return implode(" ", $this->parameters);
	}
} 