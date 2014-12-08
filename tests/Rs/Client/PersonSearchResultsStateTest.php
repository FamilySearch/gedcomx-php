<?php

namespace Gedcomx\Tests\Rs\Client;

use Gedcomx\Rs\Client\Util\GedcomxPersonSearchQueryBuilder;
use Gedcomx\Tests\ApiTestCase;
use Gedcomx\Rs\Client\StateFactory;

class PersonSearchResultsStateTest extends ApiTestCase{

    private $searchQuery;

    public function setUp(){
        parent::setUp();
        $this->searchQuery = new GedcomxPersonSearchQueryBuilder();
        $this->searchQuery->givenName("George")
            ->surname("Washington")
            ->deathPlace( "Mount Vernon, VA" );
    }

	public function testSearchForPersonsWithQueryBuilder(){
        $factory = new StateFactory();
        $this->collectionState($factory);

        $searchResults = $this->collectionState()->searchForPersons($this->searchQuery);

		$this->assertNotNull($searchResults);
	}

	public function testSearchForPersonsWithQueryString(){
        $factory = new StateFactory();
        $this->collectionState($factory);

        $query = "givenName:Richard Henry~ surname:Washington~";
        $searchResults = $this->collectionState()->searchForPersons($query);

		$this->assertNotNull($searchResults);
	}

    /**
     * Search results are not currently returning a Rel::RECORD link. This
     * is here to make sure the objects and methods are accessible and not
     * throwing errors.
     */
    public function testCanReadRecord(){
        $factory = new StateFactory();
        $this->collectionState($factory);

        $searchResults = $this->collectionState()->searchForPersons($this->searchQuery);
        $feed = $searchResults->getEntity();
        $entries = $feed->getEntries();
        $searchResults->readRecord( $entries[0] );
	}

    public function testCanReadPersonFromConclusion(){
        $factory = new StateFactory();
        $this->collectionState($factory);

        $searchResults = $this->collectionState()->searchForPersons($this->searchQuery);
        $feed = $searchResults->getEntity();
        if ($feed == null) {
            $this->assertTrue(false, "Search results did not return any matches");
        }
        $entries = $feed->getEntries();
        $persons = $entries[0]->getContent()->getGedcomx()->getPersons();
        $personState = $searchResults->readPersonFromConclusion($persons[0]);

        $this->assertNotNull($personState);
        $this->assertAttributeEquals( "200", "statusCode", $personState->getResponse() );
    }

    public function testCanReadPersonFromEntry(){
        $factory = new StateFactory();
        $this->collectionState($factory);

        $searchResults = $this->collectionState()->searchForPersons($this->searchQuery);
        $feed = $searchResults->getEntity();
        if( $feed == null) {
            $this->assertTrue(false, "Search did not return any matches.");
        }
        $entries = $feed->getEntries();
        $personState = $searchResults->readPersonFromEntry($entries[0]);

        $this->assertNotNull($personState);
        $this->assertAttributeEquals( "200", "statusCode", $personState->getResponse() );
    }
}