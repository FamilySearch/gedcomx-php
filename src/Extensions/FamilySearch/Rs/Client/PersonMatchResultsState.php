<?php

namespace Gedcomx\Extensions\FamilySearch\Rs\Client;

use Gedcomx\Atom\Entry;
use Gedcomx\Conclusion\Identifier;
use Gedcomx\Conclusion\Person;
use Gedcomx\Extensions\FamilySearch\Rs\Client\Helpers\FamilySearchRequest;
use Gedcomx\Gedcomx;
use Gedcomx\Rs\Client\Options\StateTransitionOption;
use Gedcomx\Rs\Client\PersonSearchResultsState;
use Gedcomx\Rs\Client\PersonState;
use Gedcomx\Rs\Client\StateFactory;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use Gedcomx\Types\IdentifierType;

/**
 * The PersonMatchResultsState exposes management functions for a person match results.
 *
 * Class PersonMatchResultsState
 *
 * @package Gedcomx\Extensions\FamilySearch\Rs\Client
 */
class PersonMatchResultsState extends PersonSearchResultsState
{
    /**
     * Constructs a new person match results state using the specified client, request, response, access token, and
     * state factory.
     * @param \GuzzleHttp\Client             $client
     * @param \GuzzleHttp\Psr7\Request    $request
     * @param \GuzzleHttp\Psr7\Response   $response
     * @param string                          $accessToken
     * @param \Gedcomx\Rs\Client\StateFactory $stateFactory
     */
    function __construct(Client $client, Request $request, Response $response, $accessToken, StateFactory $stateFactory)
    {
        parent::__construct($client, $request, $response, $accessToken, $stateFactory);
    }

    /**
     * Clones the current state instance.
     *
     * @param \GuzzleHttp\Psr7\Request  $request
     * @param \GuzzleHttp\Psr7\Response $response
     *
     * @return \Gedcomx\Extensions\FamilySearch\Rs\Client\PersonMatchResultsState
     */
    protected function reconstruct(Request $request, Response $response)
    {
        return new PersonMatchResultsState($this->client, $request, $response, $this->accessToken, $this->stateFactory);
    }

    /**
     * Reads the current person for these potential match results.
     *
     * @param StateTransitionOption $option,...
     *
     * @return PersonState
     */
    public function readPerson(StateTransitionOption $option = null)
    {
        $link = $this->getLink(Rel::PERSON);
        if ($link == null || $link->getHref() == null) {
            return null;
        }

        $request = $this->createAuthenticatedGedcomxRequest("GET", $link->getHref());
        return $this->stateFactory->createState(
            "PersonState",
            $this->client,
            $request,
            $this->passOptionsTo('invoke', array($request), func_get_args()),
            $this->accessToken
        );
    }

    /**
     * Reads merge options for the specified search result entry.
     *
     * @param Entry $entry
     * @param StateTransitionOption $options
     * @return PersonMergeState
     */
    public function readMergeOptions(Entry $entry, StateTransitionOption $options = null)
    {
        $link = $entry->getLink(Rel::MERGE);
        if ($link == null || $link->getHref() == null) {
            return null;
        }

        $request = $this->createAuthenticatedRequest('OPTIONS', $link->getHref());
        FamilySearchRequest::applyFamilySearchMediaType($request);
        return $this->stateFactory->createState(
            "PersonMergeState",
            $this->client,
            $request,
            $this->passOptionsTo('invoke', array($request), func_get_args()),
            $this->accessToken
        );
    }

    /**
     * Creates a merge analysis for the current person and the potential duplicate person specified by the search result
     * entry.
     *
     * @param Entry $entry
     * @param StateTransitionOption $options
     * @return PersonMergeState
     */
    public function readMergeAnalysis(Entry $entry, StateTransitionOption $options = null)
    {
        $link = $entry->getLink(Rel::MERGE);
        if ($link == null || $link->getHref() == null) {
            return null;
        }

        $request = $this->createAuthenticatedRequest('GET', $link->getHref());
        FamilySearchRequest::applyFamilySearchMediaType($request);
        return $this->stateFactory->createState(
            "PersonMergeState",
            $this->client,
            $request,
            $this->passOptionsTo('invoke', array($request), func_get_args()),
            $this->accessToken
        );
    }

    /**
     * Declares the specified search result entry as not a match for the current person.
     *
     * @param Entry $entry
     * @param StateTransitionOption $options
     * @return PersonNonMatchesState
     */
    public function addNonMatch(Entry $entry, StateTransitionOption $options = null)
    {
        $link = $this->getLink(Rel::NOT_A_MATCHES);
        if ($link == null || $link->getHref() == null) {
            return null;
        }

        $entity = new Gedcomx();
        $person = new Person();
        $person->setId($entry->getId());
        $entity->addPerson($person);
        $request = $this->createAuthenticatedRequest('POST', $link->getHref());
        $request->setBody($entity->toJson());
        FamilySearchRequest::applyFamilySearchMediaType($request);
        return $this->stateFactory->createState(
            "PersonNonMatchesState",
            $this->client,
            $request,
            $this->passOptionsTo('invoke', array($request), func_get_args()),
            $this->accessToken
        );
    }

    /**
     * Declares the match status for the current person the specified search result entry.
     *
     * @param Entry $entry
     * @param string $status
     * @param StateTransitionOption $options
     * @return PersonMatchResultsState
     */
    public function updateMatchStatus(Entry $entry, $status, StateTransitionOption $options = null)
    {
        $updateStatusUri = $this->getSelfUri();
        $entity = new Gedcomx();
        $person = new Person();
        $id = new Identifier();
        $id->setType(IdentifierType::PERSISTENT);
        $id->setValue($entry->getId());
        $person->setIdentifiers(array($id));
        $entity->setPersons(array($person));
        $request = $this->createAuthenticatedGedcomxRequest("POST", $updateStatusUri);
        $request->removeHeader("Accept");
        $request->setBody($entity->toJson());
        return $this->stateFactory->createState(
            "PersonMatchResultsState",
            $this->client,
            $request,
            $this->passOptionsTo('invoke', array($request), func_get_args()),
            $this->accessToken
        );
    }
}