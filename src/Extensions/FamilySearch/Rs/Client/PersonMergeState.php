<?php

namespace Gedcomx\Extensions\FamilySearch\Rs\Client;

use Gedcomx\Extensions\FamilySearch\FamilySearchPlatform;
use Gedcomx\Extensions\FamilySearch\Platform\Tree\Merge;
use Gedcomx\Extensions\FamilySearch\Rs\Client\Helpers\FamilySearchRequest;
use Gedcomx\Rs\Client\Options\StateTransitionOption;
use Guzzle\Http\Message\Request;
use Guzzle\Http\Message\Response;

class PersonMergeState extends FamilySearchCollectionState{

    protected function reconstruct(Request $request, Response $response)
    {
        /** @var \Gedcomx\Rs\Client\StateFactory $stateFactory */
        return new PersonMergeState($this->client, $request, $response, $this->accessToken, $this->stateFactory);
    }

    protected function getScope()
    {
        return $this->getEntity();
    }

    /**
     * @return \Gedcomx\Extensions\FamilySearch\Platform\Tree\MergeAnalysis|null
     */
    public function getAnalysis()
    {
        if ($this->getEntity() != null && $this->getEntity()->getMergeAnalyses() != null) {
            $analysis = $this->getEntity()->getMergeAnalyses();
            if (count($analysis) > 0) {
                return $analysis[0];
            }
        }

        return null;
    }

    /**
     * If there is no 'Allow' header, it is allowed
     *
     * @returns boolean
     */
    public function isAllowed()
    {
        $header = $this->getResponse()->getHeader("Allow");
        if ($header == null) {
            return false;
        }

        $values = $header->toArray();
        return  count($values) > 0 && strpos(strtoupper($values[0]), Request::POST) !== false;
    }

    /**
     * @param \Gedcomx\Rs\Client\Options\StateTransitionOption $option,...
     *
     * @return PersonMergeState|null
     */
    public function readMergeMirror(StateTransitionOption $option = null)
    {
        $link = $this->getLink(Rel::MERGE_MIRROR);
        if ($link == null || $link->getHref() == null) {
            return null;
        }

        $request = $this->createAuthenticatedRequest(Request::GET, $link->getHref());
        FamilySearchRequest::applyFamilySearchMediaType($request);

        return $this->stateFactory->createState(
            'PersonMergeState',
            $this->client,
            $request,
            $this->passOptionsTo('invoke', array($request), func_get_args()),
            $this->accessToken
        );
    }

    /**
     * @param \Gedcomx\Rs\Client\Options\StateTransitionOption $option,...
     *
     * @return \Gedcomx\Rs\Client\PersonState|null
     */
    public function readSurvivor(StateTransitionOption $option = null)
    {
        $link = $this->getLink(Rel::PERSON);
        if ($link == null || $link->getHref() == null) {
            return null;
        }

        $request = $this->createAuthenticatedRequest(Request::GET, $link->getHref());

        return $this->stateFactory->createState(
            'PersonState',
            $this->client,
            $request,
            $this->passOptionsTo('invoke', array($request), func_get_args()),
            $this->accessToken
        );
    }

    /**
     * @param \Gedcomx\Extensions\FamilySearch\Platform\Tree\Merge $merge
     * @param \Gedcomx\Rs\Client\Options\StateTransitionOption     $option,...
     *
     * @return PersonMergeState
     */
    public function doMerge(Merge $merge, StateTransitionOption $option = null)
    {
        $entity = new FamilySearchPlatform();
        $entity->addMerge($merge);

        return $this->passOptionsTo('mergeEntity', array($entity), func_get_args());
    }

    /**
     * @param \Gedcomx\Extensions\FamilySearch\FamilySearchPlatform $entity
     * @param \Gedcomx\Rs\Client\Options\StateTransitionOption      $option,...
     *
     * @return PersonMergeState
     */
    public function mergeEntity(FamilySearchPlatform $entity, StateTransitionOption $option = null)
    {
        return $this->passOptionsTo('post', array($entity), func_get_args());
    }

}