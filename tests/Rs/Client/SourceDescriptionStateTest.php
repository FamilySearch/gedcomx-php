<?php


namespace Gedcomx\Tests\Rs\Client;


use Gedcomx\Common\Attribution;
use Gedcomx\Rs\Client\SourceDescriptionState;
use Gedcomx\Rs\Client\StateFactory;
use Gedcomx\Rs\Client\Util\HttpStatus;
use Gedcomx\Tests\ApiTestCase;

class SourceDescriptionStateTest extends ApiTestCase {

    /**
     * @link https://familysearch.org/developers/docs/api/sources/Create_Source_Description_usecase
     * @link https://familysearch.org/developers/docs/api/sources/Read_Source_Description_usecase
     * @link https://familysearch.org/developers/docs/api/sources/Update_Source_Description_usecase
     * @link https://familysearch.org/developers/docs/api/sources/Delete_Source_Description_usecase
     */
    public function testSourceDescriptionCRUD()
    {
        $this->collectionState(new StateFactory());
        /* CREATE */
        /** @var SourceDescriptionState $sourceState */
        $sourceState = $this->createSource();
        $this->assertAttributeEquals( HttpStatus::CREATED, "statusCode", $sourceState->getResponse(), $this->buildFailMessage(__METHOD__."(CREATE)", $sourceState));

        /* READ */
        $sourceState = $sourceState->get();
        $this->assertAttributeEquals( HttpStatus::OK, "statusCode", $sourceState->getResponse(), $this->buildFailMessage(__METHOD__."(READ)", $sourceState));

        /* UPDATE */
        $source = $sourceState->getSourceDescription();
        $source->setAttribution( new Attribution( array(
            "changeMessage" => $this->faker->sentence(6)
        )));
        $updated = $sourceState->update($source);
        $this->assertAttributeEquals( HttpStatus::NO_CONTENT, "statusCode", $updated->getResponse(), $this->buildFailMessage(__METHOD__."(UPDATE)", $updated));

        /* DELETE */
        $deleted = $sourceState->delete();
        $this->assertAttributeEquals( HttpStatus::NO_CONTENT, "statusCode", $deleted->getResponse(), $this->buildFailMessage(__METHOD__."(DELETE)", $deleted));
    }

} 