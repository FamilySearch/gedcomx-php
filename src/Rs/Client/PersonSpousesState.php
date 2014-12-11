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

/**
 * The PersonSpousesState exposes management functions for person spouses.
 *
 * Class PersonSpousesState
 *
 * @package Gedcomx\Rs\Client
 */
class PersonSpousesState extends GedcomxApplicationState
{
    /**
     * Create a new PersonSpousesState
     *
     * @param \Guzzle\Http\Client             $client
     * @param \Guzzle\Http\Message\Request    $request
     * @param \Guzzle\Http\Message\Response   $response
     * @param string                          $accessToken
     * @param \Gedcomx\Rs\Client\StateFactory $stateFactory
     */
    function __construct(Client $client, Request $request, Response $response, $accessToken, StateFactory $stateFactory)
    {
        parent::__construct($client, $request, $response, $accessToken, $stateFactory);
    }

    /**
     * Clone this instance of PersonSpousesState
     *
     * @param \Guzzle\Http\Message\Request  $request
     * @param \Guzzle\Http\Message\Response $response
     *
     * @return \Gedcomx\Rs\Client\PersonSpousesState
     */
    protected function reconstruct(Request $request, Response $response)
    {
        return new PersonSpousesState($this->client, $request, $response, $this->accessToken, $this->stateFactory);
    }

    /**
     * Parse the JSON response into GedcomX classes
     *
     * @return \Gedcomx\Gedcomx
     */
    protected function loadEntity()
    {
        $json = json_decode($this->response->getBody(), true);
        return new Gedcomx($json);
    }

    /**
     * Return the primary data object for this state
     *
     * @return \Gedcomx\Gedcomx
     */
    protected function getScope()
    {
        return $this->getEntity();
    }

    /**
     * Get the person objects associated with this state
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
     * Get the relationship objects associated with this state.
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
     * Return the spouse relationship definition of the spouse
     *
     * @param \Gedcomx\Conclusion\Person $spouse
     *
     * @return \Gedcomx\Conclusion\Relationship|null
     */
    public function findRelationshipTo(Person $spouse)
    {
        $relationships = $this->getRelationships();
        if ($relationships) {
            foreach ($relationships as $relationship) {
                if ($relationship->getPerson2() && $relationship->getPerson2()->getResource() && $relationship->getPerson2()->getResource() == '#' . $spouse->getId()) {
                    return $relationship;
                }
            }
        }

        return null;
    }

    /**
     * Read the primary person
     *
     * @param \Gedcomx\Rs\Client\Options\StateTransitionOption $option,...
     *
     * @return \Gedcomx\Rs\Client\PersonState|null
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
     * Read the spouse person
     *
     * @param \Gedcomx\Conclusion\Person                       $spouse
     * @param \Gedcomx\Rs\Client\Options\StateTransitionOption $option,...
     *
     * @return \Gedcomx\Rs\Client\PersonState|null
     */
    public function readSpouse(Person $spouse, StateTransitionOption $option = null)
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
     * Read the relationship definition of this relationship
     *
     * @param \Gedcomx\Conclusion\Relationship                 $relationship
     * @param \Gedcomx\Rs\Client\Options\StateTransitionOption $option,...
     *
     * @return \Gedcomx\Rs\Client\RelationshipState|null
     */
    public function readRelationship(Relationship $relationship, StateTransitionOption $option = null)
    {
        $link = $this->getLink(Rel::RELATIONSHIP);
        $link = $link == null ? $relationship->getLink(Rel::SELF) : $link;
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
     * Delete this relationship
     *
     * @param \Gedcomx\Conclusion\Relationship                 $relationship
     * @param \Gedcomx\Rs\Client\Options\StateTransitionOption $option,...
     *
     * @return \Gedcomx\Rs\Client\RelationshipState
     * @throws \Gedcomx\Rs\Client\Exception\GedcomxApplicationException
     */
    public function removeRelationship(Relationship $relationship, StateTransitionOption $option = null)
    {
        $link = $this->getLink(Rel::RELATIONSHIP);
        $link = $link == null ? $relationship->getLink(Rel::SELF) : $link;
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
     * Remove the spouse person from this relationship
     *
     * @param \Gedcomx\Conclusion\Person                       $spouse
     * @param \Gedcomx\Rs\Client\Options\StateTransitionOption $option,...
     *
     * @return \Gedcomx\Rs\Client\RelationshipState
     * @throws \Gedcomx\Rs\Client\Exception\GedcomxApplicationException
     */
    public function removeRelationshipTo(Person $spouse, StateTransitionOption $option = null)
    {
        $relationship = $this->findRelationshipTo($spouse);
        if ($relationship == null) {
            throw new GedcomxApplicationException("Unable to remove spouse: not found");
        }

        $link = $this->getLink(Rel::RELATIONSHIP);
        $link = $link == null ? $relationship->getLink(Rel::SELF) : $link;
        if ($link == null || $link->getHref() == null) {
            throw new GedcomxApplicationException("Unable to remove spouse: missing link.");
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
     * Read the Ancestry based on this spouse
     *
     * @param \Gedcomx\Conclusion\Person                       $person
     * @param \Gedcomx\Rs\Client\Options\StateTransitionOption $option,...
     *
     * @return \Gedcomx\Rs\Client\PersonState|null
     */
    public function readAncestryWithSpouse(Person $person, StateTransitionOption $option = null)
    {
        $link = $person->getLink(Rel::ANCESTRY);
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
     * Read the Decendancy based on this spouse
     *
     * @param \Gedcomx\Conclusion\Person                       $person
     * @param \Gedcomx\Rs\Client\Options\StateTransitionOption $option,...
     *
     * @return \Gedcomx\Rs\Client\PersonState|null
     */
    public function readDescendancyWithSpouse(Person $person, StateTransitionOption $option = null)
    {
        $link = $person->getLink(Rel::DESCENDANCY);
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
}