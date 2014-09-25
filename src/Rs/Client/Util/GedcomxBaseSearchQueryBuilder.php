<?php

namespace Gedcomx\Rs\Client\Util;

class GedcomxBaseSearchQueryBuilder {

	protected $parameters;

	public function __construct(){
		$this->parameters = array();
	}

	/**
	 * @return string
	 */
	public function build(){
		return implode(" ", $this->parameters);
	}
} 