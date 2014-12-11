<?php

namespace Gedcomx\Extensions\FamilySearch\Rs\Client;

use Gedcomx\Extensions\FamilySearch\FamilySearchPlatform;
use Gedcomx\Extensions\FamilySearch\Platform\Discussions\Comment;
use Gedcomx\Extensions\FamilySearch\Platform\Discussions\Discussion;
use Gedcomx\Extensions\FamilySearch\Rs\Client\Helpers\FamilySearchRequest;
use Gedcomx\Links\Link;
use Gedcomx\Records\Collection;
use Gedcomx\Rs\Client\Exception\GedcomxApplicationException;
use Gedcomx\Rs\Client\GedcomxApplicationState;
use Gedcomx\Rs\Client\Options\StateTransitionOption;
use Guzzle\Http\Client;
use Guzzle\Http\Message\Request;
use Guzzle\Http\Message\Response;

/**
 * The DiscussionState exposes management functions for a discussion.
 *
 * Class DiscussionState
 *
 * @package Gedcomx\Extensions\FamilySearch\Rs\Client
 */
class DiscussionState extends GedcomxApplicationState
{
    /**
     * Constructs a new discussion state using the specified client, request, response, access token, and state factory.
     *
     * @param \Guzzle\Http\Client                                                 $client
     * @param \Guzzle\Http\Message\Request                                        $request
     * @param \Guzzle\Http\Message\Response                                       $response
     * @param string                                                              $accessToken
     * @param \Gedcomx\Extensions\FamilySearch\Rs\Client\FamilySearchStateFactory $stateFactory
     */
    public function __construct(Client $client, Request $request, Response $response, $accessToken, FamilySearchStateFactory $stateFactory)
    {
        parent::__construct($client, $request, $response, $accessToken, $stateFactory);
    }

    /**
     * Clones the current state instance.
     *
     * @param \Guzzle\Http\Message\Request  $request
     * @param \Guzzle\Http\Message\Response $response
     *
     * @return \Gedcomx\Extensions\FamilySearch\Rs\Client\DiscussionState
     */
    protected function reconstruct(Request $request, Response $response)
    {
        return new DiscussionState($this->client, $request, $response, $this->accessToken, $this->stateFactory);
    }

    /**
     * Gets the main data element represented by this state instance.
     *
     * @return \Gedcomx\Extensions\FamilySearch\Platform\Discussions\Discussion
     */
    protected function getScope()
    {
        return $this->getDiscussion();
    }

    /**
     * Gets the first discussion from the current entity (FamilySearchPlatform::getDiscussions()).
     *
     * @return Discussion
     */
    public function getDiscussion()
    {
        if ($this->entity) {
            $discussions = $this->entity->getDiscussions();
            if (count($discussions) > 0) {
                return $discussions[0];
            }
        }

        return null;
    }

    /**
     * Creates a new discussion and only sets the discussion ID to the current discussion's ID.
     *
     * @return Discussion
     */
    protected function createEmptySelf()
    {
        $discussion = new Discussion();
        $discussion->setId($this->getLocalSelfId());
        return $discussion;
    }

    /**
     * Gets the current discussion ID.
     *
     * @return string|null
     */
    protected function getLocalSelfId()
    {
        $me = $this->getDiscussion();
        return $me == null ? null : $me->getId();
    }

    /**
     * Returns the entity from the REST API response.
     *
     * @return \Gedcomx\Extensions\FamilySearch\FamilySearchPlatform
     */
    protected function loadEntity()
    {
        $json = json_decode($this->response->getBody(), true);

        return new FamilySearchPlatform($json);
    }

    /**
     * Loads the comments for the current discussion.
     *
     * @param StateTransitionOption $option
     * @return DiscussionState
     */
    public function loadComments(StateTransitionOption $option = null)
    {
        $link = $this->getLink(Rel::COMMENTS);
        if ($this->entity != null && $link != null && $link->getHref() != null) {
            $this->passOptionsTo('embed', array($link), func_get_args());
        }

        return $this;
    }

    /**
     * Creates a REST API request (with appropriate authentication headers).
     *
     * @param string $method
     * @param Link $link
     * @return Request
     */
    protected function createRequestForEmbeddedResource($method, Link $link)
    {
        $request = $this->createAuthenticatedGedcomxRequest($method, $link->getHref());
        FamilySearchRequest::applyFamilySearchMediaType($request);

        return $request;
    }

    /**
     * Updates the specified discussion.
     *
     * @param \Gedcomx\Extensions\FamilySearch\Platform\Discussions\Discussion $discussion
     * @param \Gedcomx\Rs\Client\Options\StateTransitionOption                 $option
     *
     * @return mixed
     */
    public function update(Discussion $discussion, StateTransitionOption $option = null)
    {
        return $this->passOptionsTo('updateInternal', array($discussion, Rel::SELF), func_get_args());
    }

    /**
     * Adds a comment to the current discussion.
     *
     * @param String $comment
     * @param \Gedcomx\Rs\Client\Options\StateTransitionOption $option
     *
     * @return DiscussionState
     */
    public function addCommentString($comment, StateTransitionOption $option = null)
    {
        return $this->passOptionsTo('addComments', array(array(new Comment($comment))), func_get_args());
    }

    /**
     * Adds a comment to the current discussion.
     *
     * @param Comment $comment
     * @param StateTransitionOption $option
     *
     * @return DiscussionState
     */
    public function addComment(Comment $comment, StateTransitionOption $option = null)
    {
        return $this->passOptionsTo('addComments', array(array($comment)), func_get_args());
    }

    /**
     * Adds the specified comments to the current discussion.
     *
     * @param array $comments
     * @param StateTransitionOption $option
     * @return DiscussionState
     */
    public function addComments(array $comments, StateTransitionOption $option = null)
    {
        $discussion = $this->createEmptySelf();
        $discussion->setComments($comments);
        return $this->passOptionsTo('updateInternal', array($discussion, Rel::COMMENTS), func_get_args());
    }

    /**
     * Updates the specified comment on the current discussion.
     *
     * @param Comment $comment
     * @param StateTransitionOption $option
     * @return DiscussionState
     */
    public function updateComment(Comment $comment, StateTransitionOption $option = null)
    {
        return $this->passOptionsTo('updateComments', array(array($comment)), func_get_args());
    }

    /**
     * Updates the specified comments on the current discussion.
     *
     * @param array $comments
     * @param StateTransitionOption $option
     * @return DiscussionState
     */
    public function updateComments(array $comments, StateTransitionOption $option = null)
    {
        $discussion = $this->createEmptySelf();
        $discussion->setComments($comments);
        return $this->passOptionsTo('updateInternal', array($discussion,Rel::COMMENTS), func_get_args());
    }

    /**
     * Updates the specified discussion.
     *
     * @param Discussion $discussion
     * @param string $rel
     * @param StateTransitionOption $option
     * @return DiscussionState
     */
    protected function updateInternal(Discussion $discussion, $rel, StateTransitionOption $option = null)
    {
        $target = $this->getSelfUri();
        $link = $this->getLink($rel);
        if ($link != null && $link->getHref() != null) {
            $target = $link->getHref();
        }

        $fsp = new FamilySearchPlatform();
        $fsp->setDiscussions(array($discussion));
        $request = $this->createAuthenticatedRequest(Request::POST, $target);
        FamilySearchRequest::applyFamilySearchMediaType($request);
        $request->setBody($fsp->toJson());
        return $this->stateFactory->createState(
            'DiscussionState',
            $this->client,
            $request,
            $this->passOptionsTo('invoke', array($request), func_get_args()),
            $this->accessToken
        );
    }

    /**
     * Deletes the specified comment from the current discussion.
     *
     * @param Comment $comment
     * @param StateTransitionOption $option
     * @return DiscussionState
     * @throws GedcomxApplicationException
     */
    public function deleteComment(Comment $comment, StateTransitionOption $option = null)
    {
        $link = $comment->getLink(Rel::COMMENT);
        $link = $link == null ? $comment->getLink(Rel::SELF) : $link;
        if ($link == null || $link->getHref() == null) {
            throw new GedcomxApplicationException("Comment cannot be deleted: missing $link.");
        }

        $request = $this->createAuthenticatedGedcomxRequest(Request::DELETE, $link->getHref());
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