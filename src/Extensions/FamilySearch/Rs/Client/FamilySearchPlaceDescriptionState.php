<?php

namespace Gedcomx\Extensions\FamilySearch\Rs\Client;

use Gedcomx\Rs\Client\PlaceDescriptionState;
use GuzzleHttp\Psr7\Request;

/**
 * The FamilySearchPlaceDescriptionState exposes management functions for a FamilySearch place description.
 *
 * Class FamilySearchPlaceDescriptionState
 *
 * @package Gedcomx\Extensions\FamilySearch\Rs\Client
 */
class FamilySearchPlaceDescriptionState extends PlaceDescriptionState
{
    /**
     * Reads the place described by the current place description.
     *
     * @param \Gedcomx\Extensions\FamilySearch\Rs\Client\StateTransitionOption $option,...
     *
     * @return mixed|null
     */
    public function readPlace(StateTransitionOption $option = null)
    {
        $link = $this->getLink(Rel::PLACE);
        $link = $link == null ? $this->getLink(Rel::SELF) : $link;
        if ($link == null || $link->getHref() == null) {
            return null;
        }

        $request = $this->createAuthenticatedGedcomxRequest('GET', $link->getHref());
        return $this->stateFactory->createState(
            'PlaceState',
            $this->client,
            $request,
            $this->passOptionsTo('invoke', array($request), func_get_args()),
            $this->accessToken);
    }
} 