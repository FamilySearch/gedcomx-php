<?php

namespace Gedcomx\tests\Extensions\FamilySearch\Rs\Client;

use Gedcomx\Extensions\FamilySearch\Rs\Client\FamilySearchStateFactory;
use Gedcomx\Rs\Client\Util\GedcomxPlaceSearchQueryBuilder;
use Gedcomx\Rs\Client\Util\HttpStatus;
use Gedcomx\Tests\ApiTestCase;

class FamilySearchPlaceDescriptionStateTest extends ApiTestCase
{
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

}