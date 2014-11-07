<?php

namespace Gedcomx\Tests\Rs\Client;

use Gedcomx\Rs\Client\Rel;
use Gedcomx\Rs\Client\StateFactory;
use Gedcomx\Rs\Client\Util\HttpStatus;
use Gedcomx\Source\SourceDescription;
use Gedcomx\Tests\ApiTestCase;
use Gedcomx\Tests\SourceBuilder;

class SourceDescriptionsStateTest extends ApiTestCase {

    /**
     * @link https://familysearch.org/developers/docs/api/sources/Create_Source_Description_usecase
     */
    public function testCreateSourceDescriptionCRUD()
    {
        $this->collectionState(new StateFactory());
        /** @var SourceDescription $source */
        $source = SourceBuilder::newSource();
        $link = $this->collectionState()->getLink(Rel::SOURCE_DESCRIPTIONS);
        if ($link === null || $link->getHref() === null) {
            return null;
        }

        $sourceState = $this->collectionState()->addSourceDescription($source);
        $this->assertAttributeEquals( HttpStatus::CREATED, "statusCode", $sourceState->getResponse(), $this->buildFailMessage(__METHOD__."(CREATE)", $sourceState));
    }

    /**
     * @link https://familysearch.org/developers/docs/api/sources/Create_User-Uploaded_Source_usecase
     */
    public function testCreateUserUploadedSource(){
        $this->collectionState(new StateFactory());
        /** @var SourceDescription $source */
        $source = SourceBuilder::newSource();
        //todo: create a memory once the Memories code has been implemented
        $source->setAbout('https://sandbox.familysearch.org/platform/memories/memories/103820');
    }
} 