<?php

namespace Gedcomx\tests\Rs\Client;

class VocabElementStateTest
{
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
}