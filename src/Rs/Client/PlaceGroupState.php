<?php

namespace Gedcomx\Rs\Client;

use Gedcomx\Gedcomx;
use Guzzle\Http\Message\Request;
use Guzzle\Http\Message\Response;

/**
 * The PlaceGroupState exposes management functions for a place group.
 */
class PlaceGroupState extends GedcomxApplicationState
{
    /**
     * Clones the current state instance.
     *
     * @param \Guzzle\Http\Message\Request  $request
     * @param \Guzzle\Http\Message\Response $response
     *
     * @return \Gedcomx\Rs\Client\PlaceGroupState
     */
    protected function reconstruct(Request $request, Response $response)
    {
        return new PlaceGroupState($this->client, $request, $response, $this->accessToken, $this->stateFactory);
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
        return $this->getPlaceGroup();
    }

    /**
     * Gets the rel name for the current state instance.
     *
     * @return string
     */
    public function getSelfRel()
    {
        return Rel::PLACE_GROUP;
    }

    /**
     * Gets a list of place descriptions represented by the current place group.
     *
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