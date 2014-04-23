<?php


namespace Gedcomx\Rs\Api;

use Gedcomx\Gedcomx;

class CollectionState extends GedcomxApplicationState
{


    function __construct($client, $request, $response, $accessToken, $stateFactory)
    {
        parent::__construct($client, $request, $response, $accessToken, $stateFactory);
    }

    protected function reconstruct($request, $response)
    {
        return new CollectionState($this->client, $request, $response, $this->accessToken, $this->stateFactory);
    }

    protected function loadEntity()
    {
        $json = json_decode($this->response->getBody(), true);
        return new Gedcomx($json);
    }

    protected function getScope()
    {
        return $this->getCollection();
    }

    public function getCollection()
    {
        if ($this->entity) {
            $collections = $this->entity->getCollections();
            if (count($collections) > 0) {
                return $collections[0];
            }
        }

        return null;
    }

    public function readPersonForCurrentUser()
    {
        $link = $this->getLink(Rel::CURRENT_USER_PERSON);
        if (!$link) {
            return null;
        }

        $href = $link->getHref();
        if (!$href) {
            return null;
        }

        $request = $this->createAuthenticatedGedcomxRequest("GET");
        $request->setUrl($href);
        return $this->stateFactory->buildPersonState($this->client, $request, $this->client->send($request), $this->accessToken);
    }


}