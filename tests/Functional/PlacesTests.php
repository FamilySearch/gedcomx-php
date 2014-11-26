<?php 

namespace Gedcomx\Tests\Functional;

use Gedcomx\Extensions\FamilySearch\Rs\Client\FamilySearchStateFactory;
use Gedcomx\Rs\Client\Util\GedcomxPlaceSearchQueryBuilder;
use Gedcomx\Rs\Client\Util\HttpStatus;
use Gedcomx\Tests\ApiTestCase;
use Gedcomx\Tests\SandboxCredentials;

class PlacesTests extends ApiTestCase
{
    private $vocabListState;
    private $vocabElements;
    /**
     * @var \Gedcomx\Extensions\FamilySearch\Rs\Client\FamilySearchPlaces
     */
    private $collection;

    /**
     * @link https://familysearch.org/developers/docs/api/places/Read_Place_usecase
     */
    public function testReadPlace()
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
        $places = $results->getResults();
        $description = $results->readPlaceDescription($places[0]);
        $place = $description->readPlace();
        $this->assertEquals(
            HttpStatus::OK,
            $place->getResponse()->getStatusCode(),
            $this->buildFailMessage(__METHOD__, $place)
        );
        $this->assertNotEmpty($description->getPlaceDescription());
    }

    /**
     * @link https://familysearch.org/developers/docs/api/places/Read_Place_Description_usecase
     */
    public function testReadPlaceDescription()
    {
        $query = new GedcomxPlaceSearchQueryBuilder();
        $query->name("Paris");
        $query->parentId('442102', true, true);

        $factory = new FamilySearchStateFactory();
        /** @var \Gedcomx\Extensions\FamilySearch\Rs\Client\FamilySearchPlaces $collection */
        $collection = $factory->newPlacesState()
                              ->authenticateViaOAuth2Password(
                                  SandboxCredentials::USERNAME,
                                  SandboxCredentials::PASSWORD,
                                  SandboxCredentials::API_KEY);
        $results = $collection->searchForPlaces($query);
        $places = $results->getResults();
        $description = $results->readPlaceDescription($places[0]);
        $this->assertEquals(
            HttpStatus::OK,
            $description->getResponse()->getStatusCode(),
            $this->buildFailMessage(__METHOD__, $description)
        );
        $this->assertNotEmpty($description->getPlaceDescription());
    }

    /**
     * @link https://familysearch.org/developers/docs/api/places/Read_Place_Description_Children_usecase
     */
    public function testReadPlaceDescriptionChildren()
    {
        $query = new GedcomxPlaceSearchQueryBuilder();
        $query->name("Utah")->typeId('47');

        $factory = new FamilySearchStateFactory();
        /** @var \Gedcomx\Extensions\FamilySearch\Rs\Client\FamilySearchPlaces $collection */
        $collection = $factory->newPlacesState()
                              ->authenticateViaOAuth2Password(
                                  SandboxCredentials::USERNAME,
                                  SandboxCredentials::PASSWORD,
                                  SandboxCredentials::API_KEY);
        /** @var \Gedcomx\Extensions\FamilySearch\Rs\Client\FamilySearchPlaceDescriptionState $results */
        $results = $collection->searchForPlaces($query);
        $places = $results->getResults();
        $description = $results->readPlaceDescription($places[0]);
        $children = $description->readChildren();

        $this->assertEquals(
            HttpStatus::OK,
            $children->getResponse()->getStatusCode(),
            $this->buildFailMessage(__METHOD__, $children)
        );
        $this->assertNotEmpty($children->getPlaceDescriptions());
    }

    /**
     * testReadPlaceGroup
     * @link https://familysearch.org/developers/docs/api/places/Read_Place_Group_usecase
     * @see this::testPlaceTypeGroups
     */

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
     * testReadPlaceTypeGroup
     * @link https://familysearch.org/developers/docs/api/places/Read_Place_Type_Group_usecase
     * @see this::testPlaceTypeGroups
     */

    /**
     * https://familysearch.org/developers/docs/api/places/Read_Place_Type_Groups_usecase
     */
    public function testPlaceTypeGroups()
    {
        $factory = new FamilySearchStateFactory();
        /** @var \Gedcomx\Extensions\FamilySearch\Rs\Client\FamilySearchPlaces $collection */
        $collection = $factory->newPlacesState()
                              ->authenticateViaOAuth2Password(
                                  SandboxCredentials::USERNAME,
                                  SandboxCredentials::PASSWORD,
                                  SandboxCredentials::API_KEY
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
         * @link https://familysearch.org/developers/docs/api/places/Read_Place_Group_usecase
         */
        $groupState = $collection->readPlaceGroupById(30);
        $this->assertEquals(
            HttpStatus::OK,
            $groupState->getResponse()->getStatusCode(),
            $this->buildFailMessage(__METHOD__."(Get group)",$groupState)
        );
        $this->assertNotEmpty($groupState->getPlaceGroup());
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
                                  SandboxCredentials::USERNAME,
                                  SandboxCredentials::PASSWORD,
                                  SandboxCredentials::API_KEY);
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
                                  SandboxCredentials::USERNAME,
                                  SandboxCredentials::PASSWORD,
                                  SandboxCredentials::API_KEY);
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
                                  SandboxCredentials::USERNAME,
                                  SandboxCredentials::PASSWORD,
                                  SandboxCredentials::API_KEY);
        $results = $collection->searchForPlaces($query);
        $this->assertEquals(HttpStatus::OK, $results->getResponse()->getStatusCode());
        $this->assertNotEmpty($results->getResults());
    }

    private function fetchVocabElements(){
        if ($this->vocabElements == null) {
            $factory = new FamilySearchStateFactory();
            $this->collection = $factory->newPlacesState()
                                        ->authenticateViaOAuth2Password(
                                            SandboxCredentials::USERNAME,
                                            SandboxCredentials::PASSWORD,
                                            SandboxCredentials::API_KEY
                                        );
            $this->vocabListState = $this->collection->readPlaceTypes();
            $this->vocabElements = $this->vocabListState->getVocabElementList()->getElements();
        }
    }
    
}