<?php

namespace Gedcomx\Tests\Functional;

use Gedcomx\Extensions\FamilySearch\Rs\Client\FamilySearchStateFactory;
use Gedcomx\Rs\Client\Options\HeaderParameter;
use Gedcomx\Rs\Client\Util\HttpStatus;
use Gedcomx\Tests\ApiTestCase;
use Gedcomx\Tests\SandboxCredentials;

class VocabulariesTests extends ApiTestCase
{
    /**
     * @link https://familysearch.org/developers/docs/api/cv/Read_Vocabulary_List_usecase
     */
    public function testReadVocabularyList()
    {
        $factory = new FamilySearchStateFactory();
        /** @var \Gedcomx\Extensions\FamilySearch\Rs\Client\FamilySearchPlaces $collection */
        $collection = $factory->newPlacesState()
            ->authenticateViaOAuth2Password(
                SandboxCredentials::USERNAME,
                SandboxCredentials::PASSWORD,
                SandboxCredentials::API_KEY
            );
        $listState = $collection->readPlaceTypes();
        $elements = $listState->getVocabElementList()->getElements();

        $this->assertEquals(
            HttpStatus::OK,
            $listState->getResponse()->getStatusCode(),
            $this->buildFailMessage(__METHOD__,$listState)
        );
        $this->assertNotEmpty($elements);
    }

    /**
     * @link https://familysearch.org/developers/docs/api/cv/Controlled_Vocabulary_Term_resource
     */
    public function testControlledVocabularyTerm()
    {
        $factory = new FamilySearchStateFactory();
        /** @var \Gedcomx\Extensions\FamilySearch\Rs\Client\FamilySearchPlaces $collection */
        $collection = $factory->newPlacesState()
            ->authenticateViaOAuth2Password(
                SandboxCredentials::USERNAME,
                SandboxCredentials::PASSWORD,
                SandboxCredentials::API_KEY
            );
        $listState = $collection->readPlaceTypes();
        $elements = $listState->getVocabElementList()->getElements();
        $type = $collection->readPlaceTypeById(array_shift($elements)->getId());

        $this->assertEquals(
            HttpStatus::OK,
            $type->getResponse()->getStatusCode(),
            $this->buildFailMessage(__METHOD__,$type)
        );
        $this->assertNotEmpty($type->getVocabElement());
    }

    /**
     * @link https://familysearch.org/developers/docs/api/cv/Read_Vocabulary_Term,_Alternate_Language_usecase
     */
    public function testReadVocabularyTermAlternateLanguage()
    {
        $factory = new FamilySearchStateFactory();
        /** @var \Gedcomx\Extensions\FamilySearch\Rs\Client\FamilySearchPlaces $collection */
        $collection = $factory->newPlacesState()
            ->authenticateViaOAuth2Password(
                SandboxCredentials::USERNAME,
                SandboxCredentials::PASSWORD,
                SandboxCredentials::API_KEY
            );
        $listState = $collection->readPlaceTypes();
        $elements = $listState->getVocabElementList()->getElements();

        $inFrench = new HeaderParameter(true,'Accept-Language','fr');
        $type = $collection->readPlaceTypeById(array_shift($elements)->getId(), $inFrench);

        $this->assertEquals(
            HttpStatus::OK,
            $type->getResponse()->getStatusCode(),
            $this->buildFailMessage(__METHOD__,$type)
        );
        $this->assertNotEmpty($type->getVocabElement());
    }
}