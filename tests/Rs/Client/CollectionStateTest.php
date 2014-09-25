<?php

namespace Gedcomx\Tests\Rs\Client;

use Gedcomx\Tests\ApiTestCase;
use Gedcomx\Rs\Client\StateFactory;

class CollectionStateTest extends ApiTestCase{

	public function testSearchForPersons(){
		$stateFactory = new StateFactory();
		$persons = $stateFactory
			->newCollectionState($this->apiEndpoint)
			->authenticateViaOAuth2Password(
				$this->apiCredentials->username,
				$this->apiCredentials->password,
				$this->apiCredentials->apiKey);

		$this->assertNotNull($persons->getPerson());
	}

	public function testCanReadPerson(){
		$stateFactory = new StateFactory();
		$persons = $stateFactory
			->newCollectionState($this->apiEndpoint)
			->authenticateViaOAuth2Password(
				$this->apiCredentials->username,
				$this->apiCredentials->password,
				$this->apiCredentials->apiKey)
			->readPerson('');

		$this->assertNotNull($persons->getPerson());
	}
} 