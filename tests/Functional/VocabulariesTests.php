<?php

namespace Gedcomx\Tests\Functional;

use Gedcomx\Tests\ApiTestCase;

class VocabulariesTests extends ApiTestCase
{
    /**
     * @link https://familysearch.org/developers/docs/api/cv/Read_Vocabulary_List_usecase
     */
    public function testReadVocabularyList()
    {
        $factory = new FamilySearchStateFactory();
        /** @var \Gedcomx\Extensions\FamilySearch\Rs\Client\FamilySearchPlaces $collection */
        $this->collection = $factory->newPlacesState()
            ->authenticateViaOAuth2Password(
                $this->apiCredentials->username,
                $this->apiCredentials->password,
                $this->apiCredentials->apiKey
            );
        $listState = $this->collection->readPlaceTypes();
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
        $this->collection = $factory->newPlacesState()
            ->authenticateViaOAuth2Password(
                $this->apiCredentials->username,
                $this->apiCredentials->password,
                $this->apiCredentials->apiKey
            );
        $listState = $this->collection->readPlaceTypes();
        $elements = $listState->getVocabElementList()->getElements();
        $type = $this->collection->readPlaceTypeById($elements->getId());

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
        $this->assertTrue(false, "This test not yet implemented"); //todo
    }
}