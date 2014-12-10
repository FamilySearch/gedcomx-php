<?php


namespace Gedcomx\Rs\Client;

use Gedcomx\Gedcomx;
use Gedcomx\Records\Collection;
use Gedcomx\Rs\Client\Options\StateTransitionOption;
use Gedcomx\Source\SourceDescription;
use Guzzle\Http\Client;
use Guzzle\Http\Message\Request;
use Guzzle\Http\Message\Response;
use Symfony\Component\Yaml\Exception\RuntimeException;

/**
 * The CollectionsState exposes management functions for collections.
 *
 * Class CollectionsState
 *
 * @package Gedcomx\Rs\Client
 */
class CollectionsState extends GedcomxApplicationState
{
    /**
     * Constructs a new collections state using the specified client, request, response, access token, and state factory.
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
     * @return \Gedcomx\Rs\Client\CollectionsState
     */
    protected function reconstruct(Request $request, Response $response)
    {
        return new CollectionsState($this->client, $request, $response, $this->accessToken, $this->stateFactory);
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
     * @return \Gedcomx\Records\Collection[]|null
     */
    protected function getScope()
    {
        return $this->getCollections();
    }

    /**
     * Gets the collections represented by the current state instance.
     *
     * @return Collection[]|null
     */
    public function getCollections()
    {
        if ($this->entity) {
            return $this->entity->getCollections();
        }

        return null;
    }

    /**
     * Gets the source descriptions represented by the current state instance.
     *
     * @return SourceDescription[]|null
     */
    public function getSourceDescriptions()
    {
        if ($this->entity) {
            return $this->entity->getSourceDescriptions();
        }

        return null;
    }

    /**
     * Reads the specified collection.
     *
     * @param Collection|SourceDescription|null $collection
     * @param StateTransitionOption $options,...
     * @return CollectionState|null The collection that contains these collections.
     */
    public function readCollection($collection, StateTransitionOption $options = null)
    {
        $link = null;
        if ($collection === null){
            $link = $this->getLink(Rel::COLLECTION);
            if ($link == null || $link->getHref() == null)
            {
                return null;
            }
            $link = $link->getHref();
        }else if ($collection instanceof Collection) {
            $link = $collection->getLink("self");
            if ($link == null || $link->getHref() == null) {
                return null;
            }
            $link = $link->getHref();
        } else if ($collection instanceof SourceDescription){
            $link = $collection->getAbout();
            if (!$link){
                return null;
            }
        }

        $request = $this->createAuthenticatedGedcomxRequest("GET", $link);
        return $this->stateFactory->createState(
            "CollectionState",
            $this->client,
            $request,
            $this->passOptionsTo('invoke', array($request), func_get_args()),
            $this->accessToken
        );
    }

    /**
     * Updates the specified collection.
     *
     * @param Collection $collection
     * @param StateTransitionOption $options
     * @return CollectionState|null
     */
    public function updateCollection(Collection $collection, StateTransitionOption $options = null)
    {
        $link = $collection->getLink("self");
        if ($link == null || $link->getHref() == null) {
            return null;
        }

        $request = $this->createAuthenticatedGedcomxRequest("POST", $link->getHref());
        return $this->stateFactory->createState(
            "CollectionState",
            $this->client,
            $request,
            $this->passOptionsTo('invoke', array($request), func_get_args()),
            $this->accessToken
        );
    }

    /**
     * Adds a collection to the collection by this state instance.
     *
     * @param Collection $collection the collection to add to these collections.
     * @return CollectionState|null
     */
    public function addCollection($collection)
    {
        $link = $this->getLink("self");
        $href = $link === null ? null : $link->getHref() == null ? null : $link->getHref();
        $href = $href === null ? $this->getUri() : $href;

        $request = $this->createAuthenticatedGedcomxRequest("POST", $href);
        /** @var CollectionState $result */
        $result = $this->stateFactory->createState(
            "CollectionState",
            $this->client,
            $request,
            $this->passOptionsTo('invoke', array($request), func_get_args()),
            $this->accessToken
        );

        return $result->ifSuccessful();
    }
}