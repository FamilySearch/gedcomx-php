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
use Gedcomx\Tests\TestBuilder;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Handler\CurlHandler;
use GuzzleHttp\Client;
use GuzzleHttp\Middleware;
use GuzzleHttp\MessageFormatter;
use GuzzleHttp\Psr7\Request;

abstract class ApiTestCase extends \PHPUnit_Framework_TestCase
{
    
    /**
     * @var string
     */
    protected $testRootDir;
    
    /**
     * @var string
     */
    protected $tempDir;
    
    /**
     * @var string
     */
    protected $filesDir;
    
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
        TestBuilder::seed(1123546);
        $this->testRootDir = dirname(__DIR__) . DIRECTORY_SEPARATOR . "tests" . DIRECTORY_SEPARATOR;
        $this->tempDir = $this->testRootDir . "tmp" . DIRECTORY_SEPARATOR;
        $this->filesDir = $this->testRootDir . "files" . DIRECTORY_SEPARATOR;
        ArtifactBuilder::setTempDir($this->tempDir);
    }

    public function tearDown()
    {
        foreach (glob($this->tempDir . '*') as $file) {
            if ($file != '.gitignore') {
                unlink($file);
            }
        }
    }
    
    protected function loadJson($filename)
    {
        return json_decode(file_get_contents($this->filesDir . $filename), true);
    }

    protected function createPerson($gender = null)
    {
        $person = PersonBuilder::buildPerson($gender);
        $state = $this->collectionState()->addPerson($person);
        $this->queueForDelete($state);
        return $state;
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
        return new QueryParameter(true, '_', TestBuilder::faker()->randomNumber);
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
            $father->getStatus(),
            $this->buildFailMessage(__METHOD__.'(createFather)', $father)
        );
        /** @var PersonState $mother */
        $mother = $this->createPerson('female');
        $this->assertEquals(
            HttpStatus::CREATED,
            $mother->getStatus(),
            $this->buildFailMessage(__METHOD__.'(createMother)', $mother)
        );
        /** @var PersonState $child */
        $child = $this->createPerson();
        $this->assertEquals(
            HttpStatus::CREATED,
            $child->getStatus(),
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
            $rState->getStatus(),
            $this->buildFailMessage(__METHOD__.'(createFamily)', $rState)
        );
        $this->queueForDelete($rState);

        return $rState;
    }

}