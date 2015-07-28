<?php 

namespace Gedcomx\Rs\Client;

use Gedcomx\Conclusion\PlaceDescription;
use Gedcomx\Gedcomx;
use Gedcomx\Rs\Client\Options\StateTransitionOption;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;

/**
 * The PlaceDescriptionsState exposes management functions for place descriptions.
 */
class PlaceDescriptionsState extends GedcomxApplicationState
{
    /**
     * Clones the current state instance.
     *
     * @param \GuzzleHttp\Psr7\Request  $request
     * @param \GuzzleHttp\Psr7\Response $response
     *
     * @return \Gedcomx\Rs\Client\PlaceDescriptionsState
     */
    protected function reconstruct(Request $request, Response $response)
    {
        return new PlaceDescriptionsState($this->client, $request, $response, $this->accessToken, $this->stateFactory);
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
     * @return object
     */
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
     * Adds a place description to the current collection of place descriptions.
     *
     * @param \Gedcomx\Conclusion\PlaceDescription             $place
     * @param \Gedcomx\Rs\Client\Options\StateTransitionOption $option,...
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

    /**
     * Reads the collection specified by this state instance.
     *
     * @param \Gedcomx\Rs\Client\Options\StateTransitionOption $option,...
     *
     * @return mixed|null
     */
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