<?php

namespace Gedcomx\Rs\Client;

use Gedcomx\Gedcomx;
use Gedcomx\Rs\Client\Options\StateTransitionOption;
use Guzzle\Http\Message\Request;
use Guzzle\Http\Message\Response;

class PlaceDescriptionState extends GedcomxApplicationState
{

    protected function reconstruct(Request $request, Response $response)
    {
        return new PlaceDescriptionState($this->client, $request, $response, $this->accessToken, $this->stateFactory);
    }

    /**
     * @return \Gedcomx\Gedcomx
     */
    protected function loadEntity()
    {
        $json = json_decode($this->response->getBody(), true);
        return new Gedcomx($json);
    }

    protected function getScope()
    {
        return $this->getPlaceDescription();
    }

    public function getSelfRel()
    {
        return Rel::DESCRIPTION;
    }

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