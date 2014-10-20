<?php


namespace Gedcomx\Extensions\FamilySearch\Rs\Client;


use Gedcomx\Extensions\FamilySearch\FamilySearchPlatform;
use Gedcomx\Extensions\FamilySearch\Rs\Client\FamilyTree\PreferredRelationshipState;
use Gedcomx\Rs\Client\GedcomxApplicationState;
use Guzzle\Http\Message\Request;
use Guzzle\Http\Message\Response;

class FamilySearchPlaceState extends GedcomxApplicationState implements PreferredRelationshipState{

    protected function reconstruct(Request $request, Response $response)
    {
        return new FamilySearchPlaceState($this->client, $request, $response, $this->accessToken, $this->stateFactory);
    }

    protected function loadEntity()
    {
        $json = json_decode($this->response->getBody(), true);

        return new FamilySearchPlatform($json);
    }

    protected function getScope()
    {
        throw new \RuntimeException('FamilySearchPlatform::getScope');
    }
}