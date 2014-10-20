<?php

namespace Gedcomx\Extensions\FamilySearch\Rs\Client\FamilyTree;

use Gedcomx\Rs\Client\GedcomxApplicationState;
use Guzzle\Http\Message\Request;
use Guzzle\Http\Message\Response;

class ChildAndParentsRelationshipState extends GedcomxApplicationState implements PreferredRelationshipState{

    protected function reconstruct(Request $request, Response $response)
    {
        return new ChildAndParentsRelationshipState($this->client, $request, $response, $this->accessToken, $this->stateFactory);
    }

    protected function loadEntity()
    {
        $json = json_decode($this->response->getBody(), true);

        return new FamilySearchPlatform($json);
    }

    protected function getScope()
    {
        // TODO: Implement getScope() method.
    }
}