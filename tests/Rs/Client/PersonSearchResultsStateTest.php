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
		$searchResults = $this->collectionState
			->searchForPersons($this->searchQuery);

		$this->assertNotNull($searchResults);
	}

	public function testSearchForPersonsWithQueryString(){
        $query = "givenName:Richard Henry~ surname:Washington~";
        $searchResults = $this->collectionState
            ->searchForPersons($query);

		$this->assertNotNull($searchResults);
	}

    /**
     * Search results are not currently returning a Rel::RECORD link. This
     * is here to make sure the objects and methods are accessible and not
     * throwing errors.
     */
    public function testCanReadRecord(){
        $searchResults = $this->collectionState
            ->searchForPersons($this->searchQuery);
        $feed = $searchResults->getEntity();
        $entries = $feed->getEntries();
        $searchResults->readRecord( $entries[0] );
	}

    public function testCanReadPersonFromConclusion(){
        $searchResults = $this->collectionState
            ->searchForPersons($this->searchQuery);
        $feed = $searchResults->getEntity();
        $entries = $feed->getEntries();
        $persons = $entries[0]->getContent()->getGedcomx()->getPersons();
        $personState = $searchResults->readPersonFromConclusion($persons[0]);

        $this->assertNotNull($personState);
    }

    public function testCanReadPersonFromEntry(){
        $searchResults = $this->collectionState
            ->searchForPersons($this->searchQuery);
        $feed = $searchResults->getEntity();
        $entries = $feed->getEntries();
        $personState = $searchResults->readPersonFromEntry($entries[0]);

        $this->assertNotNull($personState);
    }

    public function testCanReadNextPage(){
        $searchResults = $this->collectionState
            ->searchForPersons($this->searchQuery);
        $nextPage = $searchResults->readNextPage();
        $first = $searchResults->getEntity()->getEntries();
        $second = $nextPage->getEntity()->getEntries();

        $this->assertNotEquals( $first, $second );
    }

    public function testWarningsAreReturned(){
        $searchResults = $this->collectionState
            ->searchForPersons("firsstName:Ruby");

        $this->assertArrayHasKey( "warning", $searchResults->getHeaders(), "Warning headers should be returned with this request." );
    }
} 