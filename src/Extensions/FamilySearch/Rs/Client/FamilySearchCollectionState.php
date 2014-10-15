<?php

	namespace Gedcomx\Extensions\FamilySearch\Rs\Client;

	use Gedcomx\Common\TextValue;
	use Gedcomx\Conclusion\DateInfo;
	use Gedcomx\Rs\Client\CollectionState;
	use Gedcomx\Rs\Client\Util\GedcomxPersonSearchQueryBuilder;
	use Guzzle\Http\Client;
	use Guzzle\Http\Message\Request;
	use Guzzle\Http\Message\Response;

	class FamilySearchCollectionState extends CollectionState
	{
		const FAMILYSEARCH_MEDIA_TYPE = 'application/x-fs-v1+json';

		function __construct(Client $client, Request $request, Response $response, $accessToken, FamilySearchStateFactory $stateFactory)
		{
			parent::__construct($client, $request, $response, $accessToken, $stateFactory);
		}

		/**
		 * @param Request  $request
		 * @param Response $response
		 *
		 * @return FamilySearchCollectionState
		 */
		protected function reconstruct(Request $request, Response $response)
		{
			return new FamilySearchCollectionState($this->client, $request, $response, $this->accessToken, $this->stateFactory);
		}

		protected function loadEntity()
		{
			$json = json_decode($this->response->getBody(), true);

			return new Gedcomx($json);
		}

		protected function getScope()
		{
			return $this->getCollection();
		}

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
				array()
			);

			$request = $this->createRequest(Request::GET, $uri);
			$response = $this->passOptionsTo('invoke', array($request), func_get_args());
			$dateValue = new DateInfo();
			$dateValue->setOriginal($date);
			$dateValue->addNormalizedExtension(new TextValue($response->getEntity()));
			$headers = $response->getHeaders();
			if ($headers != null && isset($headers["Location"])) {
				$dateValue->setFormal($headers["Location"]);

				return $dateValue;
			}

			return null;
		}

		/**
		 * @param StateTransitionOption $option
		 *
		 * @return UserState|null
		 */
		public function readCurrentUser(StateTransitionOption $option = null)
		{
			$link = $this->getLink(Rel::CURRENT_USER);
			if ($link == null || $link->getHref() == null) {
				return null;
			}

			$request = $this->createAuthenticatedFamilyTreeRequest(Request::GET, $link->getHref());

			return $this->stateFactory->createState(
				'UserState',
				$this->client,
				$request,
				$this->passOptionsTo('invoke', $request, func_get_args()),
				$this->accessToken
			);
		}

		/**
		 * @param StateTransitionOption $option
		 *
		 * @return UserHistoryState|null
		 */
		public function readCurrentUserHistory(StateTransitionOption $option = null)
		{
			$link = $this->getLink(Rel::CURRENT_USER_HISTORY);
			if ($link == null || $link->getHref() == null) {
				return null;
			}

			$request = $this->createAuthenticatedFamilyTreeRequest(Request::GET, $link->getHref());

			return $this->stateFactory->createState(
				'UserHistoryState',
				$this->client,
				$request,
				$this->passOptionsTo('invoke', array($request), func_get_args()),
				$this->accessToken
			);
		}

		/**
		 * @param GedcomxPersonSearchQueryBuilder|string $query
		 * @param StateTransitionOption                  $option
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

			$request = $this->createAuthenticatedFeedRequest(Request::GET, $uri);

			return $this->stateFactory->createState(
				'PersonMatchResultsState',
				$this->client,
				$request,
				$this->passOptionsTo('invoke', array($request), func_get_args()),
				$this->accessToken
			);
		}

		/**
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

			$request = $this->createAuthenticatedFamilyTreeRequest(Request::GET, $link->getHref());

			return $this->stateFactory->createState(
				'DiscussionsState',
				$this->client,
				$request,
				$this->passOptionsTo('invoke', $request, func_get_args()),
				$this->accessToken
			);
		}

		/**
		 * @param Discussion            $discussion
		 * @param StateTransitionOption $option
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
			$request = $this->createAuthenticatedFamilyTreeRequest(Request::POST, $link->getHref());
			$request->setBody($entity->toJson());

			return $this->stateFactory->createState(
				'DiscussionState',
				$this->client,
				$request,
				$this->passOptionsTo('invoke', array($request), func_get_args()),
				$this->accessToken
			);
		}

		/**
		 * @param string       $method The http method.
		 * @param string|array $uri    string with an href, or an array with template info
		 *
		 * @return Request The request.
		 */
		protected function createAuthenticatedFamilyTreeRequest($method, $uri)
		{
			$request = $this->createAuthenticatedRequest($method, $uri);
			$request->setHeader('Accept', self::FAMILYSEARCH_MEDIA_TYPE);
			$request->setHeader('Content-Type', self::FAMILYSEARCH_MEDIA_TYPE);

			return $request;
		}

	}