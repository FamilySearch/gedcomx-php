<?php

namespace Gedcomx\Tests;

abstract class ApiTestCase extends \PHPUnit_Framework_TestCase{

	protected $apiEndpoint;
	protected $apiCredentials;

	public function setUp(){
		$this->apiEndpoint = 'https://sandbox.familysearch.org/platform/collections/tree';
		$this->apiCredentials = (object)array(
			'username' => "sdktester",
			'password' => "1234sdkpass",
			'apiKey' => "WCQY-7J1Q-GKVV-7DNM-SQ5M-9Q5H-JX3H-CMJK"
		);
	}
} 