<?php


class CollectionStateTest extends ApiTestCase{

	public function testCanReadPersons(){
		$stateFactory = new StateFactory();
		$persons = $stateFactory
			->newCollectionState($this->apiEndpoint)
			->authenticateViaOAuth2Password(
				$this->apiCredentials->username,
				$this->apiCredentials->password,
				$this->apiCredentials->apiKey)
			->readPersons();


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
			->readPersons();


		$this->assertNotNull($persons->getPerson());
	}
} 