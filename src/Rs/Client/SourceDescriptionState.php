<?php


namespace Gedcomx\Rs\Client;

use Gedcomx\Conclusion\Person;
use Gedcomx\Gedcomx;
use Gedcomx\Rs\Client\Options\StateTransitionOption;
use Gedcomx\Source\SourceDescription;
use Guzzle\Http\Client;
use Guzzle\Http\Message\EntityEnclosingRequest;
use Guzzle\Http\Message\Request;
use Guzzle\Http\Message\Response;
use RuntimeException;

/**
 * The SourceDescriptionState exposes management functions for a source description.
 */
class SourceDescriptionState extends GedcomxApplicationState
{
    /**
     * Constructs a source description state using the specified client, request, response, access token and state factory.
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
     * Clones the current state instance.
     *
     * @param \Guzzle\Http\Message\Request  $request
     * @param \Guzzle\Http\Message\Response $response
     *
     * @return \Gedcomx\Rs\Client\SourceDescriptionState
     */
    protected function reconstruct(Request $request, Response $response)
    {
        return new SourceDescriptionState($this->client, $request, $response, $this->accessToken, $this->stateFactory);
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
     * @return \Gedcomx\Source\SourceDescription|null
     */
    protected function getScope()
    {
        return $this->getSourceDescription();
    }

    /**
     * Gets the rel name for the current state instance.
     *
     * @return string
     */
    public function getSelfRel()
    {
        return Rel::DESCRIPTION;
    }

    /**
     * Gets the source description represented by this state instance.
     *
     * @return SourceDescription|null
     */
    public function getSourceDescription()
    {
        if ($this->getEntity() != null) {
            if ($this->getEntity()->getSourceDescriptions() != null && count($this->getEntity()->getSourceDescriptions()) > 0) {
                $descriptions = $this->getEntity()->getSourceDescriptions();
                return $descriptions[0];
            }
        }

        return null;
    }

    /**
     * Updates the specified source description.
     *
     * @param Gedcomx|SourceDescription $description
     * @param StateTransitionOption $option,...
     *
     * @return SourceDescriptionState
     */
    public function update($description, StateTransitionOption $option = null)
    {
        $entity = null;

        if ($description instanceof SourceDescription) {
            $entity = new Gedcomx();
            $entity->setSourceDescriptions(array($description));
        } else {
            $entity = $description;
        }
        /** @var EntityEnclosingRequest $request */
        $request = $this->createAuthenticatedGedcomxRequest(Request::POST, $this->getSelfUri());
        $request->setBody($entity->toJson());
        return $this->stateFactory->createState(
            'SourceDescriptionState',
            $this->client,
            $request,
            $this->passOptionsTo('invoke', array($request), func_get_args()),
            $this->accessToken
        );
    }

    /**
     * Read persons associated with the current source description.
     *
     * @param StateTransitionOption $option,...
     *
     * @return PersonsState
     */
    public function readPersons(StateTransitionOption $option = null)
    {
        $link = $this->getLink(Rel::PERSONS);
        if ($link == null || $link->getHref() == null) {
            return $this->stateFactory->createState(
                'PersonsState',
                $this->client,
                $this->request,
                $this->response,
                $this->accessToken
            );
        } else {
            $request = $this->createAuthenticatedGedcomxRequest(Request::GET, $link->getHref());
            return $this->stateFactory->createState(
                'PersonsState',
                $this->client,
                $request,
                $this->passOptionsTo('invoke', array($request), func_get_args()),
                $this->accessToken
            );
        }
    }

    /**
     * Read personas associated with the current source description.
     *
     * @param \Gedcomx\Rs\Client\Options\StateTransitionOption $option
     *
     * @return mixed
     */
    public function readPersonas(StateTransitionOption $option = null)
    {
        $link = $this->getLink(Rel::PERSONS);
        if ($link == null || $link->getHref() == null) {
            return $this->stateFactory->createState(
                'PersonsState',
                $this->getClient(),
                $this->getRequest(),
                $this->getResponse(),
                $this->accessToken
            );
        } else {
            $request = $this->createAuthenticatedGedcomxRequest(Request::GET, $link->getHref());
        }
        return $this->stateFactory->createState(
            'PersonsState',
            $this->client,
            $request,
            $this->passOptionsTo('invoke', array($request), func_get_args()),
            $this->accessToken
        );
    }

    /**
     * Adds a persona to the current source description using a person object.
     *
     * @param Person $person
     * @param StateTransitionOption $option,...
     *
     * @return PersonState
     */
    public function addPersonPersona(Person $person, StateTransitionOption $option = null)
    {
        $entity = new Gedcomx();
        $entity->addPerson($person);
        return $this->passOptionsTo('addGedcomxPersona', array($entity), func_get_args());
    }

    /**
     * Adds a persona to the current source description using a GEDCOM X object.
     *
     * @param Gedcomx $persona
     * @param StateTransitionOption $option,...
     *
     * @return PersonState
     */
    public function addGedcomxPersona(Gedcomx $persona, StateTransitionOption $option = null)
    {
        $target = $this->getSelfUri();
        $link = $this->getLink(Rel::PERSONS);
        if ($link != null && $link->getHref() != null) {
            $target = $link->getHref();
        }

        $request = $this->createAuthenticatedGedcomxRequest(Request::POST, $target);
        $request->setBody($persona->toJson());
        return $this->stateFactory->createState(
            'PersonState',
            $this->client,
            $request,
            $this->passOptionsTo('invoke', array($request), func_get_args()),
            $this->accessToken
        );
    }

    /**
     * Queries for attached references to this source description.
     *
     * @param StateTransitionOption $option,...
     *
     * @return SourceDescriptionState
     */
    public function queryAttachedReferences(StateTransitionOption $option = null)
    {
        $link = $this->getLink(Rel::SOURCE_REFERENCES_QUERY);
        if ($link == null || $link->getHref() == null) {
            return null;
        } else {
            $request = $this->createAuthenticatedGedcomxRequest(Request::GET, $link->getHref());
            return $this->stateFactory->createState(
                'SourceDescriptionState',
                $this->client,
                $request,
                $this->passOptionsTo('invoke', array($request), func_get_args()),
                $this->accessToken
            );
        }
    }

    /**
     * Reads the collection specified by this state instance.
     *
     * @param StateTransitionOption $option,...
     *
     * @return CollectionState
     */
    public function readCollection(StateTransitionOption $option = null)
    {
        $link = $this->getLink(Rel::COLLECTION);
        if ($link == null || $link->getHref() == null) {
            return null;
        }

        $request = $this->createAuthenticatedGedcomxRequest(Request::GET, $link->getHref());
        return $this->stateFactory->createState(
            'CollectionState',
            $this->client,
            $request,
            $this->passOptionsTo('invoke', array($request), func_get_args()),
            $this->accessToken
        );
    }
}