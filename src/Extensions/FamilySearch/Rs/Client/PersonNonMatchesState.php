<?php

namespace Gedcomx\Extensions\FamilySearch\Rs\Client;

use Gedcomx\Conclusion\Person;
use Gedcomx\Extensions\FamilySearch\FamilySearchPlatform;
use Gedcomx\Extensions\FamilySearch\Rs\Client\FamilyTree\FamilyTreePersonState;
use Gedcomx\Extensions\FamilySearch\Rs\Client\FamilyTree\FamilyTreeStateFactory;
use Gedcomx\Extensions\FamilySearch\Rs\Client\Helpers\FamilySearchRequest;
use Gedcomx\Rs\Client\Options\StateTransitionOption;
use Gedcomx\Rs\Client\PersonState;
use Gedcomx\Rs\Client\Util\HttpStatus;
use Guzzle\Http\Client;
use Guzzle\Http\Message\Request;
use Guzzle\Http\Message\Response;

/**
 * The PersonNonMatchesState exposes management functions for person non matches.
 *
 * Class PersonNonMatchesState
 *
 * @package Gedcomx\Extensions\FamilySearch\Rs\Client
 */
class PersonNonMatchesState extends PersonState
{
    /**
     * Constructs a new person non matches state using the specified client, request, response, access token, and state factory.
     *
     * @param \Guzzle\Http\Client                                                          $client
     * @param \Guzzle\Http\Message\Request                                                 $request
     * @param \Guzzle\Http\Message\Response                                                $response
     * @param string                                                                       $accessToken
     * @param \Gedcomx\Extensions\FamilySearch\Rs\Client\FamilyTree\FamilyTreeStateFactory $stateFactory
     */
    public function __construct(Client $client, Request $request, Response $response, $accessToken, FamilyTreeStateFactory $stateFactory)
    {
        parent::__construct($client, $request, $response, $accessToken, $stateFactory);
    }

    /**
     * Clones the current state instance.
     *
     * @param \Guzzle\Http\Message\Request  $request
     * @param \Guzzle\Http\Message\Response $response
     *
     * @return \Gedcomx\Extensions\FamilySearch\Rs\Client\FamilyTree\FamilyTreePersonState
     */
    protected function  reconstruct(Request $request, Response $response)
    {
        /** @var \Gedcomx\Rs\Client\StateFactory $stateFactory */
        return new FamilyTreePersonState($this->client, $request, $response, $this->accessToken, $this->stateFactory);
    }

    /**
     * Loads the entity from the REST API response if the response should have data.
     *
     * @return \Gedcomx\Extensions\FamilySearch\FamilySearchPlatform|null
     */
    protected function loadEntityConditionally()
    {
        if ($this->request->getMethod() == Request::GET
            && ($this->response->getStatusCode() == HttpStatus::OK || $this->response->getStatusCode() == HttpStatus::GONE)
            || $this->response->getStatusCode() == HttpStatus::PRECONDITION_FAILED
        ) {
            return $this->loadEntity();
        } else {
            return null;
        }
    }

    /**
     * Returns the entity from the REST API response.
     *
     * @return \Gedcomx\Extensions\FamilySearch\FamilySearchPlatform
     */
    protected function loadEntity()
    {
        $json = json_decode($this->response->getBody(), true);

        return new FamilySearchPlatform($json);
    }

    /**
     * Adds a person as a non match to this collection.
     *
     * @param Person $person
     * @param StateTransitionOption $option,...
     *
     * @return PersonNonMatchesState|null
     */
    public function addNonMatch(Person $person, StateTransitionOption $option = null)
    {
        $entity = new FamilySearchPlatform();
        $entity->setPersons(array($person));
        return $this->passOptionsTo('post', array($entity), func_get_args());
    }

    /**
     * Removes the declared non match person from this collection.
     *
     * @param \Gedcomx\Conclusion\Person                       $nonMatch
     * @param \Gedcomx\Rs\Client\Options\StateTransitionOption $option
     *
     * @return mixed|null
     */
    public function removeNonMatch(Person $nonMatch, StateTransitionOption $option = null)
    {
        $link = $nonMatch->getLink(Rel::NOT_A_MATCHES);
        if ($link == null || $link->getHref() == null) {
            return null;
        }

        $request = $this->createAuthenticatedRequest(Request::DELETE, $link->getHref());
        FamilySearchRequest::applyFamilySearchMediaType($request);
        return $this->stateFactory->createState(
            'PersonNonMatchesState',
            $this->client,
            $request,
            $this->passOptionsTo('invoke', array($request), func_get_args()),
            $this->accessToken
        );
    }
}