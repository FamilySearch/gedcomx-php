<?php

	namespace Gedcomx\Extensions\FamilySearch\Rs\Client;

	use Gedcomx\Extensions\FamilySearch\Rs\Client\StateFactory;
	use Guzzle\Http\Client;
	use Guzzle\Http\Message\Request;
	use Guzzle\Http\Message\Response;

	class FamilySearchStateFactory extends StateFactory
	{

		/**
		 * Create a new places state with the given URI
		 *
		 * @param string $uri the discovery URI for places
		 * @param Client $client
		 * @param string $method
		 *
		 * @return \Gedcomx\Extensions\FamilySearch\Rs\Client\PlacesState a new places state created with with the given URI
		 */
		public function newPlacesState($uri, $client = null, $method = "GET")
		{
			if ($client == null) {
				$client = new Client();
			}
			$request = $client->createRequest($method, $uri);
			$request->setHeader("Accept", GedcomxApplicationState::GEDCOMX_MEDIA_TYPE);

			return new PlacesState($client, $request, $client->send($request), null, $this);
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
		 * @return \Gedcomx\Extensions\FamilySearch\Rs\Client\FamilySearchCollectionState
		 */
		protected function buildCollectionState(Client $client, Request $request, Response $response, $accessToken)
		{
			return new FamilySearchCollectionState($client, $request, $response, $accessToken, $this);
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
		 * @return \Gedcomx\Extensions\FamilySearch\Rs\Client\FamilySearchPlaceState
		 */
		protected function buildPlaceState(Client $client, Request $request, Response $response, $accessToken)
		{
			return new FamilySearchPlaceState($client, $request, $response, $accessToken, $this);
		}

		/**
		 * @param \Guzzle\Http\Client           $client
		 * @param \Guzzle\Http\Message\Request  $request
		 * @param \Guzzle\Http\Message\Response $response
		 * @param string                        $accessToken The access token for this session
		 *
		 * @return \Gedcomx\Extensions\FamilySearch\Rs\Client\FamilySearchPlaceDescriptionState
		 */
		protected function buildPlaceDescriptionState(Client $client, Request $request, Response $response, $accessToken)
		{
			return new FamilySearchPlaceDescriptionState($client, $request, $response, $accessToken, $this);
		}
	}