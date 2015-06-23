<?php

namespace Gedcomx\Tests;

use Faker\Factory;
use Gedcomx\Extensions\FamilySearch\Platform\Tree\ChildAndParentsRelationship;
use Gedcomx\Extensions\FamilySearch\Rs\Client\FamilyTree\ChildAndParentsRelationshipState;
use Gedcomx\Extensions\FamilySearch\Rs\Client\FamilyTree\FamilyTreeStateFactory;
use Gedcomx\Extensions\FamilySearch\Rs\Client\FamilySearchClient;
use Gedcomx\Extensions\FamilySearch\Rs\Client\Rel;
use Gedcomx\Rs\Client\GedcomxApplicationState;
use Gedcomx\Rs\Client\PersonState;
use Gedcomx\Rs\Client\StateFactory;
use Gedcomx\Rs\Client\Util\HttpStatus;
use Gedcomx\Rs\Client\Options\QueryParameter;
use Guzzle\Http\Message\EntityEnclosingRequest;

abstract class ApiTestCase extends \PHPUnit_Framework_TestCase
{
    protected $testRootDir;
    protected $tempDir;
    /**
     * @var string
     */
    protected $apiEndpoint;
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
    /**
     * @var string
     */
    private $personId = 'KWW6-H43';
    /**
     * @var \Gedcomx\Rs\Client\GedcomxApplicationState[]
     */
    protected $dustbin;

	public function setUp()
    {
        $this->faker = Factory::create();
        $this->testRootDir = dirname(__DIR__) . DIRECTORY_SEPARATOR . "tests" . DIRECTORY_SEPARATOR;
        $this->tempDir = $this->testRootDir . "tmp" . DIRECTORY_SEPARATOR;
        ArtifactBuilder::setTempDir($this->tempDir);
	}

    public function tearDown()
    {
        if ($this->dustbin != null && is_array($this->dustbin)) {
            foreach ($this->dustbin as $s ){
                $s->delete();
            }
        }
        foreach (glob($this->tempDir . '*') as $file) {
            if ($file != '.gitignore') {
                unlink($file);
            }
        }
    }

    /**
     * Pass an arbitrary number of state objects to call delete on during tear down.
     *
     * @param \Gedcomx\Rs\Client\GedcomxApplicationState $state,...
     */
    public function queueForDelete($state){
        $toQueue = func_get_args();
        foreach ($toQueue as $state) {
            $this->dustbin[] = $state;
        }
    }

    /**
     * @param StateFactory|FamilyTreeStateFactory $factory
     * @param string $uri
     * @param string $method
     * @param \Guzzle\Http\Client $client
     *
     * @throws \RuntimeException
     * @return \Gedcomx\Rs\Client\CollectionState|\Gedcomx\Extensions\FamilySearch\Rs\Client\FamilyTree\FamilyTreeCollectionState
     */
    protected function collectionState($factory = null, $uri = null, $method = "GET", $client = null){
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
                ->newCollectionState($uri, $method, $client)
                ->authenticateViaOAuth2Password(
                    SandboxCredentials::USERNAME,
                    SandboxCredentials::PASSWORD,
                    SandboxCredentials::API_KEY);
            $this->currentFactory = $factory;

            return $this->collectionState;
        }
    }

    /**
     * @param \Gedcomx\Rs\Client\GedcomxApplicationState $state
     *
     * @return \Gedcomx\Rs\Client\GedcomxApplicationState
     */
    protected function authorize(GedcomxApplicationState $state)
    {
        return $state->authenticateViaOAuth2Password(
            SandboxCredentials::USERNAME,
            SandboxCredentials::PASSWORD,
            SandboxCredentials::API_KEY
        );
    }

    protected function buildFailMessage( $methodName, $stateObj )
    {
        $method = explode("\\",$methodName );
        $methodName = array_pop($method);
        $code = $stateObj->getResponse()->getStatusCode();
        $message = $methodName . " failed. Returned " . $code . ":" . HttpStatus::getText($code);
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
            foreach ($warnings->toArray() as $msg) {
                $message .= $msg . "\n";
            }
        }

        return $message;
    }
    
    protected function createFamilySearchClient($mods = array())
    {
        return new FamilySearchClient(array(
            'environment' => 'sandbox',
            'clientId' => SandboxCredentials::API_KEY,
            'redirectURI' => SandboxCredentials::REDIRECT_URI,
            'pendingModifications' => $mods
        ));
    }
    
    protected function createAuthenticatedFamilySearchClient($mods = array())
    {
        return $this->createFamilySearchClient($mods)
            ->authenticateViaOAuth2Password(
                SandboxCredentials::USERNAME, 
                SandboxCredentials::PASSWORD
            );
    }

    protected  function createPerson($gender = null)
    {
        $person = PersonBuilder::buildPerson($gender);
        $state = $this->collectionState()->addPerson($person);
        $this->queueForDelete($state);

        return $state;
    }

    protected function getPersonId(){
        return $this->personId;
    }

    protected  function getPerson($pid = null, array $options = array()){
        if ($pid == null) {
            $pid = $this->personId;
        }
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
        $source = SourceBuilder::newSource();
        $link = $this->collectionState()->getLink(Rel::SOURCE_DESCRIPTIONS);
        if ($link === null || $link->getHref() === null) {
            return null;
        }

        $state = $this->collectionState()->addSourceDescription($source);
        $this->queueForDelete($state);

        return $state;
    }
    
    protected function createCacheBreakerQueryParam()
    {
        return new QueryParameter(true, '_', $this->faker->randomNumber);
    }

    /**
     * Initialize a ChildAndParentRelationship for tests requiring one.
     *
     * @return \Gedcomx\Extensions\FamilySearch\Rs\Client\FamilyTree\ChildAndParentsRelationshipState
     * @throws \Gedcomx\Rs\Client\Exception\GedcomxApplicationException
     */
    protected  function createRelationship()
    {
        /** @var PersonState $father */
        $father = $this->createPerson('male');
        $this->assertEquals(
            HttpStatus::CREATED,
            $father->getResponse()->getStatusCode(),
            $this->buildFailMessage(__METHOD__.'(createFather)', $father)
        );
        /** @var PersonState $mother */
        $mother = $this->createPerson('female');
        $this->assertEquals(
            HttpStatus::CREATED,
            $mother->getResponse()->getStatusCode(),
            $this->buildFailMessage(__METHOD__.'(createMother)', $mother)
        );
        /** @var PersonState $child */
        $child = $this->createPerson();
        $this->assertEquals(
            HttpStatus::CREATED,
            $child->getResponse()->getStatusCode(),
            $this->buildFailMessage(__METHOD__.'(createChild)', $child)
        );
        $this->queueForDelete($father,$child,$mother);

        $rel = new ChildAndParentsRelationship();
        $rel->setChild($child->getResourceReference());
        $rel->setFather($father->getResourceReference());
        $rel->setMother($mother->getResourceReference());

        /** @var ChildAndParentsRelationshipState $rState */
        $rState = $this->collectionState()->addChildAndParentsRelationship($rel);
        $this->assertEquals(
            HttpStatus::CREATED,
            $rState->getResponse()->getStatusCode(),
            $this->buildFailMessage(__METHOD__.'(createFamily)', $rState)
        );
        $this->queueForDelete($rState);

        return $rState;
    }

}