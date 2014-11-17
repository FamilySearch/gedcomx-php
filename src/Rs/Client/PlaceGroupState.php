<?php

namespace Gedcomx\Rs\Client;

use Gedcomx\Gedcomx;
use Guzzle\Http\Message\Request;
use Guzzle\Http\Message\Response;

class PlaceGroupState extends GedcomxApplicationState
{

    protected function reconstruct(Request $request, Response $response)
    {
        return new PlaceGroupState($this->client, $request, $response, $this->accessToken, $this->stateFactory);
    }

    protected function loadEntity()
    {
        $json = json_decode($this->response->getBody(), true);
        return new Gedcomx($json);
    }

    protected function getScope()
    {
        return $this->getPlaceGroup();
    }

    public function getSelfRel()
    {
        return Rel::PLACE_GROUP;
    }

    /**
     * @return \Gedcomx\Source\SourceDescription|null
     */
    public function  getPlaceGroup()
    {
        if ($this->getEntity() != null) {
            return $this->getEntity()->getSourceDescriptions();
        }

        return null;
    }
}