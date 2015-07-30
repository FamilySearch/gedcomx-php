<?php

namespace Gedcomx\Rs\Client;

use Gedcomx\Common\Attributable;
use Gedcomx\Common\ResourceReference;
use Gedcomx\Gedcomx;
use Gedcomx\Links\Link;
use Gedcomx\Rs\Client\Exception\GedcomxApplicationException;
use Gedcomx\Rs\Client\Options\HeaderParameter;
use Gedcomx\Rs\Client\Options\StateTransitionOption;
use Gedcomx\Rs\Client\Util\EmbeddedLinkLoader;
use Gedcomx\Rs\Client\Util\HttpStatus;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * This is the base class for all state instances.
 *
 * Class GedcomxApplicationState
 *
 * @package Gedcomx\Rs\Client
 */
abstract class GedcomxApplicationState
{
    /**
     * The REST API client to use with all API calls.
     *
     * @var Client
     */
    protected $client;
    /**
     * Gets or sets the REST API request.
     *
     * @var Request
     */
    protected $request;
    /**
     * Gets or sets the REST API response.
     *
     * @var Response
     */
    protected $response;
    /**
     * Gets or sets the current access token (the OAuth2 token), see {@link https://familysearch.org/developers/docs/api/authentication/Access_Token_resource}.
     *
     * @var string
     */
    protected $accessToken;
    /**
     * The factory responsible for creating new state instances from REST API response data.
     *
     * @var StateFactory
     */
    protected $stateFactory;
    /**
     * The list of hypermedia links. Links are not specified by GEDCOM X core, but as extension elements by GEDCOM X RS.
     *
     * @var array
     */
    protected $links;
    /**
     * Gets the entity represented by this state (if applicable). Not all responses produce entities.
     *
     * @var object
     */
    protected $entity;
    /**
     * The last embedded request (from a previous call to GedcomxApplicationState embed()).
     *
     * @var \GuzzleHttp\Psr7\Request
     */
    private $lastEmbeddedRequest;
    /**
     * Gets or sets the last embedded response (from a previous call to GedcomxApplicationState embed()).
     *
     * @var \GuzzleHttp\Psr7\Response
     */
    private $lastEmbeddedResponse;

    /**
     * Constructs a new GedcomxApplicationState using the specified client, request, response, access token, and state factory.
     * @param Client       $client
     * @param Request      $request
     * @param Response     $response
     * @param string       $accessToken
     * @param StateFactory $stateFactory
     */
    function __construct(Client $client, Request $request, Response $response, $accessToken, StateFactory $stateFactory)
    {
        $this->client = $client;
        $this->request = $request;
        $this->response = $response;
        $this->accessToken = $accessToken;
        $this->stateFactory = $stateFactory;
        $this->entity = $this->loadEntityConditionally();
        $this->links = $this->loadLinks();
    }

    /**
     * Loads the entity from the REST API response if the response should have data.
     *
     * @return null
     */
    protected function loadEntityConditionally()
    {
        if (   ($this->request->getMethod() != 'HEAD' && $this->request->getMethod() != 'OPTIONS')
            && ($this->response->getStatusCode() == HttpStatus::OK || $this->response->getStatusCode() == HttpStatus::GONE)
            || $this->response->getStatusCode() == HttpStatus::PRECONDITION_FAILED
        ) {
            return $this->loadEntity();
        }
        else {
            return null;
        }
    }

    /**
     * Clonse the current state instance.
     *
     * @param \GuzzleHttp\Psr7\Request  $request
     * @param \GuzzleHttp\Psr7\Response $response
     *
     * @return mixed
     */
    protected abstract function reconstruct(Request $request, Response $response);

    /**
     * Returns the entity from the REST API response.
     *
     * @return mixed
     */
    protected abstract function loadEntity();

    /**
     * Gets the main data element represented by this state instance.
     *
     * @return mixed
     */
    protected abstract function getScope();

    /**
     * Invokes the specified REST API request and returns a state instance of the REST API response.
     *
     * @param \GuzzleHttp\Psr7\Request $request
     *
     * @return mixed
     */
    public function inject(Request $request)
    {
        return $this->reconstruct($request, $this->passOptionsTo('invoke',array($request)));
    }

    /**
     * Loads all links from a REST API response and entity object, whether from the header, response body, or any other properties available to extract useful links for this state instance.
     *
     * @return array
     */
    protected function loadLinks()
    {
        $links = array();

        //if there's a location, we'll consider it a "self" link.
        $myLocation = $this->response->getHeader('Location');
        if (isset($myLocation)) {
            $links['self'] = new Link();
            $links['self']->setRel('self');
            $links['self']->setHref($myLocation[0]);
        }

        //load link headers
        $linkHeaders = \GuzzleHttp\Psr7\parse_header($this->response->getHeader('Link'));
        foreach ($linkHeaders as $linkHeader) {
            $linkHeader['href'] = trim($linkHeader[0], '<>');
            if (isset($linkHeader['rel'])) {
                $link = new Link($linkHeader);
                $links[$linkHeader['rel']] = $link;
            }
        }

        //load links from the entity.
        if (isset($this->entity) && $this->entity->getLinks() != null) {
            $links = array_merge($links, $this->entity->getLinks());
        }

        $scope = $this->getScope();
        if (isset($scope) && is_object($scope)) {
            $links = array_merge($links, $scope->getLinks());
        }

        return $links;
    }

    /**
     * Gets sets the main REST API client to use with all API calls.
     *
     * @return \GuzzleHttp\Client
     */
    public function getClient()
    {
        return $this->client;
    }

    /**
     * Gets the current access token (the OAuth2 token), see https://familysearch.org/developers/docs/api/authentication/Access_Token_resource.
     *
     * @return string
     */
    public function getAccessToken()
    {
        return $this->accessToken;
    }

    /**
     * Gets the REST API request.
     *
     * @return \GuzzleHttp\Psr7\Request
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * Gets the REST API response.
     *
     * @return \GuzzleHttp\Psr7\Response
     */
    public function getResponse()
    {
        return $this->response;
    }

    /**
     * Gets the last embedded request (from a previous call to GedcomxApplicationState embed()).
     *
     * @return \GuzzleHttp\Psr7\Request
     */
    public function getLastEmbeddedRequest()
    {
        return $this->lastEmbeddedRequest;
    }

    /**
     * Gets the last embedded response (from a previous call to GedcomxApplicationState embed()).
     *
     * @return \GuzzleHttp\Psr7\Response
     */
    public function getLastEmbeddedResponse()
    {
        return $this->lastEmbeddedResponse;
    }

    /**
     * Gets the entity represented by this state (if applicable). Not all responses produce entities.
     *
     * @return object
     */
    public function getEntity()
    {
        return $this->entity;
    }

    /**
     * Gets a value indicating whether this instance is authenticated.
     *
     * @return boolean whether this state is authenticated.
     */
    public function isAuthenticated()
    {
        return isset($this->accessToken);
    }

    /**
     * Gets the URI of the REST API request associated to this state instance.
     *
     * @return string The URI for this application state.
     */
    public function getUri()
    {
        return $this->request->getUri();
    }

    /**
     * Determines whether the server response status code indicates a client side error (status code >= 400 and < 500).
     *
     * @return bool Whether this state is a client-side error.
     */
    public function hasClientError()
    {
        $statusCode = intval($this->getStatus());
        return $statusCode >= 400 && $statusCode < 500;
    }

    /**
     * Determines whether the server response status code indicates a server side error (status code >= 500 and < 600).
     *
     * @return bool Whether this state is a server-side error.
     */
    public function hasServerError()
    {
        $statusCode = intval($this->getStatus());
        return $statusCode >= 500 && $statusCode < 600;
    }

    /**
     * Determines whether this instance has error (server [code >= 500 and < 600] or client [code >= 400 and < 500]).
     *
     * @return bool Whether this state has an error.
     */
    public function hasError()
    {
        return $this->hasClientError() || $this->hasServerError();
    }

    /**
     * Determines whether the current REST API response has the specified status.
     *
     * @param int $status
     *
     * @return bool
     */
    public function hasStatus($status)
    {
        return $status == $this->getStatus() ? true : false;
    }

    /**
     * Get the HTTP status code of the response.
     *
     * @return int
     */
    public function getStatus()
    {
        return intval($this->getResponse()->getStatusCode());
    }

    /**
     * Gets the collection of REST API response headers.
     *
     * @return array The headers for this state.
     */
    public function getHeaders()
    {
        return $this->response->getHeaders();
    }

    /**
     * Gets the REST API response header (by name) if present.
     *
     * @param string $name The name of the header to retrieve.
     *
     * @return array return a specific header for this state.
     */
    public function getHeader($name)
    {
        return $this->response->getHeader($name);
    }

    /**
     * Gets the URI representing this current state instance.
     *
     * @return string The self-URI for this state.
     */
    public function getSelfUri()
    {
        $selfRel = $this->getSelfRel();
        $link = null;
        if ($selfRel != null) {
            $link = $this->getLink($selfRel);
        }
        if ($link == null){
            $link = $this->getLink(Rel::SELF);
        }
        $self = null;
        if ($link != null){
            if ($link->getHref() != null){
                $self = $link->getHref();
            }
        }
        if ($self == null){
            return $this->getUri();
        }else{
            return $self;
        }
    }

    /**
     * Gets the rel name for the current state instance. This is expected to be overridden.
     *
     * @return null
     */
    public function getSelfRel(){
        return null;
    }

    /**
     * Gets the entity tag of the entity represented by this instance.
     *
     * @return array
     */
    public function getETag() {
        return $this->response->getHeader(HeaderParameter::ETAG);
    }

    /**
     * Gets the last modified date of the entity represented by this instance.
     *
     * @return array
     */
    public function getLastModified() {
        return $this->response->getHeader(HeaderParameter::LAST_MODIFIED);
    }

    /**
     * Gets the resource reference represented by this instance.
     *
     * @return ResourceReference
     */
    public function getResourceReference(){
        $args = array(
            'resource' => $this->getSelfUri()
        );
        if ($this->getScope() != null) {
           $args['resourceId'] = $this->getScope()->getId();
        }

        return new ResourceReference($args);
    }

    /**
     * Executes a HEAD verb request against the current REST API request and returns a state instance with the response.
     *
     * @param \Gedcomx\Rs\Client\Options\StateTransitionOption $option,...
     *
     * @return \Gedcomx\Rs\Client\GedcomxApplicationState
     */
    public function head(StateTransitionOption $option = null)
    {
        $headers = [];
        $accept = $this->request->getHeader("Accept");
        if (isset($accept)) {
            $headers["Accept"] = $accept;
        }
        $request = $this->createAuthenticatedRequest('HEAD', $this->getSelfUri(), $headers);
        return $this->reconstruct($request, $this->passOptionsTo('invoke',array($request),func_get_args()));
    }

    /**
     * Executes a'GET' verb request against the current REST API request and returns a state instance with the response.
     *
     * @param \Gedcomx\Rs\Client\Options\StateTransitionOption $option,...
     *
     * @return mixed
     */
    public function get(StateTransitionOption $option = null)
    {
        $headers = [];
        $accept = $this->request->getHeader("Accept");
        if (isset($accept)) {
            $headers["Accept"] = $accept;
        }
        $request = $this->createAuthenticatedRequest('GET', $this->getSelfUri(), $headers);
        return $this->reconstruct($request, $this->passOptionsTo('invoke',array($request),func_get_args()));
    }

    /**
     * Executes an DELETE verb request against the current REST API request and returns a state instance with the response.
     *
     * @param \Gedcomx\Rs\Client\Options\StateTransitionOption $option,...
     *
     * @return \Gedcomx\Rs\Client\GedcomxApplicationState
     */
    public function delete(StateTransitionOption $option = null)
    {
        $headers = [];
        $accept = $this->request->getHeader("Accept");
        if (isset($accept)) {
            $headers["Accept"] = $accept;
        }
        $request = $this->createAuthenticatedRequest('DELETE', $this->getSelfUri(), $headers);
        return $this->reconstruct($request, $this->passOptionsTo('invoke',array($request),func_get_args()));
    }

    /**
     * Executes an OPTIONS verb request against the current REST API request and returns a state instance with the response.
     *
     * @param \Gedcomx\Rs\Client\Options\StateTransitionOption $option,...
     *
     * @return \Gedcomx\Rs\Client\GedcomxApplicationState
     */
    public function options(StateTransitionOption $option = null)
    {
        $headers = [];
        $accept = $this->request->getHeader("Accept");
        if (isset($accept)) {
            $headers["Accept"] = $accept;
        }
        $request = $this->createAuthenticatedRequest('OPTIONS', $this->getSelfUri(), $headers);
        return $this->reconstruct($request, $this->passOptionsTo('invoke',array($request),func_get_args()));
    }

    /**
     * Executes a PUT verb request against the current REST API request and returns a state instance with the response.
     *
     * @param                                                  $entity
     * @param \Gedcomx\Rs\Client\Options\StateTransitionOption $option,...
     *
     * @return \Gedcomx\Rs\Client\GedcomxApplicationState
     * @throws GedcomxApplicationException
     */
    public function put($entity, StateTransitionOption $option = null)
    {
        $headers = [];
        $accept = $this->request->getHeader("Accept");
        if (isset($accept)) {
            $headers["Accept"] = $accept;
        }
        $contentType = $this->request->getHeader("Content-Type");
        if (isset($contentType)) {
            $headers["Content-Type"] = $contentType;
        }
        $request = $this->createAuthenticatedRequest('PUT', $this->getSelfUri(), $headers, null, $entity->toJson());
        return $this->reconstruct($request, $this->passOptionsTo('invoke',array($request),func_get_args()));
    }

    /**
     * Executes a POST verb request against the current REST API request and returns a state instance with the response.
     *
     * @param \Gedcomx\Gedcomx                                 $entity
     * @param \Gedcomx\Rs\Client\Options\StateTransitionOption $option,...
     *
     * @return mixed
     */
    public function post(Gedcomx $entity, StateTransitionOption $option = null)
    {
        $headers = [];
        $accept = $this->request->getHeader("Accept");
        if (isset($accept)) {
            $headers["Accept"] = $accept;
        }
        $contentType = $this->request->getHeader("Content-Type");
        if (isset($contentType)) {
            $headers["Content-Type"] = $contentType;
        }
        if ($entity instanceof Gedcomx && !isset($headers["Content-Type"])){
            $headers["Content-Type"] = Gedcomx::JSON_MEDIA_TYPE;
        }
        $request = $this->createAuthenticatedRequest('POST', $this->getSelfUri(), $headers, null, $entity->toJson());
        return $this->reconstruct($request, $this->passOptionsTo('invoke', array($request), func_get_args()));
    }

    /**
     * Get a link by its rel. Links are not specified by GEDCOM X core, but as extension elements by GEDCOM X RS.
     *
     * @param string $rel The link rel.
     *
     * @return \Gedcomx\Links\Link
     */
    public function getLink($rel)
    {
		if( isset($this->links[$rel]) ){
			return $this->links[$rel];
		}

        return null;
    }

    /**
     * Gets the list of hypermedia links. Links are not specified by GEDCOM X core, but as extension elements by GEDCOM X RS.
     *
     * @return array
     */
    public function getLinks()
    {
        return $this->links == null ? array() : $this->links;
    }

    /**
     * Returns the current state instance if there are no errors in the current REST API response; otherwise, it throws an exception with the response details.
     *
     * @throws GedcomxApplicationException If this state captures an error.
     * @return GedcomxApplicationState $this
     */
    public function ifSuccessful()
    {
        if ($this->hasError()) {
            throw new GedcomxApplicationException($this->buildFailureMessage($this->request, $this->response), $this->response);
        }

        return $this;
    }

    /**
     * Sets the current access token to the one specified. The server is not contacted during this operation.
     *
     * @param $accessToken
     *
     * @return $this
     */
    public function authenticateWithAccessToken($accessToken) {
        $this->accessToken = $accessToken;
        return $this;
    }

    /**
     * Authenticates this session via OAuth2.
     *
     * @param array $formData The form parameters.
     *
     * @return \Gedcomx\Rs\Client\GedcomxApplicationState $this
     * @throws GedcomxApplicationException If there are problems.
     */
    protected function authenticateViaOAuth2(array $formData)
    {
        $tokenLink = $this->getLink(Rel::OAUTH2_TOKEN);
        if (!isset($tokenLink)) {
            $here = $this->getUri();
            throw new GedcomxApplicationException("No OAuth2 token URI supplied for resource at {$here}");
        }

        $href = $tokenLink->getHref();
        if (!isset($href)) {
            $here = $this->getUri();
            throw new GedcomxApplicationException("No OAuth2 token URI supplied for resource at {$here}");
        }

        $request = $this->createRequest('POST', $href, ['Accept' => 'application/json'], $formData);
        $response = $this->invoke($request);
        
        $statusCode = intval($response->getStatusCode());
        if ($statusCode >= 200 && $statusCode < 300) {
            $tokens = json_decode($response->getBody(), true);
            $accessToken = $tokens['access_token'];

            if (!isset($accessToken)) {
                //workaround to accommodate providers that were built on an older version of the oauth2 specification.
                $accessToken = $tokens['token'];
            }

            if (!isset($accessToken)) {
                throw new GedcomxApplicationException('Illegal access token response: no access_token provided.', $response);
            }

            $this->accessToken = $accessToken;
            return $this;
        }
        else {
            throw new GedcomxApplicationException('Unable to obtain an access token.', $response);
        }
    }

    /**
     * Authenticates this session via OAuth2 password.
     *
     * @param string $username     The username.
     * @param string $password     The password.
     * @param string $clientId     The client id.
     * @param string $clientSecret The client secret.
     * @return GedcomxApplicationState $this
     */
    public function authenticateViaOAuth2Password($username, $password, $clientId, $clientSecret = NULL)
    {
        $formData = array(
            'grant_type' => 'password',
            'username' => $username,
            'password' => $password,
            'client_id' => $clientId
        );

        if (isset($clientSecret)) {
            $formData['client_secret'] = $clientSecret;
        }

        return $this->authenticateViaOAuth2($formData);
    }

    /**
     * Authenticates this session via OAuth2 authentication code.
     *
     * @param string $authCode The auth code.
     * @param string $redirect The redirect URI.
     * @param string $clientId The client id.
     * @param string $clientSecret The client secret.
     * @return GedcomxApplicationState $this
     */
    public function authenticateViaOAuth2AuthCode($authCode, $redirect, $clientId, $clientSecret = NULL)
    {
        $formData = array(
            'grant_type' => 'authorization_code',
            'code' => $authCode,
            'redirect_uri' => $redirect,
            'client_id' => $clientId
        );

        if (isset($clientSecret)) {
            $formData['client_secret'] = $clientSecret;
        }

        return $this->authenticateViaOAuth2($formData);
    }

    /**
     * Authenticates this session via OAuth2 client credentials.
     *
     * @param string $clientId The client id.
     * @param string $clientSecret The client secret.
     * @return GedcomxApplicationState $this
     */
    public function authenticateViaOAuth2ClientCredentials($clientId, $clientSecret)
    {
        $formData = array(
            'grant_type' => 'client_credentials',
            'client_id' => $clientId,
            'client_secret' => $clientSecret
        );

        return $this->authenticateViaOAuth2($formData);
    }

    /**
     * Creates a state instance without authentication. It will produce an access token, but only good for requests that do not need authentication.
     *
     * @param string $clientId The client id.
     * @param string $ipAddress The client's ipaddress.
     * @return GedcomxApplicationState $this
     */
    public function authenticateViaOAuth2WithoutCredentials($ipAddress, $clientId)
    {
        $formData = array(
            'grant_type' => 'unauthenticated_session',
            'ip_address' => $ipAddress,
            'client_id' => $clientId
        );

        return $this->authenticateViaOAuth2($formData);
    }


    /**
     * Gets the warning headers from the current REST API response.
     *
     * @param array $headers optional: if not present current state object's headers
     *                       will be used.
     * @return string[] warning messages if Warning Headers are found
     */
    public function getWarnings( $headers = null )
    {
        if( $headers === null ){
            $headers = $this->response->getHeaders();
        }

        $warnings = array();
        foreach( $headers as $h ){
            if( $h->getName() == "Warning" ){
                $warnings = $h->toArray();
            }
        }

        return $warnings;
    }

    /**
     * Builds a pretty failure message from the specified response's warning headers, using the specified request for
     * additional information.
     * @param \GuzzleHttp\Psr7\Request   $request   HTTP request object
     * @param \GuzzleHttp\Psr7\Response  $response  HTTP response object
     * @return string
     */
    protected function buildFailureMessage( Request $request, Response $response ) {
        $message = "Unsuccessful " . $request->getMethod() . " to " . $request->getUrl() . " (" . $response->getStatusCode() . ")";
        $warnings = $this->getWarnings($response->getHeaders());
        foreach( $warnings as $w ) {
            $message .= "\nWarning: " . $w;
        }

        return $message;
    }


    /**
     * Reads a page of results, usually of type \Gedcomx\Atom\Feed.
     *
     * @param string                                           $rel        The rel
     * @param \Gedcomx\Rs\Client\Options\StateTransitionOption $option,... zero or more StateTransitionOption objects
     *
     * @return \Gedcomx\Rs\Client\GedcomxApplicationState The requested page.
     */
    protected function readPage($rel, StateTransitionOption $option = null)
    {
        $link = $this->getLink($rel);
        if ($link === null || $link->getHref() === null) {
            return null;
        }
        
        $headers = [
            'Accept' => $this->request->getHeader("Accept"),
            'Content-Type' => $this->request->getHeader("Content-Type")
        ];
        $request = $this->createAuthenticatedRequest($this->request->getMethod(), $link->getHref());
        $class = get_class($this);
        return new $class(
            $this->client,
            $request,
            $this->passOptionsTo('invoke',array($request), func_get_args()),
            $this->accessToken,
            $this->stateFactory
        );
    }

    /**
     * Reads the next page of results, usually of type \Gedcomx\Atom\Feed.
     *
     * @param \Gedcomx\Rs\Client\Options\StateTransitionOption $option,... zero or more StateTransitionOption objects
     *
     * @return \Gedcomx\Rs\Client\GedcomxApplicationState The requested page.
     */
    protected function readNextPage( StateTransitionOption $option = null )
    {
        return $this->passOptionsTo('readPage',array(Rel::NEXT), func_get_args());
    }

    /**
     * Reads the previous page of results, usually of type \Gedcomx\Atom\Feed.
     *
     * @param \Gedcomx\Rs\Client\Options\StateTransitionOption $option,... zero or more StateTransitionOption objects
     *
     * @return \Gedcomx\Rs\Client\GedcomxApplicationState The requested page.
     */
    protected function readPreviousPage(StateTransitionOption $option = null)
    {
        return $this->passOptionsTo('readPage',array(Rel::PREVIOUS), func_get_args());
    }

    /**
     * Reads the first page of results, usually of type \Gedcomx\Atom\Feed.
     *
     * @param \Gedcomx\Rs\Client\Options\StateTransitionOption $option,... zero or more StateTransitionOption objects
     *
     * @return \Gedcomx\Rs\Client\GedcomxApplicationState The requested page.
     */
    protected function readFirstPage(StateTransitionOption $option = null)
    {
        return $this->passOptionsTo('readPage',array(Rel::FIRST), func_get_args());
    }

    /**
     * Reads the last page of results, usually of type \Gedcomx\Atom\Feed.
     *
     * @param \Gedcomx\Rs\Client\Options\StateTransitionOption $option,... zero or more StateTransitionOption objects
     *
     * @return \Gedcomx\Rs\Client\GedcomxApplicationState The requested page.
     */
    protected function readLastPage(StateTransitionOption $option = null)
    {
        return $this->passOptionsTo('readPage',array(Rel::LAST), func_get_args());
    }

    /**
     * @param string $method  The HTTP method.
     * @param string|array $uri optional: string with an href, or an array with template info
     * @param array $headers optional: Associative array of HTTP headers
     * @param array $formData optional: Array of form data
     * @param string $body optional: body of the request
     *
     * @return Request The request.
     */
    protected function createRequest($method, $uri = null, $headers = array(), $formData = null, $body = null)
    {
        if(is_array($formData)){
            $body = http_build_query($formData, null, '&');
            $headers['Content-Type'] = 'application/x-www-form-urlencoded';
        }
        if(is_array($uri)){
            $uri = \GuzzleHttp\uri_template($uri[0], $uri[1]);
        }
        return new Request($method, $uri, $headers, $body);
    }

    /**
     * Creates a request object (with authorization when present) for use with REST API requests.
     *
     * @param string $method  The HTTP method.
     * @param string|array $uri optional: string with an href, or an array with template info
     * @param array $headers optional: Associative array of HTTP headers
     * @param array $formData optional: Array of form data
     * @param string $body optional: body of the request
     *
     * @return Request The request.
     */
    protected function createAuthenticatedRequest($method, $uri = null, $headers = array(), $formData = null, $body = null)
    {
        if (isset($this->accessToken)) {
            $headers['Authorization'] = "Bearer {$this->accessToken}";
        }
        $request = $this->createRequest($method, $uri, $headers, $formData, $body);
        return $request;
    }

    /**
     * Creates a request object that expects a "application/x-gedcomx-atom+json" response (with authorization when present) for use with REST API requests.
     *
     * @param string $method  The HTTP method.
     * @param string|array $uri optional: string with an href, or an array with template info
     * @param array $headers optional: Associative array of HTTP headers
     * @param array $formData optional: Array of form data
     * @param string $body optional: body of the request
     *
     * @return Request The request.
     */
    protected function createAuthenticatedFeedRequest($method, $uri = null, $headers = array(), $formData = null, $body = null)
    {
        $headers['Accept'] = Gedcomx::ATOM_JSON_MEDIA_TYPE;
        $request = $this->createAuthenticatedRequest($method, $uri, $headers, $formData, $body);
        return $request;
    }

    /**
     * Creates a request object that will send and expect "application/x-gedcomx-v1+json" data (with authorization when present) for use with REST API requests.
     *
     * @param string $method  The HTTP method.
     * @param string|array $uri optional: string with an href, or an array with template info
     * @param array $headers optional: Associative array of HTTP headers
     * @param array $formData optional: Array of form data
     * @param string $body optional: body of the request
     *
     * @return Request The request.
     */
    protected function createAuthenticatedGedcomxRequest($method, $uri = null, $headers = array(), $formData = null, $body = null)
    {
        if(!isset($headers['Accept'])){
            $headers['Accept'] = Gedcomx::JSON_MEDIA_TYPE;
        }
        if(!isset($headers['Content-Type'])){
            $headers['Content-Type'] = Gedcomx::JSON_MEDIA_TYPE;
        }
        $request = $this->createAuthenticatedRequest($method, $uri, $headers, $formData, $body);
        return $request;
    }

    /**
     * Creates a REST API request (with appropriate authentication headers).
     *
     * @param                     $method
     * @param \Gedcomx\Links\Link $link
     *
     * @return \GuzzleHttp\Psr7\Request
     */
    protected function createRequestForEmbeddedResource($method, Link $link) {
        return $this->createAuthenticatedGedcomxRequest($method, $link->getHref());
    }

    /**
     * Executes the specified link and embeds the response in the current instance entity.
     *
     * @param \Gedcomx\Links\Link                              $link
     * @param \Gedcomx\Rs\Client\Options\StateTransitionOption $option,...
     *
     * @throws Exception\GedcomxApplicationException
     */
    protected function embed(Link $link, StateTransitionOption $option = null ){
        if ($link->getHref() != null) {
            $this->lastEmbeddedRequest = $this->createRequestForEmbeddedResource('GET', $link);
            $this->lastEmbeddedResponse = $this->passOptionsTo('invoke',array($this->lastEmbeddedRequest), func_get_args());
            if ($this->lastEmbeddedResponse->getStatusCode() == 200) {
                $json = json_decode($this->lastEmbeddedResponse->getBody(), true);
                $entityClass = get_class($this->entity);
                $this->entity->embed(new $entityClass($json));
            }
            else if (floor($this->lastEmbeddedResponse->getStatusCode()/100) == 5 ) {
                throw new GedcomxApplicationException(sprintf("Unable to load embedded resources: server says \"%s\" at %s.", $this->lastEmbeddedResponse.getClientResponseStatus().getReasonPhrase(), $this->lastEmbeddedRequest.getURI()), $this->lastEmbeddedResponse);
            }
            else {
                //todo: log a warning? throw an error?
            }
        }

    }

    /**
     * Load all external resources such as notes, media, and evidence. See
     * EmbeddedLinkLoader for a complete list.
     *
     * @param \Gedcomx\Rs\Client\Options\StateTransitionOption $option,...
     *
     * @return \Gedcomx\Rs\Client\PersonState $this
     */
    public function loadAllEmbeddedResources(StateTransitionOption $option = null)
    {
        $loader = new EmbeddedLinkLoader();
        $links = $loader->loadEmbeddedLinks($this->entity);
        foreach ($links as $link) {
            $this->passOptionsTo('embed', array($link), func_get_args());
        }
        return $this;
    }

    /**
     * Extracts embedded links from the current instance entity, calls each one, and embeds the response into the current instance entity.
     *
     * @param \Gedcomx\Rs\Client\Options\StateTransitionOption $options
     */
    protected function includeEmbeddedResources(StateTransitionOption $options = null) {
        $this->passOptionsTo('loadAllEmbeddedResources', array(), func_get_args());
    }


    /**
     * @param array $args expects the results from func_get_args()
     *
     * @return array
     */
    private function findTransitionOptions( array $args )
    {
        while (! empty($args) && ! $args[0] instanceof StateTransitionOption){
            array_shift($args);
        }

        return $args;
    }

    /**
     * This function emulates optional parameters present in other languages.
     *
     * @param string $functionName The function to call. Assumed to be in $this scope
     * @param array  $args         New arguments to pass to the function
     * @param array  $passed_args  Possible optional arguments from the calling function
     * @param null   $scope
     *
     * @return mixed
     */
    protected function passOptionsTo( $functionName, array $args, array $passed_args = array(), $scope = null ){
        $func_args = array_merge($args, $this->findTransitionOptions($passed_args));
        if( $scope == null ){
            $func = 'static::' . $functionName;
        } elseif (is_string($scope)) {
            $func = $scope . "::" . $functionName;
        } else {
            $func = array($scope, $functionName);
        }
        return call_user_func_array(
            $func,
            $func_args
        );
    }

    /**
     * Reads the contributor for the current state instance.
     *
     * @param \Gedcomx\Rs\Client\Options\StateTransitionOption $option,...
     *
     * @return \Gedcomx\Rs\Client\AgentState|null
     */
    public function readContributor(StateTransitionOption $option = null)
    {
        $scope = $this->getScope();
        if ($scope instanceof Attributable) {
            return $this->passOptionsTo('readAttributableContributor', array($scope), func_get_args());
        } else {
            return null;
        }
    }

    /**
     * Reads the contributor for the specified attributable.
     *
     * @param \Gedcomx\Common\Attributable                     $attributable
     * @param \Gedcomx\Rs\Client\Options\StateTransitionOption $option,...
     *
     * @return \Gedcomx\Rs\Client\AgentState|null
     */
    public function readAttributableContributor(Attributable $attributable, StateTransitionOption $option = null)
    {
        $attribution = $attributable->getAttribution();
        if ($attribution == null) {
            return null;
        }

        $reference = $attribution->getContributor();
        return $this->passOptionsTo('readReferenceContributor', array($reference), func_get_args());
    }

    /**
     * Reads the contributor for the specified resource reference.
     *
     * @param \Gedcomx\Common\ResourceReference                $contributor
     * @param \Gedcomx\Rs\Client\Options\StateTransitionOption $option,...
     *
     * @return \Gedcomx\Rs\Client\AgentState|null
     */
    public function readReferenceContributor(ResourceReference $contributor, StateTransitionOption $option = null)
    {
        if ($contributor == null || $contributor->getResource() == null) {
            return null;
        }

        $request = $this->createAuthenticatedGedcomxRequest('GET', $contributor->getResource());
        return $this->stateFactory->createState(
            'AgentState',
            $this->client,
            $request,
            $this->passOptionsTo('invoke', array($request), func_get_args()),
            $this->accessToken
        );
    }

    /**
     * Applies the specified options before before executing the request.
     *
     * @param \GuzzleHttp\Psr7\Request $request the request to send.
     * @param \Gedcomx\Rs\Client\Options\StateTransitionOption $option,... StateTransitionOptions to be applied before sending
     *
     * @throws Exception\GedcomxApplicationException
     * @return Response The response.
     */
    protected function invoke(Request $request, StateTransitionOption $option = null)
    {
        $options = func_get_args();
        array_shift($options);
        if( $options !== null && !empty($options) ){
            foreach( $options as $opt ){
                $request = $opt->apply($request);
            }
        }
        $actualUri = (string) $request->getUri();
        $response = $this->client->send($request, [
            'curl' => ['body_as_string' => true],
            'allow_redirects' => [
                'on_redirect' => function(RequestInterface $request, ResponseInterface $response, $uri) use (&$actualUri) {
                    $actualUri = (string) $uri;
                }    
            ]   
        ]);
        $response->effectiveUri = $actualUri;
        return $response;
    }

}