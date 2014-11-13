<?php

namespace Gedcomx\Extensions\FamilySearch\Rs\Client;

use Gedcomx\Rs\Client\PlaceDescriptionState;
use Guzzle\Http\Message\Request;

class FamilySearchPlaceDescriptionState extends PlaceDescriptionState
{
    public function readPlace(StateTransitionOption $option = null)
    {
        $link = $this->getLink(Rel::PLACE);
        $link = $link == null ? $this->getLink(Rel::SELF) : $link;
        if ($link == null || $link->getHref() == null) {
            return null;
        }

        $request = $this->createAuthenticatedGedcomxRequest(Request::GET, $link->getHref());
        return $this->stateFactory->createState(
            'PlaceState',
            $this->client,
            $request,
            $this->passOptionsTo('invoke', array($request), func_get_args()),
            $this->accessToken);
    }
} 