<?php


namespace Gedcomx\Extensions\FamilySearch\Rs\Client;

use Gedcomx\Extensions\FamilySearch\Rs\Client\Helpers\FamilySearchRequest;
use Gedcomx\Gedcomx;
use Gedcomx\Rs\Client\CollectionState;
use Gedcomx\Rs\Client\Options\StateTransitionOption;
use Gedcomx\Rs\Client\SourceDescriptionState;
use Gedcomx\Rs\Client\StateFactory;
use Gedcomx\Source\SourceDescription;
use Guzzle\Http\Client;
use Guzzle\Http\Message\Request;
use Guzzle\Http\Message\Response;

class FamilySearchSourceDescriptionState extends SourceDescriptionState
{
    function __construct(Client $client, Request $request, Response $response, $accessToken, StateFactory $stateFactory)
    {
        parent::__construct($client, $request, $response, $accessToken, $stateFactory);
    }

    protected function reconstruct(Request $request, Response $response)
    {
        return new FamilySearchSourceDescriptionState($this->client, $request, $response, $this->accessToken, $this->stateFactory);
    }

    /**
     * @param StateTransitionOption $options,...
     * @return DiscussionState|null
     */
    public function readComments(StateTransitionOption $options = null)
    {
        $link = $this->getLink(Rel::COMMENTS);
        if ($link == null || $link->getHref() == null) {
            return null;
        }

        $request = $this->createAuthenticatedRequest(Request::GET, $link->getHref());
        FamilySearchRequest::applyFamilySearchMediaType($request);

        return $this->stateFactory->createState(
            'DiscussionState',
            $this->client,
            $request,
            $this->passOptionsTo('invoke', array($request), func_get_args()),
            $this->accessToken
        );
    }

    //TODO: Create FamilysearchSourceReferencesQueryState class, add it to FamilySearchStateFactory when link is created
    ///**
    // * @return FamilySearchSourceReferencesQueryState|null
    // */
    /*
    public function readSourceReferencesQuery()
    {
        $link = $this->getLink( //TODO: Put Rel here when added );
        if ($link == null || $link->getHref() = null) {
            return null;
        }

        $request = $this->createAuthenticatedRequest(Request::GET, $link->getHref());
        FamilySearchRequest::applyFamilySearchMediaType($request);

        return $this->stateFactory->createState(
            'FamilySearchSourceReferencesQueryState',
            $this->client,
            $request,
            $this->passOptionsTo('invoke', array($request), func_get_args()),
            $this->accessToken
        );
    } */

    /**
     * @param CollectionState $collection
     * @param StateTransitionOption $options,...
     * @return FamilySearchSourceDescriptionState|null
     */
    public function moveToCollection(CollectionState $collection, StateTransitionOption $options = null)
    {
        $link = $collection->getLink(Rel::SOURCE_DESCRIPTIONS);
        if ($link == null || $link->getHref() == null) {
            return null;
        }

        /** @var SourceDescription $me */
        $me = $this->getSourceDescription();
        if ($me == null || $me->getId() == null) {
            return null;
        }

        /** @var Gedcomx $gx */
        $gx = new Gedcomx();
        $sd = new SourceDescription();
        $sd->setId($me->getId());
        $gx->setSourceDescriptions(array($sd));

        $request = $this->createAuthenticatedRequest(Request::POST, $link->getHref());
        $request->setBody($gx->toJson());
        FamilySearchRequest::applyFamilySearchMediaType($request);

        return $this->stateFactory->createState(
            'SourceDescriptionState',
            $this->client,
            $request,
            $this->passOptionsTo('invoke', array($request), func_get_args()),
            $this->accessToken
        );
    }
}