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
     * @vcr VocabulariesTests/testReadVocabularyTermAlternateLanguage.json
     * @link https://familysearch.org/developers/docs/api/cv/Read_Vocabulary_Term,_Alternate_Language_usecase
     */
    public function testReadVocabularyTermAlternateLanguage()
    {
        $lang = "fr";
        $factory = new FamilySearchStateFactory();
        /** @var \Gedcomx\Extensions\FamilySearch\Rs\Client\FamilySearchPlaces $collection */
        $collection = $factory->newPlacesState()
            ->authenticateViaOAuth2Password(
                SandboxCredentials::USERNAME,
                SandboxCredentials::PASSWORD,
                SandboxCredentials::API_KEY
            );
        $listState = $collection->readPlaceTypes();
        $this->assertNotnull($listState);
        $this->assertNotNull($listState->ifSuccessful());
        $this->assertNotNull($listState->getVocabElementList());
        $elements = $listState->getVocabElementList()->getElements();
        $this->assertnotnull($elements);

        $inFrench = new HeaderParameter(true,'Accept-Language',$lang);
        $type = $collection->readPlaceTypeById(array_shift($elements)->getId(), $inFrench);
        $this->assertNotNull($type);

        $this->assertEquals(
            HttpStatus::OK,
            $type->getResponse()->getStatusCode(),
            $this->buildFailMessage(__METHOD__,$type)
        );
        $element = $type->getVocabElement();
        $this->assertNotEmpty($element);
        $descriptions = $element->getDescriptions();
        $labels = $element->getLabels();
        $this->assertNotNull($descriptions);
        $this->assertNotNull($labels);
        $this->assertGreaterThan(0, count($descriptions));
        $this->assertGreaterThan(0, count($labels));
        /** @var \Gedcomx\Common\TextValue $desc */
        $desc = array_shift($descriptions);
        /** @var \Gedcomx\Common\TextValue $label */
        $label = array_shift($labels);
        $this->assertEquals($lang, $desc->getLang());
        $this->assertEquals($lang, $label->getLang());
    }
}