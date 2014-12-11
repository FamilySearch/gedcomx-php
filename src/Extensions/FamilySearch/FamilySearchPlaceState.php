<?php

namespace Gedcomx\Extensions\FamilySearch\Rs\Client;

use Gedcomx\Extensions\FamilySearch\FamilySearchPlatform;
use Gedcomx\Extensions\FamilySearch\Rs\Client\FamilyTree\PreferredRelationshipState;
use Gedcomx\Rs\Client\GedcomxApplicationState;
use Guzzle\Http\Message\Request;
use Guzzle\Http\Message\Response;

/**
 * The FamilySearchPlaceState exposes management functions for a FamilySearch place.
 * Class FamilySearchPlaceState
 *
 * @package Gedcomx\Extensions\FamilySearch\Rs\Client
 */
class FamilySearchPlaceState extends GedcomxApplicationState implements PreferredRelationshipState
{
    /**
     * Clones the current state instance.
     *
     * @param \Guzzle\Http\Message\Request  $request
     * @param \Guzzle\Http\Message\Response $response
     *
     * @return \Gedcomx\Extensions\FamilySearch\Rs\Client\FamilySearchPlaceState
     */
    protected function reconstruct(Request $request, Response $response)
    {
        return new FamilySearchPlaceState($this->client, $request, $response, $this->accessToken, $this->stateFactory);
    }

    /**
     * Returns the entity from the REST API response.
     *
     * @return \Gedcomx\Extensions\FamilySearch\FamilySearchPlatform
     */
    protected function loadEntity()
    {
        $json = json_decode($this->response->getBody(), true);

        return new FamilySearchPlatform($json);
    }

    /**
     * Returns the entity from the REST API response.
     *
     * @return \Gedcomx\Conclusion\PlaceDescription|null
     */
    protected function getScope()
    {
        return $this->getPlace();
    }

    /**
     * Gets the first place from Gedcomx::getPlaces() represented by the current state instance.
     *
     * @return \Gedcomx\Conclusion\PlaceDescription|null
     */
    public function getPlace()
    {
        if ($this->getEntity() != null ) {
            $places = $this->getEntity()->getPlaces();
            if ($places != null && count($places) > 0) {
                return $places[0];
            }
        }

        return null;
    }
}