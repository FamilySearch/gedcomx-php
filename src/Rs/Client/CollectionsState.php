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

class CollectionsState extends GedcomxApplicationState
{

    function __construct(Client $client, Request $request, Response $response, $accessToken, StateFactory $stateFactory)
    {
        parent::__construct($client, $request, $response, $accessToken, $stateFactory);
    }

    protected function reconstruct(Request $request, Response $response)
    {
        return new CollectionsState($this->client, $request, $response, $this->accessToken, $this->stateFactory);
    }

    protected function loadEntity()
    {
        $json = json_decode($this->response->getBody(), true);
        return new Gedcomx($json);
    }

    protected function getScope()
    {
        return $this->getCollections();
    }

    /**
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
     * @return SourceDescription[]|null
     */
    public function getSourceDescriptions()
    {
        if ($this->entity) {
            return $this->entity->getSourceDescriptions();
        }
    }

    /**
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