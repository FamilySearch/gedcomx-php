<?php

namespace Gedcomx\Extensions\FamilySearch\Rs\Client\FamilyTree;

use Gedcomx\Extensions\FamilySearch\Rs\Client\Helpers\FamilySearchRequest;
use Gedcomx\Rs\Client\GedcomxApplicationState;
use Guzzle\Http\Message\Request;
use Guzzle\Http\Message\Response;

class ChangeHistoryState extends GedcomxApplicationState
{

    protected function reconstruct(Request $request, Response $response)
    {
        return new ChangeHistoryState($this->client, $request, $response, $this->accessToken, $this->stateFactory);
    }

    protected function loadEntity()
    {
        $json = json_decode($this->response->getBody(), true);

        return new FamilySearchPlatform($json);
    }

    protected function getScope()
    {
        // TODO: Implement getScope() method.
    }

    /**
     * @return ChangeHistoryPage|null
     */
    public function getPage()
    {
        $feed = $this->getEntity();
        return $feed == null ? null : new ChangeHistoryPage($feed);
    }

    /**
     * @param Entry $change
     * @param StateTransitionOption $option
     * @return ChangeHistoryState
     * @throws GedcomxApplicationException
     */
    public function restoreChange(Entry $change, StateTransitionOption $option = null)
    {
        $link = $change->getLink(Rel::RESTORE);
        if ($link == null || $link->getHref() == null) {
            throw new GedcomxApplicationException("Unrestorable change: " + change . getId());
        }

        $request = $this->createAuthenticatedRequest(Request::POST, $link->getHref());
        FamilySearchRequest::applyFamilySearchMediaType($request);
        return $this->stateFactory->createState(
            'ChangeHistoryState',
            $this->client,
            $request,
            $this->passOptionsTo('invoke', array($request), func_get_args()),
            $this->accessToken
        );
    }

}