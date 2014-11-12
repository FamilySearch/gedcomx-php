<?php 

namespace Gedcomx\Extensions\FamilySearch\Rs\Client;

use Guzzle\Http\Message\Request;
use Guzzle\Http\Message\Response;

class FamilySearchPlaceState extends FamilySearchCollectionState
{
    protected function reconstruct(Request $request, Response $response)
    {
        return new FamilySearchPlaceState($this->client, $request, $response, $this->accessToken, $this->stateFactory);
    }

    public function getSelfRel()
    {
        return Rel::PLACE;
    }

    public function getScope()
    {
        return $this->getPlace();
    }

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