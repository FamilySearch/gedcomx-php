<?php

namespace Gedcomx\Extensions\FamilySearch\Rs\Client;

use Gedcomx\Extensions\FamilySearch\FamilySearchPlatform;
use Gedcomx\Extensions\FamilySearch\Rs\Client\Helpers\FamilySearchRequest;
use Gedcomx\Rs\Client\GedcomxApplicationState;
use Gedcomx\Rs\Client\Util\HttpStatus;
use Guzzle\Http\Message\Request;
use Guzzle\Http\Message\Response;

class DiscussionsState extends GedcomxApplicationState {

    protected function reconstruct(Request $request, Response $response)
    {
        /** @var \Gedcomx\Rs\Client\StateFactory $stateFactory */
        return new DiscussionsState($this->client, $request, $response, $this->accessToken, $this->stateFactory);
    }

    protected function loadEntity()
    {
        $entity = null;
        if ($this->response->getStatusCode() == HttpStatus::OK) {
            $json = json_decode($this->response->getBody(), true);
            $entity = new FamilySearchPlatform($json);
        }

        return $entity;
    }

    protected function getScope()
    {
        return $this->getEntity();
    }

    public function getDiscussions(){
        return $this->getEntity()->getDiscussions();
    }

    /**
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