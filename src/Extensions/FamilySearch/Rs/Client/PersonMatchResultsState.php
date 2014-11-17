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
use Guzzle\Http\Client;
use Guzzle\Http\Message\Request;
use Guzzle\Http\Message\Response;
use Gedcomx\Types\IdentifierType;

class PersonMatchResultsState extends PersonSearchResultsState
{
    function __construct(Client $client, Request $request, Response $response, $accessToken, StateFactory $stateFactory)
    {
        parent::__construct($client, $request, $response, $accessToken, $stateFactory);
    }

    protected function reconstruct(Request $request, Response $response)
    {
        return new PersonMatchResultsState($this->client, $request, $response, $this->accessToken, $this->stateFactory);
    }

    /**
     * @param StateTransitionOption $option
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

        $request = $this->createAuthenticatedRequest(Request::OPTIONS, $link->getHref());
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

        $request = $this->createAuthenticatedRequest(Request::GET, $link->getHref());
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
        $request = $this->createAuthenticatedRequest(Request::POST, $link->getHref());
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