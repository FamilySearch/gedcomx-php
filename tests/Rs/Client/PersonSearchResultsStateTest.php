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

	public function testCanReadRecord(){
        $searchResults = $this->collectionState
            ->searchForPersons($this->searchQuery);
        $feed = $searchResults->getEntity();
        $entries = $feed->getEntries();
        $personState = $searchResults->readRecord( $entries[0] );

		$this->assertNotNull($personState);
	}

    public function testCanReadPerson(){
        $searchResults = $this->collectionState
            ->searchForPersons($this->searchQuery);
        $feed = $searchResults->getEntity();
        $entries = $feed->getEntries();
        $person = $entries[0]->getContent()->getGedcomx()->persons[0];
        $personState = $searchResults->readPerson($person );

        $this->assertNotNull($personState);
    }

    public function testCanReadNextPage(){
        $searchResults = $this->collectionState
            ->searchForPersons($this->searchQuery)
            ->readNextPage();

        $this->assertNotNull( $searchResults );
    }
} 