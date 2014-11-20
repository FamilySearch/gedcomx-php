<?php

	namespace Gedcomx\Extensions\FamilySearch\Rs\Client;

	use Gedcomx\Extensions\FamilySearch\FamilySearchPlatform;
	use Gedcomx\Extensions\FamilySearch\Rs\Client\FamilyTree\FamilyTreePersonState;
    use Gedcomx\Extensions\FamilySearch\Rs\Client\Util\ExperimentsFilter;
    use Gedcomx\Rs\Client\GedcomxApplicationState;
	use Gedcomx\Rs\Client\PlaceDescriptionsState;
	use Gedcomx\Rs\Client\PlaceDescriptionState;
	use Gedcomx\Rs\Client\PlaceGroupState;
	use Gedcomx\Rs\Client\PlaceSearchResultsState;
	use Gedcomx\Rs\Client\StateFactory;
	use Guzzle\Http\Client;
	use Guzzle\Http\Message\Request;
	use Guzzle\Http\Message\Response;

	class FamilySearchStateFactory extends StateFactory
	{
		const PLACES_URI = "https://familysearch.org/platform/collections/places";
		const PLACES_SANDBOX_URI = "https://sandbox.familysearch.org/platform/collections/places";

		/**
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
		 * @param Client $client
		 * @param string $method
		 *
		 * @return \Gedcomx\Extensions\FamilySearch\Rs\Client\FamilySearchPlacesState a new places state created with with the given URI
		 */
		public function newPlacesState($client = null, $method = "GET")
		{
			if (!$client) {
				$client = $this->defaultClient();
			}

			/** @var Request $request */
			$request = $client->createRequest($method, ($this->production ? self::PLACES_URI : self::PLACES_SANDBOX_URI));
			$request->setHeader("Accept", GedcomxApplicationState::JSON_MEDIA_TYPE);

			return new FamilySearchPlaces($client, $request, $client->send($request), null, $this);
		}

		/**
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