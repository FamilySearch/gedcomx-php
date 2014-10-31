<?php

namespace Gedcomx\Extensions\FamilySearch\Rs\Client\FamilyTree;

use Gedcomx\Extensions\FamilySearch\Rs\Client\Helpers\FamilySearchRequest;
use Gedcomx\Extensions\FamilySearch\Rs\Client\Rel;
use Gedcomx\Rs\Client\Options\StateTransitionOption;
use Gedcomx\Rs\Client\RelationshipState;
use Guzzle\Http\Message\Request;

class FamilyTreeRelationshipState extends RelationshipState implements PreferredRelationshipState
{
    /**
     * @param StateTransitionOption $option,..
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