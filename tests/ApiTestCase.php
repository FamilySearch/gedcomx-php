<?php

namespace Gedcomx\Tests;

use Faker\Factory;
use Gedcomx\Extensions\FamilySearch\Rs\Client\Rel;
use Gedcomx\Rs\Client\StateFactory;
use Guzzle\Http\Message\EntityEnclosingRequest;

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

    /**
     * @param StateFactory|FamilyTreeStateFactory $factory
     * @return \Gedcomx\Rs\Client\CollectionState|\Gedcomx\Extensions\FamilySearch\Rs\Client\FamilyTree\FamilyTreeCollectionState
     */
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

    protected function buildFailMessage( $methodName, $stateObj )
    {
        $method = explode("\\",$methodName );
        $methodName = array_pop($method);
        $message = $methodName . " failed. Returned " . $stateObj->getResponse()->getStatusCode() . ".";
        $message .= "\n" . $stateObj->getRequest()->getMethod() . ": " . $stateObj->getResponse()->getEffectiveUrl();
        $message .= "\nContent-Type: " . $stateObj->getRequest()->getHeader("Content-Type");
        $message .= "\nAccept: " . $stateObj->getRequest()->getHeader("Accept");
        $message .= "\nRequest:" . (
            $stateObj->getRequest() instanceof EntityEnclosingRequest ?
                "\n".$stateObj->getRequest()->getBody() :
                " n/a"
        );
        $message .= "\nResponse:\n" . $stateObj->getResponse()->getBody();

        $warnings = $stateObj->getHeader('warning');
        if (!empty($warnings)) {
            $message .= "Warnings:\n";
            foreach ($warnings->values as $msg) {
                $message .= $msg . "\n";
            }
        }

        return $message;
    }

    protected  function createPerson($gender = null)
    {
        $person = PersonBuilder::buildPerson($gender);
        return $this->collectionState()->addPerson($person);
    }

    protected  function getPerson($pid = 'KWW6-H43', array $options = array()){
        $link = $this->collectionState()->getLink(Rel::PERSON);
        if ($link === null || $link->getTemplate() === null) {
            return null;
        }
        $uri = array(
            $link->getTemplate(),
            array(
                "pid" => $pid
            )
        );

        $args = array_merge(array($uri), $options);
        return call_user_func_array(array($this->collectionState(),"readPerson"), $args);
    }

    protected function createSource(){
        $source = SourceBuilder::buildSource();
        $link = $this->collectionState()->getLink(Rel::SOURCE_DESCRIPTIONS);
        if ($link === null || $link->getHref() === null) {
            return null;
        }

        return $this->collectionState()->addSourceDescription($source);
    }

}