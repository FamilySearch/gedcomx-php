<?php


namespace Gedcomx\Extensions\FamilySearch\Rs\Client;

use Gedcomx\Extensions\FamilySearch\Rs\Client\Helpers\FamilySearchRequest;
use Gedcomx\Gedcomx;
use Gedcomx\Rs\Client\CollectionState;
use Gedcomx\Rs\Client\Options\StateTransitionOption;
use Gedcomx\Rs\Client\SourceDescriptionState;
use Gedcomx\Rs\Client\StateFactory;
use Gedcomx\Source\SourceDescription;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;

/**
 * The FamilySearchSourceDescriptionState exposes management functions for a FamilySearch source description.
 *
 * Class FamilySearchSourceDescriptionState
 *
 * @package Gedcomx\Extensions\FamilySearch\Rs\Client
 */
class FamilySearchSourceDescriptionState extends SourceDescriptionState
{
    /**
     * Constructs a new FamilySearch source description state using the specified client, request, response, access token, and state factory.
     *
     * @param \GuzzleHttp\Client             $client
     * @param \GuzzleHttp\Psr7\Request    $request
     * @param \GuzzleHttp\Psr7\Response   $response
     * @param string                          $accessToken
     * @param \Gedcomx\Rs\Client\StateFactory $stateFactory
     */
    function __construct(Client $client, Request $request, Response $response, $accessToken, StateFactory $stateFactory)
    {
        parent::__construct($client, $request, $response, $accessToken, $stateFactory);
    }

    /**
     * Clones the current state instance.
     *
     * @param \GuzzleHttp\Psr7\Request  $request
     * @param \GuzzleHttp\Psr7\Response $response
     *
     * @return \Gedcomx\Extensions\FamilySearch\Rs\Client\FamilySearchSourceDescriptionState
     */
    protected function reconstruct(Request $request, Response $response)
    {
        return new FamilySearchSourceDescriptionState($this->client, $request, $response, $this->accessToken, $this->stateFactory);
    }

    /**
     * Reads the comments on the current source description.
     *
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
     * Moves the current source description to the specified collection.
     *
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