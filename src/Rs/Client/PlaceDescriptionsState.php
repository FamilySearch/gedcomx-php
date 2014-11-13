<?php 

namespace Gedcomx\Rs\Client;

use Gedcomx\Conclusion\PlaceDescription;
use Gedcomx\Gedcomx;
use Gedcomx\Rs\Client\Options\StateTransitionOption;
use Guzzle\Http\Message\Request;
use Guzzle\Http\Message\Response;

class PlaceDescriptionsState extends GedcomxApplicationState
{

    protected function reconstruct(Request $request, Response $response)
    {
        return new PlaceDescriptionsState($this->client, $request, $response, $this->accessToken, $this->stateFactory);
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
        return $this->getEntity();
    }

    public function getPlaceDescriptions()
    {
        if( $this->getEntity() != null ){
            return $this->getEntity()->getPlaces();
        }

        return null;
    }

    /**
     * @param \Gedcomx\Conclusion\PlaceDescription             $place
     * @param \Gedcomx\Rs\Client\Options\StateTransitionOption $option
     *
     * @return PlaceDescriptionState
     */
    public function addPlaceDescription(PlaceDescription $place, StateTransitionOption $option = null)
    {
        $entity = new Gedcomx();
        $entity->addPlace($place);
        $request = $this->createAuthenticatedGedcomxRequest(Request::POST, $this->getSelfUri());
        $request->setBody($entity->toJson());
        return $this->stateFactory->createState(
            'PlaceDescriptionState',
            $this->client,
            $request,
            $this->passOptionsTo('invoke', array($request), func_get_args()),
            $this->accessToken
        );
    }

    public function readCollection(StateTransitionOption $option = null)
    {
        $link = $this->getLink(Rel::COLLECTION);
        if ($link == null || $link->getHref() == null) {
            return null;
        }

        $request = $this->createAuthenticatedGedcomxRequest(Request::GET, $link->getHref());
        return $this->stateFactory->createState(
            'CollectionState',
            $this->client,
            $request,
            $this->passOptionsTo('invoke', array($request), func_get_args()),
            $this->accessToken
        );
    }
}