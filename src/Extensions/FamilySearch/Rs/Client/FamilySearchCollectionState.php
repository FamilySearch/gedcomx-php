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
	use Guzzle\Http\Client;
	use Guzzle\Http\Message\Request;
	use Guzzle\Http\Message\Response;

	class FamilySearchCollectionState extends CollectionState
	{
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

			return new FamilySearchPlatform($json);
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
		 * @param string                $uri      href from a Link object
		 * @param StateTransitionOption $option,...
		 *
		 * @returns FamilyTreePersonState|null
		 */
		public function readPerson( $uri, StateTransitionOption $option = null ){
			if( $uri == null ){
				return null;
			}

			$request = $this->createAuthenticatedRequest("GET", $uri);
			FamilySearchRequest::applyFamilySearchMediaType($request);
			return $this->stateFactory->createState(
				"PersonState",
				$this->client,
				$request,
				$this->passOptionsTo('invoke', array($request),func_get_args()),
				$this->accessToken
			);
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

			$request = $this->createAuthenticatedRequest(Request::GET, $link->getHref());
			FamilySearchRequest::applyFamilySearchMediaType($request);

			return $this->stateFactory->createState(
				'UserState',
				$this->client,
				$request,
				$this->passOptionsTo('invoke', array($request), func_get_args()),
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

			$request = $this->createAuthenticatedRequest(Request::GET, $link->getHref());
			FamilySearchRequest::applyFamilySearchMediaType($request);

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

			$request = $this->createAuthenticatedRequest(Request::GET, $link->getHref());
			FamilySearchRequest::applyFamilySearchMediaType($request);

			return $this->stateFactory->createState(
				'DiscussionsState',
				$this->client,
				$request,
				$this->passOptionsTo('invoke', array($request), func_get_args()),
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
			$request = $this->createAuthenticatedRequest(Request::POST, $link->getHref());
			FamilySearchRequest::applyFamilySearchMediaType($request);
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
         * @param \Gedcomx\Links\Link                              $link
         * @param \Gedcomx\Rs\Client\Options\StateTransitionOption $option,...
         *
         * @throws \Gedcomx\Rs\Client\Exception\GedcomxApplicationException
         */
        protected function embed(Link $link, StateTransitionOption $option = null ){
            if ($link->getHref() != null) {
                $lastEmbeddedRequest = $this->createRequestForEmbeddedResource(Request::GET, $link);
                $lastEmbeddedResponse = $this->passOptionsTo('invoke',array($lastEmbeddedRequest), func_get_args());
                if ($lastEmbeddedResponse->getStatusCode() == 200) {
                    $json = json_decode($lastEmbeddedResponse->getBody(),true);
                    $this->entity->embed(new FamilySearchPlatform($json));
                }
                else if (floor($lastEmbeddedResponse->getStatusCode()/100) == 5 ) {
                    throw new GedcomxApplicationException(sprintf("Unable to load embedded resources: server says \"%s\" at %s.", $lastEmbeddedResponse->getStatusCode(), $lastEmbeddedRequest->getUrl()), $lastEmbeddedResponse);
                }
                else {
                    //todo: log a warning? throw an error?
                }
            }

        }
	}
