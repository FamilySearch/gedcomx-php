<?php


namespace Gedcomx\Rs\Client;

use Gedcomx\Conclusion\Person;
use Gedcomx\Conclusion\Relationship;
use Gedcomx\Gedcomx;
use Gedcomx\Rs\Client\Exception\GedcomxApplicationException;
use Gedcomx\Rs\Client\Options\StateTransitionOption;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use RuntimeException;

/**
 * The PersonParentsState exposes management functions for person parents.
 *
 * Class PersonParentsState
 *
 * @package Gedcomx\Rs\Client
 */
class PersonParentsState extends PersonsState
{
    /**
     * Constructs a new person parents state using the specified client, request, response, access token, and state factory.
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

    /**
     * Clones the current state instance.
     * @param \GuzzleHttp\Psr7\Request  $request
     * @param \GuzzleHttp\Psr7\Response $response
     *
     * @return \Gedcomx\Rs\Client\PersonParentsState
     */
    protected function reconstruct( Request $request, Response $response)
    {
        return new PersonParentsState($this->client, $request, $response, $this->accessToken, $this->stateFactory);
    }

    /**
     * Returns the entity from the REST API response.
     *
     * @return \Gedcomx\Gedcomx
     */
    protected function loadEntity()
    {
        $json = json_decode($this->response->getBody(), true);
        return new Gedcomx($json);
    }

    /**
     * Gets the main data element represented by this state instance.
     *
     * @return object
     */
    protected function getScope()
    {
        return $this->getEntity();
    }

    /**
     * Gets the list of parents for the person represented by this state instance.
     *
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
     * Gets the list of relationships for the person's parents.
     *
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
     * Finds the relationship to the specified parent.
     * This method iterates over the current person's relationships, and each item is examined
     * to determine if the parent ID in the relationship matches the parent ID for the specified parent. If one is found,
     * that relationship object containing that parent ID is returned, and no other relationships are examined further.
     *
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
     * Reads the current person represented by this state instance.
     *
     * @param Options\StateTransitionOption $option,...
     *
     * @return PersonState
     */
    public function readPerson(StateTransitionOption $option = null)
    {
        $link = $this->getLink(Rel::PERSON);
        if ($link == null || $link->getHref() == null) {
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
     * Reads the specified person, which is a parent to the current person. This person could come the array of persons for the current person.
     *
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
            $this->passOptionsTo('invoke', array($request), func_get_args()),
            $this->accessToken
        );
    }

    /**
     * Reads the specified relationship, which is a relationship between the current person and a parent. This relationship could come from the array of relationships for the current person.
     *
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
            $this->passOptionsTo('invoke', array($request), func_get_args()),
            $this->accessToken
        );
    }

    /**
     * Removes the specified relationship, which is a relationship between the current person and a parent.
     *
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
            $this->passOptionsTo('invoke', array($request), func_get_args()),
            $this->accessToken
        );
    }

    /**
     * Removes the relationship from the current person to the specified person.
     *
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
            $this->passOptionsTo('invoke', array($request), func_get_args()),
            $this->accessToken
        );
    }
}