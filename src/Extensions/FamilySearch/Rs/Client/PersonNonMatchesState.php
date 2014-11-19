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

class PersonNonMatchesState extends PersonState
{
    public function __construct(Client $client, Request $request, Response $response, $accessToken, FamilyTreeStateFactory $stateFactory)
    {
        parent::__construct($client, $request, $response, $accessToken, $stateFactory);
    }

    protected function  reconstruct(Request $request, Response $response)
    {
        /** @var \Gedcomx\Rs\Client\StateFactory $stateFactory */
        return new FamilyTreePersonState($this->client, $request, $response, $this->accessToken, $this->stateFactory);
    }

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

    protected function loadEntity()
    {
        $json = json_decode($this->response->getBody(), true);

        return new FamilySearchPlatform($json);
    }

    /**
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