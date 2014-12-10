<?php
/**
 * Copyright Intellectual Reserve, Inc.
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *   http://www.apache.org/licenses/LICENSE-2.0
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

namespace Gedcomx\Rs\Client;

use Gedcomx\Conclusion\Person;
use Gedcomx\Conclusion\Relationship;
use Gedcomx\Gedcomx;
use Gedcomx\Rs\Client\Exception\GedcomxApplicationException;
use Gedcomx\Rs\Client\GedcomxApplicationState;
use Gedcomx\Rs\Client\Options\StateTransitionOption;
use Gedcomx\Rs\Client\Rel;
use Gedcomx\Rs\Client\StateFactory;
use Guzzle\Http\Client;
use Guzzle\Http\Message\Request;
use Guzzle\Http\Message\Response;

/**
 * The PersonChildrenState exposes management functions for person children.
 *
 * Class PersonChildrenState
 *
 * @package Gedcomx\Rs\Client
 */
class PersonChildrenState extends GedcomxApplicationState
{
    /**
     * Constructs a new person children state using the specified client, request, respons, access token, and state
     * factory.
     *
     * @param \Guzzle\Http\Client             $client
     * @param \Guzzle\Http\Message\Request    $request
     * @param \Guzzle\Http\Message\Response   $response
     * @param string                          $accessToken
     * @param \Gedcomx\Rs\Client\StateFactory $stateFactory
     */
    public function __construct(Client $client, Request $request, Response $response, $accessToken, StateFactory $stateFactory)
    {
        parent::__construct($client, $request, $response, $accessToken, $stateFactory);
    }

    /**
     * Clones the current state instance.
     *
     * @param \Guzzle\Http\Message\Request  $request
     * @param \Guzzle\Http\Message\Response $response
     *
     * @return \Gedcomx\Rs\Client\PersonChildrenState
     */
    protected function reconstruct(Request $request, Response $response)
    {
        return new PersonChildrenState($this->client, $request, $response, $this->accessToken, $this->stateFactory);
    }

    /**
     * Returns the entity from the REST API response.
     *
     * @returns \Gedcomx\Gedcomx
     */
    protected function loadEntity()
    {
        $json = json_decode($this->response->getBody(), true);

        return new Gedcomx($json);
    }

    /**
     * Gets the main data element represented by this state instance.
     *
     * @return null
     */
    protected function getScope()
    {
        return $this->getEntity();
    }

    /**
     * Gets the array of persons represented by the current state instance.
     *
     * @return null
     */
    public function getPersons()
    {
        return $this->entity == null ? null : $this->entity->getPersons();
    }

    /**
     * Return the relationships associated with this person.
     *
     * @return \Gedcomx\Conclusion\Relationship[]
     */
    public function getRelationships()
    {
        return $this->entity == null ? null : $this->entity->getRelationships();
    }

    /**
     * Finds the relationship to the specified child.
     * This method iterates over the current relationships, and each item is examined
     * to determine if the child ID in the relationship matches the child ID for the specified child. If one is found,
     * that relationship object containing that child ID is returned, and no other relationships are examined further.
     *
     * @param \Gedcomx\Conclusion\Person $child
     *
     * @return \Gedcomx\Conclusion\Relationship|null
     */
    public function findRelationshipTo(Person $child)
    {
        $relationships = $this->getRelationships();
        if ($relationships != null) {
            foreach ($relationships as $relationship) {
                $childReference = $relationship->getPerson2();
                if ($childReference != null) {
                    $reference = $childReference->getResource();
                    if ($reference == "#" . $child->getId()) {
                        return $relationship;
                    }
                }
            }
        }

        return null;
    }

    /**
     * Reads the current person represented by this state instance.
     *
     * @param \Gedcomx\Rs\Client\Options\StateTransitionOption|null $option,... zero or more StateTransitionObjects
     *
     * @return \Gedcomx\Rs\Client\PersonState
     */
    public function readPerson(StateTransitionOption $option = null)
    {
        $link = $this->getLink(Rel::PERSON);
        if ($link == null || $link->getHref() == null) {
            return null;
        }

        $request = $this->createAuthenticatedGedcomxRequest(Request::GET, $link->getHref());

        return $this->stateFactory->createState(
            "PersonState",
            $this->client,
            $request,
            $this->passOptionsTo('invoke', array($request), func_get_args()),
            $this->accessToken
        );
    }

    /**
     * Reads the child from the person specified.
     *
     * @param \Gedcomx\Conclusion\Person                            $person
     * @param \Gedcomx\Rs\Client\Options\StateTransitionOption|null $option,... zero or more StateTransitionObjects
     *
     * @return \Gedcomx\Rs\Client\PersonState
     */
    public function readChild(Person $person, StateTransitionOption $option = null)
    {
        $link = $person->getLink(Rel::PERSON);
        if ($link == null) {
            $link = $person->getLink(Rel::SELF);
        }
        if ($link == null || $link->getHref() == null) {
            return null;
        }

        $request = $this->createAuthenticatedGedcomxRequest(Request::GET, $link->getHref());

        return $this->stateFactory->createState(
            "PersonState",
            $this->client,
            $request,
            $this->passOptionsTo("invoke", array($request), func_get_args()),
            $this->accessToken
        );
    }

    /**
     * Reads the specified relationship.
     *
     * @param \Gedcomx\Conclusion\Relationship                      $relationship
     * @param \Gedcomx\Rs\Client\Options\StateTransitionOption|null $option,... zero or more StateTransitionObjects
     *
     * @return null
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
            "RelationshipState",
            $this->client,
            $request,
            $this->passOptionsTo('invoke', array($request), func_get_args()),
            $this->accessToken
        );
    }

    /**
     * Removes the specified relationship.
     *
     * @param \Gedcomx\Conclusion\Relationship                      $relationship
     * @param \Gedcomx\Rs\Client\Options\StateTransitionOption|null $option,...
     *
     * @return \Gedcomx\Rs\Client\RelationshipState
     * @throws \Gedcomx\Rs\Client\Exception\GedcomxApplicationException
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
            "RelationshipState",
            $this->client,
            $request,
            $this->passOptionsTo('invoke', array($request), func_get_args()),
            $this->accessToken
        );
    }

    /**
     * Removes the relationship to the specified child.
     *
     * @param \Gedcomx\Conclusion\Person                            $child
     * @param \Gedcomx\Rs\Client\Options\StateTransitionOption|null $option,...
     *
     * @return mixed
     * @throws GedcomxApplicationException
     */
    public function removeRelationshipTo(Person $child, StateTransitionOption $option = null)
    {
        $relationship = $this->findRelationshipTo($child);
        if ($relationship == null) {
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
            "RelationshipState",
            $this->client,
            $request,
            $this->passOptionsTo('invoke', array($request), func_get_args()),
            $this->accessToken
        );
    }
}
