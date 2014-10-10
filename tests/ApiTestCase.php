<?php

namespace Gedcomx\Tests;

use Faker\Factory;
use Gedcomx\Rs\Client\StateFactory;

abstract class ApiTestCase extends \PHPUnit_Framework_TestCase{

	protected $apiEndpoint;
	protected $apiCredentials;
    protected $collectionState;
    protected $faker;

	public function setUp()
    {
        $this->faker = Factory::create();

		$this->apiEndpoint = 'https://sandbox.familysearch.org/platform/collections/tree';
		$this->apiCredentials = (object)array(
			'username' => "sdktester",
			'password' => "1234sdkpass",
			'apiKey' => "WCQY-7J1Q-GKVV-7DNM-SQ5M-9Q5H-JX3H-CMJK"
		);
        $stateFactory = new StateFactory();
        $this->collectionState = $stateFactory
            ->newCollectionState($this->apiEndpoint)
            ->authenticateViaOAuth2Password(
                $this->apiCredentials->username,
                $this->apiCredentials->password,
                $this->apiCredentials->apiKey);
	}
} 