<?php 

namespace Gedcomx\Tests\Extensions\FamilySearch\Rs\Client;

use Gedcomx\Extensions\FamilySearch\Rs\Client\FamilySearchStateFactory;
use Gedcomx\Rs\Client\Util\GedcomxPlaceSearchQueryBuilder;
use Gedcomx\Rs\Client\Util\HttpStatus;
use Gedcomx\Tests\ApiTestCase;

class FamilySearchPlacesTest extends ApiTestCase
{
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
        $query->parentId('393946', true, true);
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
}