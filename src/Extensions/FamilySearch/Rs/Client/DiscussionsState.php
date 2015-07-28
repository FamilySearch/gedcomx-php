<?php

namespace Gedcomx\Extensions\FamilySearch\Rs\Client;

use Gedcomx\Extensions\FamilySearch\FamilySearchPlatform;
use Gedcomx\Extensions\FamilySearch\Rs\Client\Helpers\FamilySearchRequest;
use Gedcomx\Rs\Client\GedcomxApplicationState;
use Gedcomx\Rs\Client\Util\HttpStatus;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;

/**
 * The DiscussionsState exposes management functions for discussions.
 *
 * Class DiscussionsState
 *
 * @package Gedcomx\Extensions\FamilySearch\Rs\Client
 */
class DiscussionsState extends GedcomxApplicationState {
    /**
     * Clones the current state instance.
     *
     * @param \GuzzleHttp\Psr7\Request  $request
     * @param \GuzzleHttp\Psr7\Response $response
     *
     * @return \Gedcomx\Extensions\FamilySearch\Rs\Client\DiscussionsState
     */
    protected function reconstruct(Request $request, Response $response)
    {
        /** @var \Gedcomx\Rs\Client\StateFactory $stateFactory */
        return new DiscussionsState($this->client, $request, $response, $this->accessToken, $this->stateFactory);
    }

    /**
     * Returns the entity from the REST API response.
     *
     * @return \Gedcomx\Extensions\FamilySearch\FamilySearchPlatform|null
     */
    protected function loadEntity()
    {
        $entity = null;
        if ($this->response->getStatusCode() == HttpStatus::OK) {
            $json = json_decode($this->response->getBody(), true);
            $entity = new FamilySearchPlatform($json);
        }

        return $entity;
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

    /**
     * Gets the current discussions represented by the current instance.
     *
     * @return mixed
     */
    public function getDiscussions(){
        return $this->getEntity()->getDiscussions();
    }

    /**
     * Reads the collection specified by this state instance.
     *
     * @param StateTransitionOption $option,...
     *
     * @return CollectionState|null
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

    /**
     * Adds a discussion to this discussions state instance.
     *
     * @param Discussion            $discussion
     * @param StateTransitionOption $option,...
     *
     * @return DiscussionState
     */
    public function addDiscussion(Discussion $discussion, StateTransitionOption $option = null) {
        $entity = new FamilySearchPlatform();
        $entity->addDiscussion($discussion);

        $request = $this->createAuthenticatedRequest(Request::POST, $this->getSelfUri());
        $request->setBody($entity->toJson());
        FamilySearchRequest::applyFamilySearchMediaType($request);

        return $this->stateFactory->createState(
            'DiscussionState',
            $this->client,
            $request,
            $this->passOptionsTo('invoke', array($request), func_get_args()),
            $this->accessToken
        );
    }
}