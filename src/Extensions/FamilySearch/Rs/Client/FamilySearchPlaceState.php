<?php 

namespace Gedcomx\Extensions\FamilySearch\Rs\Client;

use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;

/**
 * The FamilySearchPlaceState exposes management functions for a FamilySearch place.
 *
 * Class FamilySearchPlaceState
 *
 * @package Gedcomx\Extensions\FamilySearch\Rs\Client
 */
class FamilySearchPlaceState extends FamilySearchCollectionState
{
    /**
     * Clones the current state instance.
     *
     * @param \GuzzleHttp\Psr7\Request  $request
     * @param \GuzzleHttp\Psr7\Response $response
     *
     * @return \Gedcomx\Extensions\FamilySearch\Rs\Client\FamilySearchPlaceState
     */
    protected function reconstruct(Request $request, Response $response)
    {
        return new FamilySearchPlaceState($this->client, $request, $response, $this->accessToken, $this->stateFactory);
    }

    /**
     * Gets the rel name for the current state instance.
     *
     * @return string
     */
    public function getSelfRel()
    {
        return Rel::PLACE;
    }

    /**
     * Gets the main data element represented by this state instance.
     *
     * @return null
     */
    public function getScope()
    {
        return $this->getPlace();
    }

    /**
     * Gets the first place from Gedcomx::getPlaces() represented by the current state instance.
     *
     * @return null
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