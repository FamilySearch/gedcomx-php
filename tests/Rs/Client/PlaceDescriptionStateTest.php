<?php

namespace Gedcomx\tests\Rs\Client;

use Gedcomx\Extensions\FamilySearch\Rs\Client\FamilySearchStateFactory;
use Gedcomx\Rs\Client\Util\GedcomxPlaceSearchQueryBuilder;
use Gedcomx\Rs\Client\Util\HttpStatus;
use Gedcomx\Tests\ApiTestCase;

class PlaceDescriptionStateTest extends ApiTestCase
{
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
                $this->apiCredentials->username,
                $this->apiCredentials->password,
                $this->apiCredentials->apiKey);
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
}