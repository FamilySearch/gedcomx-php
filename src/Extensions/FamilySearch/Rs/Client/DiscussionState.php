<?php

namespace Gedcomx\Extensions\FamilySearch\Rs\Client;

use Gedcomx\Extensions\FamilySearch\FamilySearchPlatform;
use Gedcomx\Extensions\FamilySearch\Platform\Discussions\Comment;
use Gedcomx\Extensions\FamilySearch\Platform\Discussions\Discussion;
use Gedcomx\Extensions\FamilySearch\Rs\Client\Helpers\FamilySearchRequest;
use Gedcomx\Links\Link;
use Gedcomx\Rs\Client\Exception\GedcomxApplicationException;
use Gedcomx\Rs\Client\GedcomxApplicationState;
use Gedcomx\Rs\Client\Options\StateTransitionOption;
use Gedcomx\Rs\Client\Util\HttpStatus;
use Guzzle\Http\Client;
use Guzzle\Http\Message\EntityEnclosingRequest;
use Guzzle\Http\Message\Request;
use Guzzle\Http\Message\Response;

class DiscussionState extends GedcomxApplicationState
{

    public function __construct(Client $client, Request $request, Response $response, $accessToken, FamilySearchStateFactory $stateFactory)
    {
        parent::__construct($client, $request, $response, $accessToken, $stateFactory);
    }

    protected function reconstruct(Request $request, Response $response)
    {
        return new DiscussionState($this->client, $request, $response, $this->accessToken, $this->stateFactory);
    }

    /**
     * @return FamilySearchPlatform
     */
    protected function loadEntity()
    {
        if ($this->response->getStatusCode() == HttpStatus::OK) {
            return $this->getEntity();
        }

        return null;
    }

    protected function getScope()
    {
        return $this->getDiscussion();
    }

    /**
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
     * @return Discussion
     */
    protected function createEmptySelf()
    {
        $discussion = new Discussion();
        $discussion->setId($this->getLocalSelfId());
        return $discussion;
    }

    /**
     * @return string|null
     */
    protected function getLocalSelfId()
    {
        $me = $this->getDiscussion();
        return $me == null ? null : $me->getId();
    }

    /**
     * @param StateTransitionOption $option
     * @return DiscussionState
     */
    public function loadComments(StateTransitionOption $option = null)
    {
        $link = $this->getLink(Rel::COMMENTS);
        if ($this->entity != null && $link != null && $link->getHref() != null) {
            $this->passOptionsTo('embed', array($link, $this->entity), func_get_args());
        }

        return $this;
    }

    /**
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

    public function update(Discussion $discussion, StateTransitionOption $option = null)
    {
        $fsp = new FamilySearchPlatform();
        $fsp->addDiscussion($discussion);

        $request = $this->createAuthenticatedRequest(Request::POST, $this->getSelfUri());
        FamilySearchRequest::applyFamilySearchMediaType($request);
        /** @var EntityEnclosingRequest $request */
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
     * @param array $comments
     * @param StateTransitionOption $option
     * @return DiscussionState
     */
    public function addComments(array $comments, StateTransitionOption $option = null)
    {
        $discussion = $this->createEmptySelf();
        $discussion->setComments($comments);
        return $this->passOptionsTo('updateDiscussion', array($discussion), func_get_args());
    }

    /**
     * @param Comment $comment
     * @param StateTransitionOption $option
     * @return DiscussionState
     */
    public function updateComment(Comment $comment, StateTransitionOption $option = null)
    {
        return $this->passOptionsTo('updateComments', array(array($comment)), func_get_args());
    }

    /**
     * @param array $comments
     * @param StateTransitionOption $option
     * @return DiscussionState
     */
    public function updateComments(array $comments, StateTransitionOption $option = null)
    {
        $discussion = $this->createEmptySelf();
        $discussion->setComments($comments);
        return $this->passOptionsTo('updateDiscussion', array($discussion), func_get_args());
    }

    /**
     * @param Discussion $discussion
     * @param StateTransitionOption $option
     * @return DiscussionState
     */
    protected function updateDiscusson(Discussion $discussion, StateTransitionOption $option = null)
    {
        $target = $this->getSelfUri();
        $link = $this->getLink(Rel::COMMENTS);
        if ($link != null && $link->getHref() != null) {
            $target = $link->getHref();
        }

        $gx = new FamilySearchPlatform();
        $gx->setDiscussions(array($discussion));
        $request = $this->createAuthenticatedRequest(Request::POST, $target);
        return $this->stateFactory->createState(
            'DiscussionState',
            $this->client,
            $request,
            $this->passOptionsTo('invoke', array($request), func_get_args()),
            $this->accessToken
        );
    }

    /**
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
        return $this->stateFactory->createState(
            'DiscussionState',
            $this->client,
            $request,
            $this->passOptionsTo('invoke', array($request), func_get_args()),
            $this->accessToken
        );
    }
}