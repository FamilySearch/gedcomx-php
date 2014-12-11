<?php

namespace Gedcomx\Extensions\FamilySearch\Rs\Client\FamilyTree;

use Gedcomx\Extensions\FamilySearch\Rs\Client\Helpers\FamilySearchRequest;
use Gedcomx\Extensions\FamilySearch\Rs\Client\Rel;
use Gedcomx\Rs\Client\Options\StateTransitionOption;
use Gedcomx\Rs\Client\RelationshipState;
use Gedcomx\Rs\Client\StateFactory;
use Guzzle\Http\Client;
use Guzzle\Http\Message\Request;
use Guzzle\Http\Message\Response;

/**
 * The FamilyTreeRelationshipState exposes management and other FamilySearch specific functions for a relationship.
 *
 * Class FamilyTreeRelationshipState
 *
 * @package Gedcomx\Extensions\FamilySearch\Rs\Client\FamilyTree
 */
class FamilyTreeRelationshipState extends RelationshipState implements PreferredRelationshipState
{
    /**
     * Constructs a new fmaily tree relationship state using the specified client, request, response, access token, and state factory.
     *
     * @param \Guzzle\Http\Client             $client
     * @param \Guzzle\Http\Message\Request    $request
     * @param \Guzzle\Http\Message\Response   $response
     * @param string                          $accessToken
     * @param \Gedcomx\Rs\Client\StateFactory $stateFactory
     */
    public function __construct(Client $client, Request $request, Response $response, $accessToken, StateFactory $stateFactory)
    {
        parent::__construct($client, $request, $response, $accessToken, $stateFactory);
    }

    /**
     * Clones the current state instance.
     *
     * @param \Guzzle\Http\Message\Request  $request
     * @param \Guzzle\Http\Message\Response $response
     *
     * @return \Gedcomx\Extensions\FamilySearch\Rs\Client\FamilyTree\FamilyTreeRelationshipState
     */
    protected function reconstruct(Request $request, Response $response)
    {
        return new FamilyTreeRelationshipState($this->client, $request, $response, $this->accessToken, $this->stateFactory);
    }

    /**
     * Loads all discussion references for the current relationship.
     *
     * @param StateTransitionOption $options
     * @return FamilyTreeRelationshipState
     */
    public function loadDiscussionReferences(StateTransitionOption $options = null)
    {
        return parent::loadEmbeddedResources(array(Rel::DISCUSSION_REFERENCES), $options);
    }

    /**
     * Reads the change history of the current relationship.
     *
     * @param StateTransitionOption $option ,..
     *
     * @return ChangeHistoryState|null
     */
    public function readChangeHistory(StateTransitionOption $option = null)
    {
        $link = $this->getLink(Rel::CHANGE_HISTORY);
        if ($link == null || $link->getHref() == null) {
            return null;
        }

        $request = $this->createAuthenticatedFeedRequest(Request::GET, $link->getHref());
        return $this->stateFactory->createState(
            'ChangeHistoryState',
            $this->client,
            $request,
            $this->passOptionsTo('invoke', array($request), func_get_args()),
            $this->accessToken
        );
    }

    /**
     * Restore the current relationship (if it is currently deleted).
     *
     * @param \Gedcomx\Rs\Client\Options\StateTransitionOption $option,...
     *
     * @return mixed|null
     */
    public function restore(StateTransitionOption $option = null)
    {
        $link = $this->getLink(Rel::RESTORE);
        if ($link == null || $link->getHref() == null) {
            return null;
        }

        $request = $this->createAuthenticatedRequest(Request::POST, $link->getHref());
        FamilySearchRequest::applyFamilySearchMediaType($request);

        return $this->stateFactory->createState(
            'RelationshipState',
            $this->client,
            $request,
            $this->passOptionsTo('invoke', array($request), func_get_args()),
            $this->accessToken
        );
    }
}