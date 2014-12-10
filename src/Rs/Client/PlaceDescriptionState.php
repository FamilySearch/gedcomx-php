<?php

namespace Gedcomx\Rs\Client;

use Gedcomx\Conclusion\PlaceDescription;
use Gedcomx\Gedcomx;
use Gedcomx\Rs\Client\Options\StateTransitionOption;
use Guzzle\Http\Message\Request;
use Guzzle\Http\Message\Response;

/**
 * The PlaceDescriptionState exposes management functions for a place description.
 */
class PlaceDescriptionState extends GedcomxApplicationState
{
    /**
     * Clones the current state instance.
     *
     * @param \Guzzle\Http\Message\Request  $request
     * @param \Guzzle\Http\Message\Response $response
     *
     * @return \Gedcomx\Rs\Client\PlaceDescriptionState
     */
    protected function reconstruct(Request $request, Response $response)
    {
        return new PlaceDescriptionState($this->client, $request, $response, $this->accessToken, $this->stateFactory);
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
     * @return PlaceDescription|null
     */
    protected function getScope()
    {
        return $this->getPlaceDescription();
    }

    /**
     * Gets the rel name for the current state instance.
     *
     * @return string
     */
    public function getSelfRel()
    {
        return Rel::DESCRIPTION;
    }

    /**
     * Gets the first place description represented by the current state instance.
     *
     * @return PlaceDescription|null
     */
    public function getPlaceDescription()
    {
        if ($this->getEntity() != null) {
            $places = $this->getEntity()->getPlaces();
            if ($places != null and count($places) > 0) {
                return $places[0];
            }
        }

        return null;
    }

    /**
     * Reads the children of the current place description.
     *
     * @param \Gedcomx\Rs\Client\Options\StateTransitionOption $option
     *
     * @return mixed|null
     */
    public function readChildren(StateTransitionOption $option = null)
    {
        $link = $this->getLink(Rel::CHILDREN);
        if ($link == null || $link->getHref() == null) {
            return null;
        }

        $request = $this->createAuthenticatedGedcomxRequest(Request::GET, $link->getHref());
        return $this->stateFactory->createState(
            'PlaceDescriptionsState',
            $this->client,
            $request,
            $this->passOptionsTo('invoke', array($request), func_get_args()),
            $this->accessToken
        );
    }
}