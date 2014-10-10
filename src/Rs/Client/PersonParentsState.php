<?php


namespace Gedcomx\Rs\Client;

use Gedcomx\Conclusion\Person;
use Gedcomx\Conclusion\Relationship;
use Gedcomx\Gedcomx;
use Gedcomx\Rs\Client\Exception\GedcomxApplicationException;
use Gedcomx\Rs\Client\Options\StateTransitionOption;
use Guzzle\Http\Client;
use Guzzle\Http\Message\Request;
use Guzzle\Http\Message\Response;
use RuntimeException;

class PersonParentsState extends GedcomxApplicationState
{
    /**
     * @param Client       $client
     * @param Request      $request
     * @param Response     $response
     * @param string       $accessToken
     * @param StateFactory $stateFactory
     */
    function __construct(Client $client, Request $request, Response $response, $accessToken, StateFactory $stateFactory)
    {
        parent::__construct($client, $request, $response, $accessToken, $stateFactory);
    }

    protected function reconstruct( Request $request, Response $response)
    {
        return new PersonParentsState($this->client, $request, $response, $this->accessToken, $this->stateFactory);
    }

    protected function loadEntity()
    {
        $json = json_decode($this->response->getBody(), true);
        return new Gedcomx($json);
    }

    protected function getScope()
    {
        return $this->getEntity();
    }

    /**
     * @return Person[]|null
     */
    public function getPersons()
    {
        if ($this->entity) {
            return $this->entity->getPersons();
        }

        return null;
    }

    /**
     * @return Relationship[]|null
     */
    public function getRelationships()
    {
        if ($this->entity) {
            return $this->entity->getRelationships();
        }

        return null;
    }

    /**
     * @param Person $parent
     * @return Relationship|null
     */
    public function findRelationshipTo(Person $parent)
    {
        $relationships = $this->getRelationships();
        if ($relationships) {
            foreach ($relationships as $relationship) {
                if ($relationship->getPerson2() && $relationship->getPerson2()->getResource() && $relationship->getPerson2()->getResource() == '#' . $parent->getId()) {
                    return $relationship;
                }
            }
        }

        return null;
    }

    /**
     * @param Options\StateTransitionOption $option,...
     *
     * @return PersonState
     */
    public function readPerson(StateTransitionOption $option = null)
    {
        $link = $this->getLink(Rel::PERSON);
        if ($link == null || link.getHref() == null) {
            return null;
        }

        $request = $this->createAuthenticatedGedcomxRequest(Request::GET, $link->getHref());
        return $this->stateFactory->createState(
            'PersonState',
            $this->client,
            $request,
            $this->passOptionsTo('invoke', array($request), func_get_args()),
            $this->accessToken
        );
    }

    /**
     * @param Person                        $parent
     * @param Options\StateTransitionOption $option,...
     *
     * @return PersonState
     */
    public function readParent(Person $parent, StateTransitionOption $option = null)
    {
        $link = $parent->getLink(Rel::PERSON);
        if ($link == null) {
            $link = $parent->getLink(Rel::SELF);
        }
        if ($link == null || $link->getHref() == null) {
            return null;
        }

        $request = $this->createAuthenticatedGedcomxRequest(Request::GET, $link->getHref());
        return $this->stateFactory->createState(
            'PersonState',
            $this->client,
            $request,
            $this->passOptionsTo('invoke', array(request), func_get_args()),
            $this->accessToken
        );
    }

    /**
     * @param Relationship                  $relationship
     * @param Options\StateTransitionOption $option,...
     *
     * @return RelationshipState
     */
    public function readRelationship(Relationship $relationship, StateTransitionOption $option = null)
    {
        $link = $relationship->getLink(Rel::RELATIONSHIP);
        if ($link == null) {
            $link = $relationship->getLink(Rel::SELF);
        }
        if ($link == null || $link->getHref() == null) {
            return null;
        }

        $request = $this->createAuthenticatedGedcomxRequest(Request::GET, $link->getHref());
        return $this->stateFactory->createState(
            'RelationshipState',
            $this->client,
            $request,
            $this->passOptionsTo('invoke', array(request), func_get_args()),
            $this->accessToken
        );
    }

    /**
     * @param Relationship                  $relationship
     * @param Options\StateTransitionOption $option,...
     *
     * @throws Exception\GedcomxApplicationException
     * @return RelationshipState
     */
    public function removeRelationship(Relationship $relationship, StateTransitionOption $option = null)
    {
        $link = $relationship->getLink(Rel::RELATIONSHIP);
        if ($link == null) {
            $link = $relationship->getLink(Rel::SELF);
        }
        if ($link == null || $link->getHref() == null) {
            throw new GedcomxApplicationException("Unable to remove relationship: missing link.");
        }

        $request = $this->createAuthenticatedGedcomxRequest(Request::DELETE, $link->getHref());
        return $this->stateFactory->createState(
            'RelationshipState',
            $this->client,
            $request,
            $this->passOptionsTo('invoke', array(request), func_get_args()),
            $this->accessToken
        );
    }

    /**
     * @param Person                        $parent
     * @param Options\StateTransitionOption $option,...
     *
     * @throws Exception\GedcomxApplicationException
     * @return RelationshipState
     */
    public function removeRelationshipTo(Person $parent, StateTransitionOption $option = null)
    {
        $relationship = $this->findRelationshipTo($parent);
        if ($relationship == null){
            throw new GedcomxApplicationException("Unable to remove relationship: not found.");
        }

        $link = $relationship->getLink(Rel::RELATIONSHIP);
        if ($link == null) {
            $link = $relationship->getLink(Rel::SELF);
        }
        if ($link == null || $link->getHref() == null) {
            throw new GedcomxApplicationException("Unable to remove relationship: missing link.");
        }

        $request = $this->createAuthenticatedGedcomxRequest(Request::DELETE, $link->getHref());
        return $this->stateFactory->createState(
            'RelationshipState',
            $this->client,
            $request,
            $this->passOptionsTo('invoke', array(request), func_get_args()),
            $this->accessToken
        );
    }
}