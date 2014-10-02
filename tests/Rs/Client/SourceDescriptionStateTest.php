<?php


namespace Gedcomx\Tests\Rs\Client;


use Gedcomx\Tests\ApiTestCase;
use Gedcomx\Tests\SourceBuilder;

class SourceDescriptionStateTest extends ApiTestCase {

    private static $sourceState;

    public function testCanCreateSourceDescription(){
        $sourceData = SourceBuilder::buildSourceData();
        self::$sourceState = $this->collectionState
            ->addSourceDescription($sourceData);

        $this->assertAttributeEquals( "201", "statusCode", self::$sourceState->getResponse() );

    }

} 