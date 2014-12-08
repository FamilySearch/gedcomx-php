<?php 

namespace Gedcomx\Tests\Functional;

use Gedcomx\Extensions\FamilySearch\Rs\Client\FamilyTree\FamilyTreeStateFactory;
use Gedcomx\Rs\Client\Options\QueryParameter;
use Gedcomx\Rs\Client\StateFactory;
use Gedcomx\Rs\Client\Util\GedcomxPersonSearchQueryBuilder;
use Gedcomx\Rs\Client\Util\HttpStatus;
use Gedcomx\Tests\ApiTestCase;
use Gedcomx\Tests\PersonBuilder;

class SearchAndMatchTests extends ApiTestCase
{
    private $searchQuery;

    public function setUp(){
        parent::setUp();
        $this->searchQuery = new GedcomxPersonSearchQueryBuilder();
        $this->searchQuery->givenName("George")
            ->surname("Washington")
            ->deathPlace( "Mount Vernon, VA" );
    }

    /**
     * @link https://familysearch.org/developers/docs/api/tree/Read_Person_Not-A-Match_Declarations_usecase
     */
    public function testReadPersonNotAMatchDeclarations()
    {
        $factory = new FamilyTreeStateFactory();
        $collection = $this->collectionState($factory);

        $p = PersonBuilder::buildPerson(null);
        $person1 = $this->collectionState()->addPerson($p);
        $person2 = $this->collectionState()->addPerson($p)->get();
        $this->queueForDelete($person1, $person2);

        sleep(30); // This is to ensure the matching system on the server has time to recognize the two new duplicates
        $matches = $person2->readMatches();
        $this->assertEquals(
            HttpStatus::OK,
            $matches->getResponse()->getStatusCode(),
            $this->buildFailMessage(__METHOD__, $matches)
        );
        $entries = $matches->getResults()->getEntries();
        $entry = array_shift($entries);
        $id = $entry->getId();
        $match = $collection->readPersonById($id);
        $person2->addNonMatchState($match);
        $state = $person2->readNonMatches();

        $this->assertNotNull($state->ifSuccessful());
        $this->assertEquals((int)$state->getResponse()->getStatusCode(), 200);
    }

    /**
     * @link https://familysearch.org/developers/docs/api/tree/Read_Person_Possible_Duplicates_usecase
     */
    public function testReadPersonPossibleDuplicates()
    {
        $factory = new FamilyTreeStateFactory();
        $this->collectionState($factory);

        $p = PersonBuilder::buildPerson(null);
        $person1 = $this->collectionState()->addPerson($p);
        $person2 = $this->collectionState()->addPerson($p)->get();
        $this->queueForDelete($person1, $person2);

        sleep(30); // This is to ensure the matching system on the server has time to recognize the two new duplicates
        $state = $person2->readMatches();

        $this->assertNotNull($state->ifSuccessful());
        $this->assertEquals((int)$state->getResponse()->getStatusCode(), 200);
        $this->assertGreaterThan(0, count($state->getResults()->getEntries()));
    }

    /**
     * @link https://familysearch.org/developers/docs/api/tree/Read_Person_Record_Matches_usecase
     */
    public function testReadPersonRecordMatches()
    {
        $factory = new FamilyTreeStateFactory();
        $this->collectionState($factory);

        $person = $this->createPerson()->get();
        $query = new QueryParameter(true, "collection", "https://familysearch.org/platform/collections/records");
        $state = $person->readMatches($query);

        $this->assertNotNull($state->ifSuccessful());
        $this->assertEquals((int)$state->getResponse()->getStatusCode(), 204);
        $this->assertNull($state->getResults());
    }

    /**
     * @link https://familysearch.org/developers/docs/api/tree/Read_All_Match_Status_Types_Person_Record_Matches_usecase
     */
    public function testReadAllMatchStatusTypesPersonRecordMatches()
    {
        $factory = new FamilyTreeStateFactory();
        $this->collectionState($factory);

        $p = PersonBuilder::buildPerson(null);
        $person1 = $this->collectionState()->addPerson($p);
        $person2 = $this->collectionState()->addPerson($p)->get();
        $this->queueForDelete($person1, $person2);

        sleep(30); // This is to ensure the matching system on the server has time to recognize the two new duplicates
        $statuses = new QueryParameter(true, "status", array("pending", "accepted", "rejected"));
        $state = $person2->readMatches($statuses);

        $this->assertNotNull($state->ifSuccessful());
        $this->assertEquals((int)$state->getResponse()->getStatusCode(), 200);
        $this->assertGreaterThan(0, count($state->getResults()->getEntries()));
    }

    /**
     * @link https://familysearch.org/developers/docs/api/tree/Read_Higher_Confidence_Person_Accepted_Record_Matches_usecase
     */
    public function testReadHigherConfidencePersonAcceptedRecordMatches()
    {
        $factory = new FamilyTreeStateFactory();
        $this->collectionState($factory);

        $p = PersonBuilder::buildPerson(null);
        $person1 = $this->collectionState()->addPerson($p);
        $person2 = $this->collectionState()->addPerson($p)->get();
        $this->queueForDelete($person1, $person2);

        sleep(30); // This is to ensure the matching system on the server has time to recognize the two new duplicates
        $statuses = new QueryParameter(true, "status", "accepted");
        $confidence = new QueryParameter(true, "confidence", "4");
        $state = $person2->readMatches($statuses, $confidence);

        $this->assertNotNull($state->ifSuccessful());
        $this->assertEquals((int)$state->getResponse()->getStatusCode(), 200);
        $this->assertGreaterThan(0, count($state->getResults()->getEntries()));
    }

    /**
     * @link https://familysearch.org/developers/docs/api/tree/Read_Match_Scores_for_Persons_usecase
     */
    public function testReadMatchScoresForPersons()
    {
        $factory = new FamilyTreeStateFactory();
        $collection = $this->collectionState($factory);

        $query = new GedcomxPersonSearchQueryBuilder();
        $query->givenName("GedcomX")
              ->surname("User")
              ->Gender("Male")
              ->BirthDate("June 1800")
              ->BirthPlace("Provo, Utah, Utah, United States")
              ->DeathDate("July 14, 1900")
              ->DeathPlace("Provo, Utah, Utah, United States");
        $state = $collection->searchForPersonMatches($query);

        $this->assertNotNull($state->ifSuccessful());
        $this->assertEquals((int)$state->getResponse()->getStatusCode(), 200);
        $this->assertNotNull($state->getResults());
        $this->assertNotNull($state->getResults()->getEntries());
        $this->assertGreaterThan(0, count($state->getResults()->getEntries()));
        $entries = $state->getResults()->getEntries();
        $this->assertGreaterThan(0, array_shift($entries)->getScore());
    }

    /**
     * @link https://familysearch.org/developers/docs/api/tree/Read_Next_Page_of_Search_Results_usecase
     */
    public function testReadNextPageOfSearchResults(){
        $factory = new StateFactory();
        $this->collectionState($factory);

        $searchResults = $this->collectionState()->searchForPersons($this->searchQuery);
        $nextPage = $searchResults->readNextPage();
        $first = $searchResults->getEntity()->getEntries();
        $second = $nextPage->getEntity()->getEntries();

        $this->assertNotEquals( $first, $second );
    }

    /**
     * @link https://familysearch.org/developers/docs/api/tree/Update_Match_Status_for_Person_Record_Matches_usecase
     */
    public function testUpdateMatchStatusForPersonRecordMatches()
    {
        $factory = new FamilyTreeStateFactory();
        $this->collectionState($factory);

        $collection = new QueryParameter(true, "collection", "https://familysearch.org/platform/collections/records");
        $p = PersonBuilder::buildPerson(null);
        $person1 = $this->collectionState()->addPerson($p);
        $person2 = $this->collectionState()->addPerson($p)->get();
        $this->queueForDelete($person1, $person2);

        sleep(30); // This is to ensure the matching system on the server has time to recognize the two new duplicates
        $matches = $person2->readMatches();
        $this->assertEquals(
            HttpStatus::OK,
            $matches->getResponse()->getStatusCode(),
            $this->buildFailMessage(__METHOD__, $matches)
        );

        $accepted = new QueryParameter(true, "status", "accepted");

        $entries = $matches->getResults()->getEntries();
        $state = $matches->updateMatchStatus(array_shift($entries), $accepted, $collection);

        $this->assertNotNull($state->ifSuccessful());
        $this->assertEquals((int)$state->getResponse()->getStatusCode(), 204);
    }

    /**
     * @link https://familysearch.org/developers/docs/api/tree/Search_Persons_usecase
     */
    public function testSearchPersons(){
        $factory = new StateFactory();
        $this->collectionState($factory);

        $query = "givenName:Richard Henry~ surname:Washington~";
        $searchResults = $this->collectionState()->searchForPersons($query);

        $this->assertNotNull($searchResults);
    }

    /**
     * @link https://familysearch.org/developers/docs/api/tree/Search_Persons_With_Warnings_and_Errors_usecase
     */
    public function testSearchForPersonMatches()
    {
        $factory = new FamilyTreeStateFactory();
        $collection = $this->collectionState($factory);

        $query = new GedcomxPersonSearchQueryBuilder();
        $query->fatherSurname("Heaton")
              ->spouseSurname("Cox")
              ->surname("Heaton")
              ->givenName("Israel")
              ->birthPlace("Orderville, UT")
              ->deathDate("29 August 1936")
              ->deathPlace("Kanab, Kane, UT")
              ->spouseGivenName("Charlotte")
              ->motherGivenName("Clarissa")
              ->motherSurname("Hoyt")
              ->gender("Male")
              ->birthDate("30 January 1880")
              ->fatherGivenName("Jonathan");
        $state = $collection->searchForPersonMatches($query);

        $this->assertNotNull($state->ifSuccessful());
        $this->assertEquals((int)$state->getResponse()->getStatusCode(), 200);
        $this->assertNotNull($state->getResults());
        $this->assertNotNull($state->getResults()->getEntries());
        $this->assertGreaterThan(0, count($state->getResults()->getEntries()));
    }

    /**
     * @link https://familysearch.org/developers/docs/api/tree/Search_Persons_With_Warnings_and_Errors_usecase
     */
    public function testSearchPersonsWithWarningsAndErrors(){
        $factory = new StateFactory();
        $this->collectionState($factory);

        $searchResults = $this->collectionState()->searchForPersons("firsstName:Ruby");

        $this->assertArrayHasKey( "warning", $searchResults->getHeaders(), "Warning headers should be returned with this request." );
    }
}