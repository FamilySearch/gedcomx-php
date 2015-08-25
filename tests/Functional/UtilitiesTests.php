<?php

namespace Gedcomx\Tests\Functional;

use Gedcomx\Extensions\FamilySearch\FamilySearchPlatform;
use Gedcomx\Extensions\FamilySearch\Feature;
use Gedcomx\Extensions\FamilySearch\Rs\Client\FamilySearchStateFactory;
use Gedcomx\Extensions\FamilySearch\Rs\Client\FamilyTree\FamilyTreeStateFactory;
use Gedcomx\Extensions\FamilySearch\Rs\Client\Util\ExperimentsFilter;
use Gedcomx\Gedcomx;
use Gedcomx\Rs\Client\GedcomxApplicationState;
use Gedcomx\Tests\ApiTestCase;
use GuzzleHttp\Psr7\Request;

class UtilitiesTests extends ApiTestCase
{
    /**
     * @vcr UtilitiesTests/testRedirectToPerson.json
     * @link https://familysearch.org/developers/docs/api/tree/Redirect_to_Person_usecase
     */
    public function testRedirectToPerson()
    {
        $factory = new FamilyTreeStateFactory();
        $this->collectionState($factory);

        $person = $this->createPerson()->get();
        $id = $person->getPerson()->getId();
        $uri = "https://sandbox.familysearch.org/platform/redirect?person=" . $id;
        $request = new Request('GET', $uri);
        $response = GedcomxApplicationState::send($person->getClient(), $request);

        $this->assertNotNull($response, "Response is null.");
        $this->assertNotEquals($uri, $response->effectiveUri, "Effective URL should not match original.");
    }

    /**
     * @vcr UtilitiesTests/testRedirectToPersonMemories.json
     * @link https://familysearch.org/developers/docs/api/tree/Redirect_to_Person_memories_usecase
     */
    public function testRedirectToPersonMemories()
    {
        $factory = new FamilyTreeStateFactory();
        $this->collectionState($factory);

        $person = $this->createPerson()->get();
        $id = $person->getPerson()->getId();
        $uri = "https://sandbox.familysearch.org/platform/redirect?context=memories&person=" . $id;
        $request = new Request('GET', $uri);
        $response = GedcomxApplicationState::send($person->getClient(), $request);

        $this->assertNotNull($response, "Response is null.");
        $this->assertNotEquals($uri, $response->effectiveUri, "Effective URL should not match original.");
    }

    /**
     * @vcr UtilitiesTests/testRedirectToSourceLinker.json
     * @link https://familysearch.org/developers/docs/api/tree/Redirect_to_Source_Linker_usecase
     */
    public function testRedirectToSourceLinker()
    {
        $factory = new FamilyTreeStateFactory();
        $this->collectionState($factory);

        $person = $this->createPerson()->get();

        $identifiers = $person->getPerson()->getIdentifiers();
        $uri = sprintf("https://sandbox.familysearch.org/platform/redirect?context=sourcelinker&person=%s&hintId=%s", $person->getPerson()->getId(), array_shift($identifiers)->getValue());
        $request = new Request('GET', $uri);
        $response = GedcomxApplicationState::send($person->getClient(), $request);

        $this->assertNotNull($response, "Response is empty.");
        $this->assertNotEquals($uri, $response->effectiveUri, "Effective URL should not match original request.");
    }

    /**
     * @vcr UtilitiesTests/testRedirectToUri.json
     * @link https://familysearch.org/developers/docs/api/tree/Redirect_to_Uri_usecase
     */
    public function testRedirectToUri()
    {
        $factory = new FamilyTreeStateFactory();
        $this->collectionState($factory);

        $uri = "https://sandbox.familysearch.org/platform/redirect?uri=https://familysearch.org/some/path?p1%3Dp1-value%26p2%3Dp2-value";
        $request = new Request('GET', $uri);
        $response = GedcomxApplicationState::send($this->collectionState()->getClient(), $request);

        $this->assertNotNull($response, "Response is empty.");
        $this->assertNotEquals($uri, $response->effectiveUri, "Effective URLs should not match");
    }
}
