<?php


namespace Gedcomx\Rs\Client;

use Guzzle\Http\Client;

class StateFactory
{

    function __construct()
    {
    }

    /**
     * @param string $uri The URI to the collection.
     * @param Client $client The client to use.
     * @param string $method The method.
     * @return CollectionState The collection state.
     */
    public function newCollectionState($uri, $client = null, $method = "GET")
    {
        if (!$client) {
            $client = new Client();
        }

        $request = $client->createRequest($method, $uri);
        $request->setHeader("Accept", GedcomxApplicationState::GEDCOMX_MEDIA_TYPE);
        return new CollectionState($client, $request, $client->send($request), null, $this);
    }

    /**
     * @param string $uri The URI to the person.
     * @param Client $client The client to use.
     * @param string $method The method.
     * @return PersonState The person state.
     */
    public function newPersonState($uri, $client = null, $method = "GET")
    {
        if (!$client) {
            $client = new Client();
        }

        $request = $client->createRequest($method, $uri);
        $request->setHeader("Accept", GedcomxApplicationState::GEDCOMX_MEDIA_TYPE);
        return new PersonState($client, $request, $client->send($request), null, $this);
    }

	function buildPersonsState( $client, $request, $response, $accessToken ){
		return new PersonsState( $client, $request, $response, $accessToken, $this );
	}

    function buildPersonState($client, $request, $response, $accessToken)
    {
        return new PersonState($client, $request, $response, $accessToken, $this);
    }
    
    public function buildAncestryResultsState($client, $request, $response, $accessToken)
    {
        return new AncestryResultsState($client, $request, $response, $accessToken, $this);
    }

	public function buildPersonSearchResultsState($client, $request, $response, $accessToken)
    {
		return new PersonSearchResultsState( $client, $request, $response, $accessToken, $this );
	}

    public function buildRecordState($client, $request, $response, $accessToken)
    {
        return new RecordState( $client, $request, $response, $accessToken, $this );
    }

}