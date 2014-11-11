<?php 

namespace Gedcomx\Extensions\FamilySearch\Rs\Client;

use Gedcomx\Extensions\FamilySearch\Rs\Client\Helpers\FamilySearchRequest;
use Gedcomx\Rs\Client\Exception\GedcomxInvalidQueryParameter;
use Gedcomx\Rs\Client\GedcomxSearchQuery;
use Gedcomx\Rs\Client\Options\StateTransitionOption;
use Gedcomx\Rs\Client\Util\GedcomxPlaceSearchQueryBuilder;
use Guzzle\Http\Message\Request;
use Guzzle\Http\Message\Response;

class FamilySearchPlaces extends FamilySearchCollectionState
{
    protected function reconstruct(Request $request, Response $response)
    {
        return new FamilySearchPlaces($this->client, $request, $response, $this->accessToken, $this->stateFactory);
    }

    /**
     * Read the list of place type groups
     *
     * @param \Gedcomx\Rs\Client\Options\StateTransitionOption $option,...
     *
     * @return \Gedcomx\Rs\Client\VocabElementListState|null the list of place type groups
     */
    public function readPlaceTypeGroups(StateTransitionOption $option = null) {
        return $this->passOptionsTo('readPlaceElementList', array(Rel::PLACE_TYPE_GROUPS), func_get_args());
    }

    /**
     * Read the list of place types
     *
     * @param \Gedcomx\Rs\Client\Options\StateTransitionOption $option,...
     *
     * @return \Gedcomx\Rs\Client\VocabElementListState the list of place types
     */
    public function readPlaceTypes(StateTransitionOption $option = null)
    {
        return $this->passOptionsTo('readPlaceElementList', array(Rel::PLACE_TYPES), func_get_args());
    }

    /**
     * Read the VocabElementList from the given path
     *
     * @param string                                           $path
     * @param \Gedcomx\Rs\Client\Options\StateTransitionOption $option
     *
     * @return \Gedcomx\Rs\Client\VocabElementListState|null
     */
    private function readPlaceElementList($path, StateTransitionOption $option = null)
    {
        $link = $this->getLink($path);
        if (null == $link || null == $link->getTemplate()) {
            return null;
        }

        $uri = array(
            $link->getTemplate(),
            array(
                "one" => "two"
            )
        );

        $request = $this->createAuthenticatedRequest(Request::GET, $uri);
        FamilySearchRequest::applyFamilySearchMediaType($request);

        return $this->stateFactory->createState(
            'VocabElementListState',
            $this->client,
            $request,
            $this->passOptionsTo('invoke', array($request), func_get_args()),
            $this->accessToken
        );
    }

    /**
     * Read the place type group with the given id
     *
     * @param string                                           $id
     * @param \Gedcomx\Rs\Client\Options\StateTransitionOption $option
     *
     * @return \Gedcomx\Rs\Client\VocabElementListState|null the place type group with the given id
     */
    public function readPlaceTypeGroupById($id, StateTransitionOption $option = null)
    {
        $link = $this->getLink(Rel::PLACE_TYPE_GROUP);
        if ($link == null || $link->getTemplate() == null) {
            return null;
        }

        $uri = array(
            $link->getTemplate(),
            array(
                'ptgid' => $id
            )
        );

        $request = $this->createAuthenticatedRequest(Request::GET, $uri);
        FamilySearchRequest::applyFamilySearchMediaType($request);

        return $this->stateFactory->createState(
            'VocabElementListState',
            $this->client,
            $request,
            $this->passOptionsTo('invoke', array($request), func_get_args()),
            $this->accessToken
        );
    }

    /**
     * Read the place type with the given id
     *
     * @param string                                           $id
     * @param \Gedcomx\Rs\Client\Options\StateTransitionOption $option
     *
     * @return \Gedcomx\Rs\Client\VocabElementState|null the place type group with the given id
     */
    public function readPlaceTypeById($id, StateTransitionOption $option = null)
    {
        $link = $this->getLink(Rel::PLACE_TYPE);
        if ($link == null || $link->getTemplate() == null) {
            return null;
        }

        $uri = array(
            $link->getTemplate(),
            array(
                "ptid" => $id
            )
        );

        $request = $this->createAuthenticatedRequest(Request::GET, $uri);

        return $this->stateFactory->createState(
            'VocabElementState',
            $this->client,
            $request,
            $this->passOptionsTo('invoke', array($request), func_get_args()),
            $this->accessToken
        );
    }

    /**
     * Return the PlaceGroup with the given id
     *
     * @param string                                           $id
     * @param \Gedcomx\Rs\Client\Options\StateTransitionOption $option
     *
     * @return \Gedcomx\Rs\Client\PlaceGroupState|null
     */
    public function readPlaceGroupById($id, StateTransitionOption $option = null)
    {
        $link = $this->getLink(Rel::PLACE_GROUP);
        if ($link == null || $link->getTemplate() == null) {
            return null;
        }

        $uri = array(
            $link->getTemplate(),
            array(
                "pgid" => $id
            )
        );

        $request = $this->createAuthenticatedRequest(Request::GET, $uri);

        return $this->stateFactory->createState(
            'PlaceGroupState',
            $this->client,
            $request,
            $this->passOptionsTo('invoke', array($request), func_get_args()),
            $this->accessToken
        );
    }

    /**
     * @param GedcomxSearchQuery|string $query
     * @param StateTransitionOption     $option,...
     *
     * @throws \Gedcomx\Rs\Client\Exception\GedcomxInvalidQueryParameter
     * @return \Gedcomx\Rs\Client\PlaceSearchResultsState|null
     */
    public function searchForPlaces($query, StateTransitionOption $option = null )
    {
        $searchLink = $this->getLink(Rel::PLACE_SEARCH);
        if ($searchLink === null || $searchLink->getTemplate() === null) {
            return null;
        }
        if ($query instanceof GedcomxPlaceSearchQueryBuilder) {
            $queryString = $query->build();
        } elseif (is_string($query)) {
            $queryString = $query;
        } else {
            throw new GedcomxInvalidQueryParameter($query);
        }

        $uri = array(
            $searchLink->getTemplate(),
            array(
                "q" => $queryString
            )
        );

        $request = $this->createAuthenticatedFeedRequest("GET", $uri);
        return $this->stateFactory->createState(
            "PlaceSearchResultsState",
            $this->client,
            $request,
            $this->passOptionsTo('invoke', array($request), func_get_args()),
            $this->accessToken
        );
    }

}