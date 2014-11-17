<?php 

namespace Gedcomx\Tests\Extensions\FamilySearch\Rs\Client;

use Gedcomx\Extensions\FamilySearch\Rs\Client\FamilySearchStateFactory;
use Gedcomx\Rs\Client\Util\GedcomxPlaceSearchQueryBuilder;
use Gedcomx\Rs\Client\Util\HttpStatus;
use Gedcomx\Tests\ApiTestCase;

class FamilySearchPlacesTest extends ApiTestCase
{
    /**
     * @var
     */
    private $vocabListState;

    /**
     * @var
     */
    private $vocabElements;

    /**
     * @link https://familysearch.org/developers/docs/api/places/Search_For_Places_usecase
     */
    public function testSearchForPlaces()
    {
        $query = new GedcomxPlaceSearchQueryBuilder();
        $query->name("Paris");
        $factory = new FamilySearchStateFactory();

        /** @var \Gedcomx\Extensions\FamilySearch\Rs\Client\FamilySearchPlaces $collection */
        $collection = $factory->newPlacesState()
                              ->authenticateViaOAuth2Password(
                                  $this->apiCredentials->username,
                                  $this->apiCredentials->password,
                                  $this->apiCredentials->apiKey);
        $results = $collection->searchForPlaces($query);
        $this->assertEquals(HttpStatus::OK, $results->getResponse()->getStatusCode());
        $this->assertNotEmpty($results->getResults());
    }

    /**
     * @link https://familysearch.org/developers/docs/api/places/Search_For_Places_usecase
     */
    public function testSearchForPlacesDirectlyUnderAJurisdiction()
    {
        $query = new GedcomxPlaceSearchQueryBuilder();
        $query->name("Paris");
        $query->parentId('442102', true, true);
        $factory = new FamilySearchStateFactory();

        /** @var \Gedcomx\Extensions\FamilySearch\Rs\Client\FamilySearchPlaces $collection */
        $collection = $factory->newPlacesState()
                              ->authenticateViaOAuth2Password(
                                  $this->apiCredentials->username,
                                  $this->apiCredentials->password,
                                  $this->apiCredentials->apiKey);
        $results = $collection->searchForPlaces($query);
        $this->assertEquals(HttpStatus::OK, $results->getResponse()->getStatusCode());
        $this->assertNotEmpty($results->getResults());
    }

    /**
     * @link https://familysearch.org/developers/docs/api/places/Search_For_Places_Under_a_Jurisdiction_usecase
     */
    public function testSearchForPlacesUnderAJurisdiction()
    {
        $query = new GedcomxPlaceSearchQueryBuilder();
        $query->name("Paris");
        $query->parentId('329', false, true);
        $factory = new FamilySearchStateFactory();

        /** @var \Gedcomx\Extensions\FamilySearch\Rs\Client\FamilySearchPlaces $collection */
        $collection = $factory->newPlacesState()
                              ->authenticateViaOAuth2Password(
                                  $this->apiCredentials->username,
                                  $this->apiCredentials->password,
                                  $this->apiCredentials->apiKey);
        $results = $collection->searchForPlaces($query);
        $this->assertEquals(HttpStatus::OK, $results->getResponse()->getStatusCode());
        $this->assertNotEmpty($results->getResults());
    }

    /**
     * @link https://familysearch.org/developers/docs/api/places/Place_Types_resource
     */
    public function testReadPlaceTypes()
    {
        $this->fetchVocabElements();

        $this->assertEquals(
            HttpStatus::OK,
            $this->vocabListState->getResponse()->getStatusCode(),
            $this->buildFailMessage(__METHOD__,$this->vocabListState)
        );
        $this->assertNotEmpty($this->vocabElements);
    }

    /**
     * @link https://familysearch.org/developers/docs/api/places/Read_Place_Type_usecase
     */
    public function testReadPlaceType()
    {
        $this->fetchVocabElements();

        $type = $this->collection->readPlaceTypeById($this->vocabElements[0]->getId());

        $this->assertEquals(
            HttpStatus::OK,
            $type->getResponse()->getStatusCode(),
            $this->buildFailMessage(__METHOD__,$type)
        );
        $this->assertNotEmpty($type->getVocabElement());
    }

    /**
     *
     */
    public function testPlaceTypeGroups()
    {
        $factory = new FamilySearchStateFactory();
        /** @var \Gedcomx\Extensions\FamilySearch\Rs\Client\FamilySearchPlaces $collection */
        $collection = $factory->newPlacesState()
            ->authenticateViaOAuth2Password(
                $this->apiCredentials->username,
                $this->apiCredentials->password,
                $this->apiCredentials->apiKey
            );

        /**
         * Read the list of group types.
         * @link https://familysearch.org/developers/docs/api/places/Read_Place_Type_Groups_usecase
         */
        $groupTypesState = $collection->readPlaceTypeGroups();
        $this->assertEquals(
            HttpStatus::OK,
            $groupTypesState->getResponse()->getStatusCode(),
            $this->buildFailMessage(__METHOD__."(List group types)",$groupTypesState)
        );
        $groupTypes = $groupTypesState->getVocabElementList()->getElements();
        $this->assertNotEmpty($groupTypes);
        /**
         * Read the list of types associated with a group.
         * @link https://familysearch.org/developers/docs/api/places/Read_Place_Type_Group_usecase
         */
        $groupTypeState = $collection->readPlaceTypeGroupById($groupTypes[0]->getId());
        $this->assertEquals(
            HttpStatus::OK,
            $groupTypeState->getResponse()->getStatusCode(),
            $this->buildFailMessage(__METHOD__."(List groups in type)",$groupTypeState)
        );
        $groups = $groupTypeState->getVocabElementList()->getElements();
        $this->assertNotEmpty($groups);
        /**
         * Read a group from the list.
         * @link https://familysearch.org/developers/docs/api/places/Place_Group_resource
         */
        $groupState = $collection->readPlaceGroupById(30);
        $this->assertEquals(
            HttpStatus::OK,
            $groupState->getResponse()->getStatusCode(),
            $this->buildFailMessage(__METHOD__."(Get group)",$groupState)
        );
        $this->assertNotEmpty($groupState->getPlaceGroup());
    }

    private function fetchVocabElements(){
        if ($this->vocabElements == null) {
            $factory = new FamilySearchStateFactory();
            /** @var \Gedcomx\Extensions\FamilySearch\Rs\Client\FamilySearchPlaces $collection */
            $this->collection = $factory->newPlacesState()
                ->authenticateViaOAuth2Password(
                    $this->apiCredentials->username,
                    $this->apiCredentials->password,
                    $this->apiCredentials->apiKey
                );
            $this->vocabListState = $this->collection->readPlaceTypes();
            $this->vocabElements = $this->vocabListState->getVocabElementList()->getElements();
        }
    }
}