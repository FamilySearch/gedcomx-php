<?php

	namespace Gedcomx\Extensions\FamilySearch\Rs\Client;

	use Gedcomx\Common\TextValue;
	use Gedcomx\Conclusion\DateInfo;
	use Gedcomx\Extensions\FamilySearch\Platform\Discussions\Discussion;
	use Gedcomx\Extensions\FamilySearch\FamilySearchPlatform;
	use Gedcomx\Extensions\FamilySearch\Rs\Client\Helpers\FamilySearchRequest;
  use Gedcomx\Links\Link;
  use Gedcomx\Rs\Client\CollectionState;
	use Gedcomx\Rs\Client\Exception\GedcomxApplicationException;
	use Gedcomx\Rs\Client\Options\StateTransitionOption;
	use Gedcomx\Rs\Client\Util\GedcomxPersonSearchQueryBuilder;
	use Gedcomx\Rs\Client\Options\QueryParameter;
	use GuzzleHttp\Client;
	use GuzzleHttp\Psr7\Request;
	use GuzzleHttp\Psr7\Response;

	/**
	 * The FamilySearchCollectionState is a collection of FamilySearch resources and exposes management of those resources.
	 *
	 * Class FamilySearchCollectionState
	 *
	 * @package Gedcomx\Extensions\FamilySearch\Rs\Client
	 */
	class FamilySearchCollectionState extends CollectionState
	{
		/**
		 * Constructs a new FamilySearch collection state using the specified client, request, response, access token, and state factory.
		 *
		 * @param \GuzzleHttp\Client                                                 $client
		 * @param \GuzzleHttp\Psr7\Request                                        $request
		 * @param \GuzzleHttp\Psr7\Response                                       $response
		 * @param string                                                              $accessToken
		 * @param \Gedcomx\Extensions\FamilySearch\Rs\Client\FamilySearchStateFactory $stateFactory
		 */
		function __construct(Client $client, Request $request, Response $response, $accessToken, FamilySearchStateFactory $stateFactory)
		{
			parent::__construct($client, $request, $response, $accessToken, $stateFactory);
		}

		/**
		 * Clones the current state instance.
		 *
		 * @param Request  $request
		 * @param Response $response
		 *
		 * @return FamilySearchCollectionState
		 */
		protected function reconstruct(Request $request, Response $response)
		{
			return new FamilySearchCollectionState($this->client, $request, $response, $this->accessToken, $this->stateFactory);
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
		 * Gets the main data element represented by this state instance.
		 *
		 * @return null
		 */
		protected function getScope()
		{
			return $this->getCollection();
		}

		/**
		 * Gets the first collection from $this->entity->getCollections().
		 *
		 * @return null
		 */
		public function getCollection()
		{
			if ($this->entity) {
				$collections = $this->entity->getCollections();
				if (count($collections) > 0) {
					return $collections[0];
				}
			}

			return null;
		}

		/**
		 * Normalizes the specified date to a DateInfo.
		 *
		 * @param string                $date
		 * @param StateTransitionOption $option,...
		 *
		 * @return \Gedcomx\Conclusion\DateInfo|null
		 */
		public function normalizeDate($date, StateTransitionOption $option = null)
		{
			$link = $this->getLink(Rel::NORMALIZED_DATE);
			if ($link == null || $link->getTemplate() == null) {
				return null;
			}
			$uri = array(
				$link->getTemplate(),
				array('date' => $date)
			);

			$request = $this->createRequest('GET', $uri);
			$response = $this->passOptionsTo('invoke', array($request), func_get_args());
			$dateValue = new DateInfo();
			$dateValue->setOriginal($date);
			$dateValue->addNormalizedExtension(new TextValue(array('value' => $response->getBody())));
			$headers = $response->getHeaders();
			if ($headers != null && isset($headers["Location"])) {
				$dateValue->setFormal($headers["Location"][0]);

				return $dateValue;
			}

			return null;
		}

		/**
		 * Reads the person specified by the URI.
		 *
		 * @param string                $uri      href from a Link object
		 * @param StateTransitionOption $option,...
		 *
		 * @returns \Gedcomx\Extensions\FamilySearch\Rs\Client\FamilyTree\FamilyTreePersonState|null
		 */
		public function readPerson( $uri, StateTransitionOption $option = null ){
			if( $uri == null ){
				return null;
			}

			$request = $this->createAuthenticatedRequest("GET", $uri, FamilySearchRequest::getMediaTypes());
			return $this->stateFactory->createState(
				"PersonState",
				$this->client,
				$request,
				$this->passOptionsTo('invoke', array($request),func_get_args()),
				$this->accessToken
			);
		}

		/**
		 * Reads the current tree user data.
		 *
		 * @param StateTransitionOption $option,...
		 *
		 * @return UserState|null
		 */
		public function readCurrentUser(StateTransitionOption $option = null)
		{
			$link = $this->getLink(Rel::CURRENT_USER);
			if ($link == null || $link->getHref() == null) {
				return null;
			}

			$request = $this->createAuthenticatedRequest('GET', $link->getHref(), FamilySearchRequest::getMediaTypes());

			return $this->stateFactory->createState(
				'UserState',
				$this->client,
				$request,
				$this->passOptionsTo('invoke', array($request), func_get_args()),
				$this->accessToken
			);
		}

		/**
		 * Reads the current user's history.
		 *
		 * @param StateTransitionOption $option,...
		 *
		 * @return UserHistoryState|null
		 */
		public function readCurrentUserHistory(StateTransitionOption $option = null)
		{
			$link = $this->getLink(Rel::CURRENT_USER_HISTORY);
			if ($link == null || $link->getHref() == null) {
				return null;
			}

			$request = $this->createAuthenticatedRequest('GET', $link->getHref(), FamilySearchRequest::getMediaTypes());

			return $this->stateFactory->createState(
				'UserHistoryState',
				$this->client,
				$request,
				$this->passOptionsTo('invoke', array($request), func_get_args()),
				$this->accessToken
			);
		}

		/**
		 * Searches for person matches based off the specified query.
		 *
		 * @param GedcomxPersonSearchQueryBuilder|string $query
		 * @param StateTransitionOption                  $option,...
		 *
		 * @return PersonMatchResultsState
		 */
		public function searchForPersonMatches($query, StateTransitionOption $option = null)
		{
			$link = $this->getLink(Rel::PERSON_MATCHES_QUERY);
			if ($link == null || $link->getTemplate() == null) {
				return null;
			}
			if ($query instanceof GedcomxPersonSearchQueryBuilder) {
				$queryString = $query->build();
			} else {
				$queryString = $query;
			}

			$uri = array(
				$link->getTemplate(),
				array("q" => $queryString)
			);

			$request = $this->createAuthenticatedFeedRequest('GET', $uri);

			return $this->stateFactory->createState(
				'PersonMatchResultsState',
				$this->client,
				$request,
				$this->passOptionsTo('invoke', array($request), func_get_args()),
				$this->accessToken
			);
		}

		/**
		 * Reads the discussions on the current collection.
		 *
		 * @param StateTransitionOption $option,...
		 *
		 * @return Discussion|null
		 */
		public function readDiscussions(StateTransitionOption $option = null)
		{
			$link = $this->getLink(Rel::DISCUSSIONS);
			if ($link == null || $link->getHref() == null) {
				return null;
			}

			$request = $this->createAuthenticatedRequest('GET', $link->getHref(), FamilySearchRequest::getMediaTypes());

			return $this->stateFactory->createState(
				'DiscussionsState',
				$this->client,
				$request,
				$this->passOptionsTo('invoke', array($request), func_get_args()),
				$this->accessToken
			);
		}

		/**
		 * Adds a discussion to the current collection.
		 *
		 * @param Discussion            $discussion
		 * @param StateTransitionOption $option,...
		 *
		 * @return DiscussionState
		 * @throws GedcomxApplicationException
		 */
		public function addDiscussion(Discussion $discussion, StateTransitionOption $option = null)
		{
			$link = $this->getLink(Rel::DISCUSSIONS);
			if ($link == null || $link->getHref() == null) {
				throw new GedcomxApplicationException("Unable to add discussion: missing link.");
			}

			$entity = new FamilySearchPlatform();
			$entity->addDiscussion($discussion);
			$request = $this->createAuthenticatedRequest('POST', $link->getHref(), FamilySearchRequest::getMediaTypes(), null, $entity->toJson());

			return $this->stateFactory->createState(
				'DiscussionState',
				$this->client,
				$request,
				$this->passOptionsTo('invoke', array($request), func_get_args()),
				$this->accessToken
			);
		}
		
		/**
		 * Adds processing time for the current user. Useful for testing throttling.
		 * 
		 * @param integer $time Additional time in milliseconds
		 * @param StateTransitionOption $option,...
		 * 
		 * @return FamilySearchCollectionState
		 */
		public function addProcessingTime($time, StateTransitionOption $option = null){
			$request = $this->createAuthenticatedRequest('GET', '/platform/throttled');
			$request = (new QueryParameter(false, 'processingTime', $time))->apply($request);
			return new FamilySearchCollectionState(
				$this->client, 
				$request, 
				$this->passOptionsTo('invoke', array($request), func_get_args()), 
				$this->accessToken, 
				$this->stateFactory
			);
		}

    /**
	 * Executes the specified link and embeds the response in the current Gedcomx entity.
	 *
     * @param \Gedcomx\Links\Link                              $link
     * @param \Gedcomx\Rs\Client\Options\StateTransitionOption $option,...
     *
     * @throws \Gedcomx\Rs\Client\Exception\GedcomxApplicationException
     */
		protected function embed(Link $link, StateTransitionOption $option = null ){
			if ($link->getHref() != null) {
				$lastEmbeddedRequest = $this->createRequestForEmbeddedResource('GET', $link);
				$lastEmbeddedResponse = $this->passOptionsTo('invoke',array($lastEmbeddedRequest), func_get_args());
				if ($lastEmbeddedResponse->getStatusCode() == 200) {
					$json = json_decode($lastEmbeddedResponse->getBody(),true);
					$this->entity->embed(new FamilySearchPlatform($json));
				}
				else if (floor($lastEmbeddedResponse->getStatusCode()/100) == 5 ) {
					throw new GedcomxApplicationException(sprintf("Unable to load embedded resources: server says \"%s\" at %s.", $lastEmbeddedResponse->getStatusCode(), $lastEmbeddedRequest->getUri()), $lastEmbeddedResponse);
				}
				else {
					//todo: log a warning? throw an error?
				}
			}

		}
	}