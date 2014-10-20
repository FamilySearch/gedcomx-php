<?php

namespace Gedcomx\Tests;

use Faker\Factory;
use Gedcomx\Rs\Client\StateFactory;

abstract class ApiTestCase extends \PHPUnit_Framework_TestCase{

    /**
     * @var string
     */
    protected $apiEndpoint;
    /**
     * @var stdClass
     */
    protected $apiCredentials;
    /**
     * @var \Gedcomx\Rs\Client\StateFactory
     */
    protected $currentFactory;
    /**
     * @var \Faker\Generator
     */
    protected $faker;
    /**
     * @var \Gedcomx\Rs\Client\CollectionState
     */
    private $collectionState;

	public function setUp()
    {
        $this->faker = Factory::create();

		$this->apiCredentials = (object)array(
			'username' => "sdktester",
			'password' => "1234sdkpass",
			'apiKey' => "WCQY-7J1Q-GKVV-7DNM-SQ5M-9Q5H-JX3H-CMJK"
		);
	}

    protected function collectionState($factory = null){
        if( $factory == null ){
            if( $this->collectionState == null ){
                throw new \RuntimeException("Collection state is null");
            } else {
                return $this->collectionState;
            }
        }
        if (get_class($this->currentFactory) == get_class($factory) && $this->collectionState != null) {
            return $this->collectionState;
        } else {
            $this->collectionState = $factory
                ->newCollectionState()
                ->authenticateViaOAuth2Password(
                    $this->apiCredentials->username,
                    $this->apiCredentials->password,
                    $this->apiCredentials->apiKey);
            $this->currentFactory = $factory;

            return $this->collectionState;
        }
    }
}