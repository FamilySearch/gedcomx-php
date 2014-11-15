<?php

namespace Gedcomx\tests\Rs\Client;

class VocabElementListStateTest
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
}