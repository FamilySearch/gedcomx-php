<?php


namespace Gedcomx\Rs\Client;

use Guzzle\Http\Client;
use Guzzle\Http\Message\Request;
use Guzzle\Http\Message\Response;

class StateFactory
{
    const PRODUCTION_URI = "https://familysearch.org/platform/collections/tree";
    const SANDBOX_URI = "https://sandbox.familysearch.org/platform/collections/tree";

    /**
     * @var boolean Are we in a production environment
     */
    protected $production;

    public function __construct($production = false){
        $this->production = $production;
    }

    /**
     * @param \Guzzle\Http\Client $client The client to use.
     * @param string              $method The method.
     *
     * @return CollectionState The collection state.
     */
    public function newCollectionState(Client $client = null, $method = "GET")
    {
        if (!$client) {
            $client = $this->defaultClient();
        }

        /** @var Request $request */
        $request = $client->createRequest($method, ($this->production ? self::PRODUCTION_URI : self::SANDBOX_URI));
        $request->setHeader("Accept", GedcomxApplicationState::JSON_MEDIA_TYPE);
        return new CollectionState($client, $request, $client->send($request), null, $this);
    }

    protected function defaultClient(){
        return new Client( '', array(
            "request.options" => array(
                "exceptions" => false
            )
        ));
    }

    /**
     * @param string              $uri    The URI to the person.
     * @param \Guzzle\Http\Client $client The client to use.
     * @param string              $method The method.
     *
     * @return PersonState The person state.
     */
    public function newPersonState($uri, Client $client = null, $method = "GET")
    {
        if (!$client) {
            $client = new Client();
        }

        /** @var Request $request */
        $request = $client->createRequest($method, $uri);
        $request->setHeader("Accept", GedcomxApplicationState::JSON_MEDIA_TYPE);
        return new PersonState($client, $request, $client->send($request), null, $this);
    }

    /**
     * @param string                        $class        The name of the state class to create
     * @param \Guzzle\Http\Client           $client
     * @param \Guzzle\Http\Message\Request  $request
     * @param \Guzzle\Http\Message\Response $response
     * @param string                        $accessToken The access token for this session
     *
     * @return mixed
     */
    public function createState( $class, Client $client, Request $request, Response $response, $accessToken ){
        $functionName = "build{$class}";
        return $this->$functionName($client, $request, $response, $accessToken);
    }

    /**
     * @param \Guzzle\Http\Client           $client
     * @param \Guzzle\Http\Message\Request  $request
     * @param \Guzzle\Http\Message\Response $response
     * @param string                        $accessToken The access token for this session
     *
     * @return \Gedcomx\Rs\Client\SourceDescriptionsState
     */
    protected function buildCollectionState( Client $client, Request $request, Response $response, $accessToken ){
        return new CollectionState( $client, $request, $response, $accessToken, $this );
    }

    /**
     * @param \Guzzle\Http\Client           $client
     * @param \Guzzle\Http\Message\Request  $request
     * @param \Guzzle\Http\Message\Response $response
     * @param string                        $accessToken The access token for this session
     *
     * @return \Gedcomx\Rs\Client\SourceDescriptionsState
     */
    protected function buildSourceDescriptionsState( Client $client, Request $request, Response $response, $accessToken ){
        return new SourceDescriptionsState( $client, $request, $response, $accessToken, $this );
    }

    /**
     * @param \Guzzle\Http\Client           $client
     * @param \Guzzle\Http\Message\Request  $request
     * @param \Guzzle\Http\Message\Response $response
     * @param string                        $accessToken The access token for this session
     *
     * @return \Gedcomx\Rs\Client\SourceDescriptionsState
     */
    protected function buildSourceDescriptionState( Client $client, Request $request, Response $response, $accessToken ){
        return new SourceDescriptionState( $client, $request, $response, $accessToken, $this );
    }

    /**
     * @param \Guzzle\Http\Client           $client
     * @param \Guzzle\Http\Message\Request  $request
     * @param \Guzzle\Http\Message\Response $response
     * @param string                        $accessToken The access token for this session
     *
     * @return \Gedcomx\Rs\Client\PersonsState
     */
    protected function buildPersonsState( Client $client, Request $request, Response $response, $accessToken ){
		return new PersonsState( $client, $request, $response, $accessToken, $this );
	}

    /**
     * @param \Guzzle\Http\Client           $client
     * @param \Guzzle\Http\Message\Request  $request
     * @param \Guzzle\Http\Message\Response $response
     * @param string                        $accessToken The access token for this session
     *
     * @return \Gedcomx\Rs\Client\PersonChildrenState
     */
    protected function buildPersonChildrenState( Client $client, Request $request, Response $response, $accessToken ){
        return new PersonChildrenState( $client, $request, $response, $accessToken, $this );
    }

    /**
     * @param \Guzzle\Http\Client           $client
     * @param \Guzzle\Http\Message\Request  $request
     * @param \Guzzle\Http\Message\Response $response
     * @param string                        $accessToken The access token for this session
     *
     * @return \Gedcomx\Rs\Client\PersonState
     */
    protected function buildPersonState(Client $client, Request $request, Response $response, $accessToken)
    {
        return new PersonState($client, $request, $response, $accessToken, $this);
    }

    /**
     * @param \Guzzle\Http\Client           $client
     * @param \Guzzle\Http\Message\Request  $request
     * @param \Guzzle\Http\Message\Response $response
     * @param string                        $accessToken The access token for this session
     *
     * @return \Gedcomx\Rs\Client\PersonParentsState
     */
    protected function buildPersonParentsState(Client $client, Request $request, Response $response, $accessToken)
    {
        return new PersonParentsState( $client, $request, $response, $accessToken, $this );
    }

    /**
     * @param \Guzzle\Http\Client           $client
     * @param \Guzzle\Http\Message\Request  $request
     * @param \Guzzle\Http\Message\Response $response
     * @param string                        $accessToken The access token for this session
     *
     * @return \Gedcomx\Rs\Client\PersonSpousesState
     */
    protected function buildPersonSpousesState(Client $client, Request $request, Response $response, $accessToken)
    {
        return new PersonSpousesState( $client, $request, $response, $accessToken, $this );
    }

    /**
     * @param \Guzzle\Http\Client           $client
     * @param \Guzzle\Http\Message\Request  $request
     * @param \Guzzle\Http\Message\Response $response
     * @param string                        $accessToken The access token for this session
     *
     * @return \Gedcomx\Rs\Client\AncestryResultsState
     */
    protected function buildAncestryResultsState(Client $client, Request $request, Response $response, $accessToken)
    {
        return new AncestryResultsState($client, $request, $response, $accessToken, $this);
    }

    /**
     * @param \Guzzle\Http\Client           $client
     * @param \Guzzle\Http\Message\Request  $request
     * @param \Guzzle\Http\Message\Response $response
     * @param string                        $accessToken The access token for this session
     *
     * @return \Gedcomx\Rs\Client\PersonSearchResultsState
     */
    protected function buildPersonSearchResultsState(Client $client, Request $request, Response $response, $accessToken)
    {
		return new PersonSearchResultsState( $client, $request, $response, $accessToken, $this );
	}

    /**
     * @param \Guzzle\Http\Client           $client
     * @param \Guzzle\Http\Message\Request  $request
     * @param \Guzzle\Http\Message\Response $response
     * @param string                        $accessToken The access token for this session
     *
     * @return \Gedcomx\Rs\Client\RecordState
     */
    protected function buildRecordState(Client $client, Request $request, Response $response, $accessToken)
    {
        return new RecordState( $client, $request, $response, $accessToken, $this );
    }

    /**
     * @param \Guzzle\Http\Client           $client
     * @param \Guzzle\Http\Message\Request  $request
     * @param \Guzzle\Http\Message\Response $response
     * @param string                        $accessToken The access token for this session
     *
     * @return \Gedcomx\Rs\Client\RecordState
     */
    protected function buildRelationshipState(Client $client, Request $request, Response $response, $accessToken)
    {
        return new RelationshipState( $client, $request, $response, $accessToken, $this );
    }

    /**
     * @param \Guzzle\Http\Client           $client
     * @param \Guzzle\Http\Message\Request  $request
     * @param \Guzzle\Http\Message\Response $response
     * @param string                        $accessToken The access token for this session
     *
     * @return \Gedcomx\Rs\Client\RecordState
     */
    protected function buildAgentState(Client $client, Request $request, Response $response, $accessToken)
    {
        return new AgentState( $client, $request, $response, $accessToken, $this );
    }

    /**
     * @param \Guzzle\Http\Client           $client
     * @param \Guzzle\Http\Message\Request  $request
     * @param \Guzzle\Http\Message\Response $response
     * @param string                        $accessToken The access token for this session
     *
     * @return \Gedcomx\Rs\Client\RecordState
     */
    protected function buildPlaceSearchResultState(Client $client, Request $request, Response $response, $accessToken)
    {
        return new PlaceSearchResultState( $client, $request, $response, $accessToken, $this );
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
        return new PlaceDescriptionState($client, $request, $response, $accessToken, $this);
    }

}