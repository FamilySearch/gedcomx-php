<?php


namespace Gedcomx\Rs\Client;

use Gedcomx\Gedcomx;
use Gedcomx\Records\Collection;
use Symfony\Component\Yaml\Exception\RuntimeException;

class CollectionsState extends GedcomxApplicationState
{

    function __construct($client, $request, $response, $accessToken, $stateFactory)
    {
        parent::__construct($client, $request, $response, $accessToken, $stateFactory);
    }

    protected function reconstruct($request, $response)
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
        return $this->getEntity();
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
     * @param Collection $collection The subcollection to read.
     * @return CollectionState|null
     */
    public function readSubcollection($collection)
    {
        throw new RuntimeException("function currently not implemented."); //todo: implement
    }

    /**
     * @return CollectionState|null The collection that contains these collections.
     */
    public function readCollection()
    {
        throw new RuntimeException("function currently not implemented."); //todo: implement
    }

    /**
     * @param Collection $collection the collection to add to these collections.
     * @return CollectionState|null
     */
    public function addCollection($collection)
    {
        throw new RuntimeException("function currently not implemented."); //todo: implement
    }

    /**
     * @return CollectionsState|null
     */
    public function readNextPage()
    {
        return parent::readNextPage();
    }

    /**
     * @return CollectionsState|null
     */
    public function readPreviousPage()
    {
        return parent::readPreviousPage();
    }

    /**
     * @return CollectionsState|null
     */
    public function readFirstPage()
    {
        return parent::readFirstPage();
    }

    /**
     * @return CollectionsState|null
     */
    public function readLastPage()
    {
        return parent::readLastPage();
    }


}