<?php

	namespace Gedcomx\Extensions\FamilySearch\Rs\Client;

	use Gedcomx\Extensions\FamilySearch\FamilySearchPlatform;
	use Gedcomx\Extensions\FamilySearch\Rs\Client\FamilyTree\FamilyTreePersonState;
	use Gedcomx\Extensions\FamilySearch\Rs\Client\Memories\FamilySearchMemories;
	use Gedcomx\Gedcomx;
	use Gedcomx\Rs\Client\GedcomxApplicationState;
    use Gedcomx\Extensions\FamilySearch\Rs\Client\Util\ExperimentsFilter;
	use Gedcomx\Rs\Client\PlaceDescriptionsState;
	use Gedcomx\Rs\Client\PlaceGroupState;
	use Gedcomx\Rs\Client\PlaceSearchResultsState;
	use Gedcomx\Rs\Client\StateFactory;
	use Guzzle\Http\Client;
	use Guzzle\Http\Message\Request;
	use Guzzle\Http\Message\Response;

	/**
	 * The state factory is responsible for instantiating state classes from REST API responses.
	 *
	 * Class FamilySearchStateFactory
	 *
	 * @package Gedcomx\Extensions\FamilySearch\Rs\Client
	 */
	class FamilySearchStateFactory extends StateFactory
	{
		/**
		 * The default production environment URI for the places collection.
		 */
		const PLACES_URI = "https://familysearch.org/platform/collections/places";
		/**
		 * The default sandbox environment URI for the places collection.
		 */
		const PLACES_SANDBOX_URI = "https://sandbox.familysearch.org/platform/collections/places";
		/**
		 * The default production environment URI for the memories collection.
		 */
		const MEMORIES_URI = "https://familysearch.org/platform/collections/memories";
		/**
		 * The default sandbox environment URI for the memories collection.
		 */
		const MEMORIES_SANDBOX_URI = "https://sandbox.familysearch.org/platform/collections/memories";

		/**
		 * Creates a new FamilySearch collection state from the specified parameters. Since a response is provided as a parameter, a REST API request will not be invoked.
		 *
		 * @param string              $uri
		 * @param string              $method The method.
		 * @param \Guzzle\Http\Client $client The client to use.
		 *
		 * @return FamilySearchCollectionState The collection state.
		 */
		public function newCollectionState($uri = null, $method = "GET", Client $client = null)
		{
			if (!$client) {
				$client = $this->defaultClient();
			}
			if ($uri == null) {
				$uri = $this->production ? self::PRODUCTION_URI : self::SANDBOX_URI;
			}

			/** @var Request $request */
			$request = $client->createRequest($method, $uri);
			$request->setHeader("Accept", FamilySearchPlatform::JSON_MEDIA_TYPE);
			return new FamilySearchCollectionState($client, $request, $client->send($request), null, $this);
		}

		/**
		 * Loads the default client for executing REST API requests.
		 *
		 * @return \Gedcomx\Util\FilterableClient
		 */
        protected function defaultClient()
        {
            $client = parent::defaultClient();

            //how to add an experiment:
            $client->addFilter(new ExperimentsFilter(array("birth-date-not-considered-death-declaration")));

            return $client;
        }

        /**
		 * Create a new places state with the given URI
		 *
		 * @param string $uri
		 * @param string $method
		 * @param Client $client
		 *
		 * @return \Gedcomx\Extensions\FamilySearch\Rs\Client\FamilySearchPlaces
		 */
		public function newPlacesState($uri = null, $method = "GET", Client $client = null)
		{
			if (!$client) {
				$client = $this->defaultClient();
			}

			if ($uri == null) {
				$uri = $this->production ? self::PLACES_URI : self::PLACES_SANDBOX_URI;
			}

			/** @var Request $request */
			$request = $client->createRequest($method, $uri);
			$request->setHeader("Accept", Gedcomx::JSON_MEDIA_TYPE);

			return new FamilySearchPlaces($client, $request, $client->send($request), null, $this);
		}

		/**
		 * Create a new memories state
		 *
		 * @param null   $uri
		 * @param string $method
		 * @param Client $client
		 *
		 * @return \Gedcomx\Extensions\FamilySearch\Rs\Client\Memories\FamilySearchMemories
		 */
		public function newMemoriesState($uri = null, $method = "GET", Client $client = null)
		{
			if (!$client) {
				$client = $this->defaultClient();
			}

			if ($uri == null) {
				$uri = $this->production ? self::MEMORIES_URI : self::MEMORIES_SANDBOX_URI;
			}

			/** @var Request $request */
			$request = $client->createRequest($method, $uri);
			$request->setHeader("Accept", Gedcomx::JSON_MEDIA_TYPE);

			return new FamilySearchMemories($client, $request, $client->send($request), null, $this);
		}

		/**
		 * Builds a new person state from the specified parameters.
		 *
		 * @param \Guzzle\Http\Client           $client
		 * @param \Guzzle\Http\Message\Request  $request
		 * @param \Guzzle\Http\Message\Response $response
		 * @param string                        $accessToken The access token for this session
		 *
		 * @return \Gedcomx\Extensions\FamilySearch\Rs\Client\DiscussionsState
		 */
		protected function buildPersonState(Client $client, Request $request, Response $response, $accessToken)
		{
			return new FamilyTreePersonState($client, $request, $response, $accessToken, $this);
		}

		/**
		 * Builds a new discussions state from the specified paramters.
		 *
		 * @param \Guzzle\Http\Client           $client
		 * @param \Guzzle\Http\Message\Request  $request
		 * @param \Guzzle\Http\Message\Response $response
		 * @param string                        $accessToken The access token for this session
		 *
		 * @return \Gedcomx\Extensions\FamilySearch\Rs\Client\DiscussionsState
		 */
		protected function buildDiscussionsState(Client $client, Request $request, Response $response, $accessToken)
		{
			return new DiscussionsState($client, $request, $response, $accessToken, $this);
		}

		/**
		 * Builds a new discussion state from the specified parameters.
		 *
		 * @param \Guzzle\Http\Client           $client
		 * @param \Guzzle\Http\Message\Request  $request
		 * @param \Guzzle\Http\Message\Response $response
		 * @param string                        $accessToken The access token for this session
		 *
		 * @return \Gedcomx\Extensions\FamilySearch\Rs\Client\DiscussionState
		 */
		protected function buildDiscussionState(Client $client, Request $request, Response $response, $accessToken)
		{
			return new DiscussionState($client, $request, $response, $accessToken, $this);
		}

		/**
		 * Builds a new user state from the specified parameters.
		 *
		 * @param \Guzzle\Http\Client           $client
		 * @param \Guzzle\Http\Message\Request  $request
		 * @param \Guzzle\Http\Message\Response $response
		 * @param string                        $accessToken The access token for this session
		 *
		 * @return \Gedcomx\Extensions\FamilySearch\Rs\Client\UserState
		 */
		protected function buildUserState(Client $client, Request $request, Response $response, $accessToken)
		{
			return new UserState($client, $request, $response, $accessToken, $this);
		}

		/**
		 * Builds a new user history state from the specified parameters.
		 *
		 * @param \Guzzle\Http\Client           $client
		 * @param \Guzzle\Http\Message\Request  $request
		 * @param \Guzzle\Http\Message\Response $response
		 * @param string                        $accessToken The access token for this session
		 *
		 * @return \Gedcomx\Extensions\FamilySearch\Rs\Client\UserHistoryState
		 */
		protected function buildUserHistoryState(Client $client, Request $request, Response $response, $accessToken)
		{
			return new UserHistoryState($client, $request, $response, $accessToken, $this);
		}

		/**
		 * Builds a new person merge state from the specified parameters.
		 *
		 * @param \Guzzle\Http\Client           $client
		 * @param \Guzzle\Http\Message\Request  $request
		 * @param \Guzzle\Http\Message\Response $response
		 * @param string                        $accessToken The access token for this session
		 *
		 * @return \Gedcomx\Extensions\FamilySearch\Rs\Client\PersonMergeState
		 */
		protected function buildPersonMergeState(Client $client, Request $request, Response $response, $accessToken)
		{
			return new PersonMergeState($client, $request, $response, $accessToken, $this);
		}

		/**
		 * Builds a new person match results state from the specified parameters.
		 *
		 * @param \Guzzle\Http\Client           $client
		 * @param \Guzzle\Http\Message\Request  $request
		 * @param \Guzzle\Http\Message\Response $response
		 * @param string                        $accessToken The access token for this session
		 *
		 * @return \Gedcomx\Extensions\FamilySearch\Rs\Client\PersonMatchResultsState
		 */
		protected function buildPersonMatchResultsState(Client $client, Request $request, Response $response, $accessToken)
		{
			return new PersonMatchResultsState($client, $request, $response, $accessToken, $this);
		}

		/**
		 * Builds a new source description state from the specified parameters.
		 *
		 * @param \Guzzle\Http\Client           $client
		 * @param \Guzzle\Http\Message\Request  $request
		 * @param \Guzzle\Http\Message\Response $response
		 * @param string                        $accessToken The access token for this session
		 *
		 * @return \Gedcomx\Extensions\FamilySearch\Rs\Client\FamilySearchSourceDescriptionState
		 */
		protected function buildSourceDescriptionState(Client $client, Request $request, Response $response, $accessToken)
		{
			return new FamilySearchSourceDescriptionState($client, $request, $response, $accessToken, $this);
		}

		/**
		 * Builds a new person non-matches state from the specified parameters.
		 *
		 * @param \Guzzle\Http\Client           $client
		 * @param \Guzzle\Http\Message\Request  $request
		 * @param \Guzzle\Http\Message\Response $response
		 * @param string                        $accessToken The access token for this session
		 *
		 * @return \Gedcomx\Extensions\FamilySearch\Rs\Client\PersonNonMatchesState
		 */
		protected function buildPersonNonMatchesState(Client $client, Request $request, Response $response, $accessToken)
		{
			return new PersonNonMatchesState($client, $request, $response, $accessToken, $this);
		}

		/**
		 * Builds a new place search results state from the specified parameters.
		 *
		 * @param \Guzzle\Http\Client           $client
		 * @param \Guzzle\Http\Message\Request  $request
		 * @param \Guzzle\Http\Message\Response $response
		 * @param string                        $accessToken The access token for this session
		 *
		 * @return \Gedcomx\Rs\Client\PlaceSearchResultsState
		 */
		protected function buildPlaceSearchResultsState(Client $client, Request $request, Response $response, $accessToken)
		{
			return new PlaceSearchResultsState($client, $request, $response, $accessToken, $this);
		}

		/**
		 * Builds a new place state from the specified parameters.
		 *
		 * @param \Guzzle\Http\Client           $client
		 * @param \Guzzle\Http\Message\Request  $request
		 * @param \Guzzle\Http\Message\Response $response
		 * @param string                        $accessToken The access token for this session
		 *
		 * @return \Gedcomx\Extensions\FamilySearch\Rs\Client\FamilySearchPlaces
		 */
		protected function buildPlaceState(Client $client, Request $request, Response $response, $accessToken)
		{
			return new FamilySearchPlaces($client, $request, $response, $accessToken, $this);
		}

		/**
		 * Builds a new place description state from the specified parameters.
		 *
		 * @param \Guzzle\Http\Client           $client
		 * @param \Guzzle\Http\Message\Request  $request
		 * @param \Guzzle\Http\Message\Response $response
		 * @param string                        $accessToken The access token for this session
		 *
		 * @return \Gedcomx\Rs\Client\PlaceDescriptionState
		 */
		protected function buildPlaceDescriptionState(Client $client, Request $request, Response $response, $accessToken)
		{
			return new FamilySearchPlaceDescriptionState($client, $request, $response, $accessToken, $this);
		}

		/**
		 * Builds a new place descriptions state from the specified parameters.
		 * @param \Guzzle\Http\Client           $client
		 * @param \Guzzle\Http\Message\Request  $request
		 * @param \Guzzle\Http\Message\Response $response
		 * @param string                        $accessToken The access token for this session
		 *
		 * @return \Gedcomx\Rs\Client\PlaceDescriptionsState
		 */
		protected function buildPlaceDescriptionsState(Client $client, Request $request, Response $response, $accessToken)
		{
			return new PlaceDescriptionsState($client, $request, $response, $accessToken, $this);
		}

		/**
		 * Builds a new place group state from the specified parameters.
		 * @param \Guzzle\Http\Client           $client
		 * @param \Guzzle\Http\Message\Request  $request
		 * @param \Guzzle\Http\Message\Response $response
		 * @param string                        $accessToken The access token for this session
		 *
		 * @return \Gedcomx\Rs\Client\PlaceDescriptionsState
		 */
		protected function buildPlaceGroupState(Client $client, Request $request, Response $response, $accessToken)
		{
			return new PlaceGroupState($client, $request, $response, $accessToken, $this);
		}
	}