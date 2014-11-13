<?php

namespace Gedcomx\Extensions\FamilySearch\Rs\Client\FamilyTree;

use Gedcomx\Atom\Entry;
use Gedcomx\Atom\Feed;
use Gedcomx\Extensions\FamilySearch\Rs\Client\Helpers\FamilySearchRequest;
use Gedcomx\Extensions\FamilySearch\Rs\Client\Rel;
use Gedcomx\Extensions\FamilySearch\Rs\Client\Util\ChangeHistoryPage;
use Gedcomx\Rs\Client\Exception\GedcomxApplicationException;
use Gedcomx\Rs\Client\GedcomxApplicationState;
use Gedcomx\Rs\Client\Options\StateTransitionOption;
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

        return new Feed($json);
    }

    protected function getScope()
    {
        return $this->getEntity();
    }

    /**
     * @return ChangeHistoryPage|null
     */
    public function getPage()
    {
        /** @var Feed $feed */
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
            throw new GedcomxApplicationException("Unrestorable change: " . $change->getId());
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