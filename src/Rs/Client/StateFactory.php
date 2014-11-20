<?php


namespace Gedcomx\Rs\Client;

use Gedcomx\Rs\Client\Util\Log4PhpLoggingFilter;
use Gedcomx\Util\FilterableClient;
use Guzzle\Http\Client;
use Guzzle\Http\Message\Request;
use Guzzle\Http\Message\Response;

class StateFactory
{
    const PRODUCTION_URI = "https://familysearch.org/platform/collections/tree";
    const SANDBOX_URI = "https://sandbox.familysearch.org/platform/collections/tree";
    const PRODUCTION_DISCOVERY_URI = "https://familysearch.org/platform/collection";
    const SANDBOX_DISCOVERY_URI = "https://sandbox.familysearch.org/platform/collection";
    const ENABLE_LOG4PHP_LOGGING_ENV_NAME = "enableLog4PhpLogging";        // env variable/property to set

    /**
     * @var boolean Are we in a production environment
     */
    protected $production;

    public function __construct($production = false)
    {
        $this->production = $production;
    }

    /**
     * @param string              $uri    Optional URI
     * @param \Guzzle\Http\Client $client The client to use.
     * @param string $method The method.
     *
     * @return CollectionState The collection state.
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
        $request->setHeader("Accept", GedcomxApplicationState::JSON_MEDIA_TYPE);
        return new CollectionState($client, $request, $client->send($request), null, $this);
    }

    /**
     * @param string              $uri    Optional URI
     * @param \Guzzle\Http\Client $client The client to use.
     * @param string              $method The method.
     *
     * @return CollectionState The collection state.
     */
    public function newDiscoveryState($uri = null, $method = "GET", Client $client = null)
    {
        if (!$client) {
            $client = $this->defaultClient();
        }
        if ($uri == null) {
            $uri = $this->production ? self::PRODUCTION_DISCOVERY_URI : self::SANDBOX_DISCOVERY_URI;
        }

        /** @var Request $request */
        $request = $client->createRequest($method, $uri);
        $request->setHeader("Accept", GedcomxApplicationState::JSON_MEDIA_TYPE);
        return new CollectionState($client, $request, $client->send($request), null, $this);
    }

    protected function defaultClient()
    {
        $opts = array(
            "request.options" => array(
                "exceptions" => false
            )
        );
        $fiddlerDebug = false;
        if ($fiddlerDebug) {
            $opts['request.options']['proxy'] = "tcp://127.0.0.1:8888";
            $opts['request.options']['verify'] = false;
        }
        $client = new FilterableClient('', $opts);

        $enableLogging = getenv($this::ENABLE_LOG4PHP_LOGGING_ENV_NAME);
        if ($enableLogging) {
            $client->addFilter(new Log4PhpLoggingFilter());
        }
        return $client;
    }

    /**
     * @param string $uri The URI to the person.
     * @param \Guzzle\Http\Client $client The client to use.
     * @param string $method The method.
     *
     * @return PersonState The person state.
     */
    public function newPersonState($uri, Client $client = null, $method = "GET")
    {
        if (!$client) {
            $client = new FilterableClient();
        }

        /** @var Request $request */
        $request = $client->createRequest($method, $uri);
        $request->setHeader("Accept", GedcomxApplicationState::JSON_MEDIA_TYPE);
        return new PersonState($client, $request, $client->send($request), null, $this);
    }

    /**
     * @param string $class The name of the state class to create
     * @param \Guzzle\Http\Client $client
     * @param \Guzzle\Http\Message\Request $request
     * @param \Guzzle\Http\Message\Response $response
     * @param string $accessToken The access token for this session
     *
     * @return mixed
     */
    public function createState($class, Client $client, Request $request, Response $response, $accessToken)
    {
        $functionName = "build{$class}";
        return $this->$functionName($client, $request, $response, $accessToken);
    }

    /**
     * @param \Guzzle\Http\Client $client
     * @param \Guzzle\Http\Message\Request $request
     * @param \Guzzle\Http\Message\Response $response
     * @param string $accessToken The access token for this session
     *
     * @return \Gedcomx\Rs\Client\SourceDescriptionsState
     */
    protected function buildCollectionState(Client $client, Request $request, Response $response, $accessToken)
    {
        return new CollectionState($client, $request, $response, $accessToken, $this);
    }

    /**
     * @param \Guzzle\Http\Client $client
     * @param \Guzzle\Http\Message\Request $request
     * @param \Guzzle\Http\Message\Response $response
     * @param string $accessToken The access token for this session
     *
     * @return \Gedcomx\Rs\Client\SourceDescriptionsState
     */
    protected function buildCollectionsState( Client $client, Request $request, Response $response, $accessToken ){
        return new CollectionsState( $client, $request, $response, $accessToken, $this );
    }

    /**
     * @param \Guzzle\Http\Client           $client
     * @param \Guzzle\Http\Message\Request  $request
     * @param \Guzzle\Http\Message\Response $response
     * @param string                        $accessToken The access token for this session
     *
     * @return \Gedcomx\Rs\Client\SourceDescriptionsState
     */
    protected function buildSourceDescriptionsState(Client $client, Request $request, Response $response, $accessToken)
    {
        return new SourceDescriptionsState($client, $request, $response, $accessToken, $this);
    }

    /**
     * @param \Guzzle\Http\Client $client
     * @param \Guzzle\Http\Message\Request $request
     * @param \Guzzle\Http\Message\Response $response
     * @param string $accessToken The access token for this session
     *
     * @return \Gedcomx\Rs\Client\SourceDescriptionsState
     */
    protected function buildSourceDescriptionState(Client $client, Request $request, Response $response, $accessToken)
    {
        return new SourceDescriptionState($client, $request, $response, $accessToken, $this);
    }

    /**
     * @param \Guzzle\Http\Client $client
     * @param \Guzzle\Http\Message\Request $request
     * @param \Guzzle\Http\Message\Response $response
     * @param string $accessToken The access token for this session
     *
     * @return \Gedcomx\Rs\Client\PersonsState
     */
    protected function buildPersonsState(Client $client, Request $request, Response $response, $accessToken)
    {
        return new PersonsState($client, $request, $response, $accessToken, $this);
    }

    /**
     * @param \Guzzle\Http\Client $client
     * @param \Guzzle\Http\Message\Request $request
     * @param \Guzzle\Http\Message\Response $response
     * @param string $accessToken The access token for this session
     *
     * @return \Gedcomx\Rs\Client\PersonChildrenState
     */
    protected function buildPersonChildrenState(Client $client, Request $request, Response $response, $accessToken)
    {
        return new PersonChildrenState($client, $request, $response, $accessToken, $this);
    }

    /**
     * @param \Guzzle\Http\Client $client
     * @param \Guzzle\Http\Message\Request $request
     * @param \Guzzle\Http\Message\Response $response
     * @param string $accessToken The access token for this session
     *
     * @return \Gedcomx\Rs\Client\PersonState
     */
    protected function buildPersonState(Client $client, Request $request, Response $response, $accessToken)
    {
        return new PersonState($client, $request, $response, $accessToken, $this);
    }

    /**
     * @param \Guzzle\Http\Client $client
     * @param \Guzzle\Http\Message\Request $request
     * @param \Guzzle\Http\Message\Response $response
     * @param string $accessToken The access token for this session
     *
     * @return \Gedcomx\Rs\Client\PersonParentsState
     */
    protected function buildPersonParentsState(Client $client, Request $request, Response $response, $accessToken)
    {
        return new PersonParentsState($client, $request, $response, $accessToken, $this);
    }

    /**
     * @param \Guzzle\Http\Client $client
     * @param \Guzzle\Http\Message\Request $request
     * @param \Guzzle\Http\Message\Response $response
     * @param string $accessToken The access token for this session
     *
     * @return \Gedcomx\Rs\Client\PersonSpousesState
     */
    protected function buildPersonSpousesState(Client $client, Request $request, Response $response, $accessToken)
    {
        return new PersonSpousesState($client, $request, $response, $accessToken, $this);
    }

    /**
     * @param \Guzzle\Http\Client $client
     * @param \Guzzle\Http\Message\Request $request
     * @param \Guzzle\Http\Message\Response $response
     * @param string $accessToken The access token for this session
     *
     * @return \Gedcomx\Rs\Client\AncestryResultsState
     */
    protected function buildAncestryResultsState(Client $client, Request $request, Response $response, $accessToken)
    {
        return new AncestryResultsState($client, $request, $response, $accessToken, $this);
    }

    /**
     * @param \Guzzle\Http\Client $client
     * @param \Guzzle\Http\Message\Request $request
     * @param \Guzzle\Http\Message\Response $response
     * @param string $accessToken The access token for this session
     *
     * @return \Gedcomx\Rs\Client\AncestryResultsState
     */
    protected function buildDescendancyResultsState(Client $client, Request $request, Response $response, $accessToken)
    {
        return new DescendancyResultsState($client, $request, $response, $accessToken, $this);
    }

    /**
     * @param \Guzzle\Http\Client $client
     * @param \Guzzle\Http\Message\Request $request
     * @param \Guzzle\Http\Message\Response $response
     * @param string $accessToken The access token for this session
     *
     * @return \Gedcomx\Rs\Client\PersonSearchResultsState
     */
    protected function buildPersonSearchResultsState(Client $client, Request $request, Response $response, $accessToken)
    {
        return new PersonSearchResultsState($client, $request, $response, $accessToken, $this);
    }

    /**
     * @param \Guzzle\Http\Client $client
     * @param \Guzzle\Http\Message\Request $request
     * @param \Guzzle\Http\Message\Response $response
     * @param string $accessToken The access token for this session
     *
     * @return \Gedcomx\Rs\Client\RecordState
     */
    protected function buildRecordState(Client $client, Request $request, Response $response, $accessToken)
    {
        return new RecordState($client, $request, $response, $accessToken, $this);
    }

    /**
     * @param \Guzzle\Http\Client $client
     * @param \Guzzle\Http\Message\Request $request
     * @param \Guzzle\Http\Message\Response $response
     * @param string $accessToken The access token for this session
     *
     * @return \Gedcomx\Rs\Client\RecordState
     */
    protected function buildRelationshipState(Client $client, Request $request, Response $response, $accessToken)
    {
        return new RelationshipState($client, $request, $response, $accessToken, $this);
    }

    /**
     * @param \Guzzle\Http\Client $client
     * @param \Guzzle\Http\Message\Request $request
     * @param \Guzzle\Http\Message\Response $response
     * @param string $accessToken The access token for this session
     *
     * @return \Gedcomx\Rs\Client\RecordState
     */
    protected function buildAgentState(Client $client, Request $request, Response $response, $accessToken)
    {
        return new AgentState($client, $request, $response, $accessToken, $this);
    }

    /**
     * @param \Guzzle\Http\Client $client
     * @param \Guzzle\Http\Message\Request $request
     * @param \Guzzle\Http\Message\Response $response
     * @param string $accessToken The access token for this session
     *
     * @return \Gedcomx\Rs\Client\RecordState
     */
    protected function buildPlaceSearchResultState(Client $client, Request $request, Response $response, $accessToken)
    {
        return new PlaceSearchResultState($client, $request, $response, $accessToken, $this);
    }

    /**
     * @param \Guzzle\Http\Client $client
     * @param \Guzzle\Http\Message\Request $request
     * @param \Guzzle\Http\Message\Response $response
     * @param string $accessToken The access token for this session
     *
     * @return \Gedcomx\Rs\Client\PlaceDescriptionState
     */
    protected function buildPlaceDescriptionState(Client $client, Request $request, Response $response, $accessToken)
    {
        return new PlaceDescriptionState($client, $request, $response, $accessToken, $this);
    }

    /**
     * @param \Guzzle\Http\Client $client
     * @param \Guzzle\Http\Message\Request $request
     * @param \Guzzle\Http\Message\Response $response
     * @param string $accessToken The access token for this session
     *
     * @return \Gedcomx\Rs\Client\PlaceDescriptionState
     */
    protected function buildVocabElementListState(Client $client, Request $request, Response $response, $accessToken)
    {
        return new VocabElementListState($client, $request, $response, $accessToken, $this);
    }

    /**
     * @param \Guzzle\Http\Client $client
     * @param \Guzzle\Http\Message\Request $request
     * @param \Guzzle\Http\Message\Response $response
     * @param string $accessToken The access token for this session
     *
     * @return \Gedcomx\Rs\Client\PlaceDescriptionState
     */
    protected function buildVocabElementState(Client $client, Request $request, Response $response, $accessToken)
    {
        return new VocabElementState($client, $request, $response, $accessToken, $this);
    }
}
