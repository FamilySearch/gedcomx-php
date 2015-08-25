<?php


namespace Gedcomx\Rs\Client;

use Gedcomx\Common\ResourceReference;
use Gedcomx\Conclusion\Person;
use Gedcomx\Conclusion\Relationship;
use Gedcomx\Gedcomx;
use Gedcomx\Rs\Client\Options\StateTransitionOption;
use Gedcomx\Types\RelationshipType;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;

/**
 * Class RelationshipsState
 * @package Gedcomx\Rs\Client
 * The RelationshipsState exposes management functions for a relationships collection.
 */
class RelationshipsState extends GedcomxApplicationState
{
    /**
     * Constructs a new relationships state using the specified client, request, response, access token, and state factory.
     *
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
     * @return \Gedcomx\Rs\Client\RelationshipsState
     */
    protected function reconstruct(Request $request, Response $response)
    {
        return new RelationshipsState($this->client, $request, $response, $this->accessToken, $this->stateFactory);
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
     * Define the rel name for this state as a fall back if it cannot be determined from state data.
     * @return null|string
     */
    public function getSelfRel(){
        return Rel::RELATIONSHIP;
    }

    /**
     * Gets the relationships represented by the current entity collection Gedcomx->getRelationships().
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
     * Reads the collection specified by this state instance.
     *
     * @return CollectionState|null The collection that contains these collections.
     */
    public function readCollection()
    {
        $link = $this->getLink(Rel::COLLECTION);
        if ($link == null || $link->getHref() == null)
        {
            return null;
        }

        $request = $this->createAuthenticatedGedcomxRequest('GET', $link->getHref());
        return $this->stateFactory->createState(
            'CollectionState',
            $this->client,
            $request,
            $this->passOptionsTo('invoke', array($request), func_get_args()),
            $this->accessToken
        );
    }

    /**
     * Creates a relationship between the persons specified in the relationship parameter.
     *
     * @param \Gedcomx\Conclusion\Relationship                 $relationship
     * @param \Gedcomx\Rs\Client\Options\StateTransitionOption $options,...
     *
     * @return RelationshipState
     */
    public function addRelationship(Relationship $relationship, StateTransitionOption $options = null)
    {
        $entity = new Gedcomx();
        $entity->addRelationship($relationship);
        $request = $this->createAuthenticatedGedcomxRequest('POST', $this->getSelfUri(), [], null, $entity->toJson());
        return $this->stateFactory->createState(
            'RelationshipState',
            $this->client,
            $request,
            $this->passOptionsTo('invoke', array($request), func_get_args()),
            $this->accessToken
        );
    }

    /**
     * Creates a spouse relationship between the two specified persons.
     * The person1 parameter does not have to be the husband, it could be the wife; however, person2 must be the opposite. So if you
     * specify a husband for person1 then person2 must be the wife. Conversely, if you specify a wife for person1
     * then person2 must be the husband.
     *
     * @param Person $person1
     * @param Person $person2
     * @param \Gedcomx\Rs\Client\Options\StateTransitionOption $options,...
     *
     * @return mixed
     */
    public function addSpouseRelationship(Person $person1, Person $person2, StateTransitionOption $options = null)
    {
        $relationship = new Relationship();
        $relationship->setPerson1(new ResourceReference($person1->getSelfUri()));
        $relationship->setPerson2(new ResourceReference($person2->getSelfUri()));
        $relationship->setKnownType(RelationshipType::COUPLE);
        return $this->addRelationship($relationship, $options);
    }

    /**
     * Creates a parent child relationship for the specified persons.     *
     *
     * @param \Gedcomx\Conclusion\Person                       $parent
     * @param \Gedcomx\Conclusion\Person                       $child
     * @param \Gedcomx\Rs\Client\Options\StateTransitionOption $options
     *
     * @return mixed
     */
    public function addParentChildRelationship(Person $parent, Person $child, StateTransitionOption $options = null)
    {
        $relationship = new Relationship();
        $relationship->setPerson1(new ResourceReference($parent->getSelfUri()));
        $relationship->setPerson2(new ResourceReference($child->getSelfUri()));
        $relationship->setKnownType(RelationshipType::COUPLE);
        return $this->addRelationship($relationship, $options);
    }
}