<?php

namespace Gedcomx\Extensions\FamilySearch\Rs\Client;

use Gedcomx\Gedcomx;
use Gedcomx\Rs\Client\Rel as GedcomxRel;
use Gedcomx\Rs\Client\GedcomxApplicationState;
use Gedcomx\Rs\Client\Exception\GedcomxApplicationException;
use Gedcomx\Extensions\FamilySearch\FamilySearchPlatform;
use Gedcomx\Extensions\FamilySearch\Rs\Client\FamilyTree\FamilyTreeStateFactory;
use Gedcomx\Extensions\FamilySearch\Rs\Client\Util\LoggerMiddleware;
use GuzzleHttp\Client;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Handler\CurlHandler;
use GuzzleHttp\Middleware;
use GuzzleHttp\Psr7\Request;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerInterface;

/**
 * API Client for the FamilySearch API
 *
 * Class Client
 *
 * @package Gedcomx\Extensions\FamilySearch\Rs\Client
 */
class FamilySearchClient implements LoggerAwareInterface{
    
    /**
     * Guzzle client object
     * 
     * @var \GuzzleHttp\Client;
     */
    private $client;
    
    /**
     * The redirect URI used during authentication via OAuth2
     *
     * @var string
     */
    private $redirectURI;
    
    /**
     * The client ID used for authentication via OAuth2
     * 
     * @var string
     */
    private $clientId;
    
    /**
     * The client secret used for authentication via OAuth2
     * 
     * @var string
     */
    private $clientSecret;
    
    /**
     * URI for the Home Collection resource.
     * 
     * @var string
     */
    private $homeURI;
    
    /**
     * @var \Gedcomx\Extensions\FamilySearch\Rs\Client\FamilyTree\FamilyTreeStateFactory
     */
    private $stateFactory;
    
    /**
     * @var \Gedcomx\Rs\Client\CollectionState
     */
    private $treeState;
    
    /**
     * @var \Gedcomx\Rs\Client\CollectionState
     */
    private $homeState;
    
    /**
     * @var \GuzzleHttp\HandlerStack
     */
    private $stack;
    
    /**
     * Construct a FamilySearch Client.
     *
     * @param array $options A keyed array of configuration options for the client. Available options:
     * 
     * * `clientId` - Required for authentication.
     * * `redirectURI` - Required for authentication.
     * * `accessToken` - If the access token is set then the `clientId` and `redirectURI` are not needed.
     * * `environment` - `production`, `beta`, or `sandbox`; defaults to `sandbox`.
     * * `userAgent` - A string which will be prepended to the default user agent string.
     * * `pendingModifications` - An array of pending modifications that should be enabled.
     * * `logger` - A `Psr\Log\LoggerInterface`. A logger can also be registered via the `setLogger()` method but passing it in as an option during instantiation ensures that the logger will see all client events.
     * * `middleware` - An array of [Guzzle Middleware](http://docs.guzzlephp.org/en/latest/handlers-and-middleware.html#middleware).
     */
    public function __construct($options = array())
    {
        if(isset($options['redirectURI'])){
            $this->redirectURI = $options['redirectURI'];
        }
        if(isset($options['clientId'])){
            $this->clientId = $options['clientId'];
        }
        
        // Set the proper collectionsURI based on the environment.
        // Default to sandbox.
        $environment = '';
        if(isset($options['environment'])){
            $environment = $options['environment'];
        }
        switch($environment){
            case 'production':
                $this->homeURI = 'https://familysearch.org/platform/collection';
                break;
            case 'beta':
                $this->homeURI = 'https://beta.familysearch.org/platform/collection';
                break;
            default:
                $this->homeURI = 'https://sandbox.familysearch.org/platform/collection';
                break;
        }
        
        // Middleware
        $this->stack = new HandlerStack();
        $this->stack->setHandler(new CurlHandler());
        
        // Pending modifications
        if(isset($options['pendingModifications']) && is_array($options['pendingModifications']) && count($options['pendingModifications']) > 0){
            $experiments = join(",", $options['pendingModifications']);
            $this->stack->push(Middleware::mapRequest(function(RequestInterface $request) use($experiments) {
                return $request->withHeader('X-FS-Feature-Tag', $experiments);
            }));
        }
        
        // Set user agent string
        $userAgent = 'gedcomx-php/1.1.1 ' . \GuzzleHttp\default_user_agent();
        if(isset($options['userAgent'])){
            $userAgent = $options['userAgent'] . ' ' . $userAgent;
        }
        
        // Custom middleware
        if(isset($options['middleware']) && is_array($options['middleware'])) {
            foreach($options['middleware'] as $middleware){
                $this->stack->push($middleware);
            }
        }
        
        // This goes last so that it sees the final request and response
        if(isset($options['logger'])){
            $this->setLogger($options['logger']);
        }
        
        // Create client
        $this->client = new Client([
            'handler' => $this->stack,
            'headers' => [
                'User-Agent' => $userAgent
            ]
        ]);
        
        $this->stateFactory = new FamilyTreeStateFactory();
        
        $this->createHomeState();
        $this->createTreeState();
        
        if(isset($options['accessToken'])){
            $this->treeState->authenticateWithAccessToken($options['accessToken']);
        }
    }
    
    /**
     * @return Gedcomx\Extensions\FamilySearch\Rs\Client\FamilyTree\FamilyTreeCollectionState
     */
    public function familytree()
    {
        return $this->treeState;
    }
    
    /**
     * Authenticate via the OAuth2 password flow
     * 
     * @param string $username
     * @param string $password
     * 
     * @return FamilySearchClient
     */
    public function authenticateViaOAuth2Password($username, $password)
    {
        $this->requireClientIdAndRedirectURI();
        $this->treeState->authenticateViaOAuth2Password($username, $password, $this->clientId, $this->clientSecret);
        return $this;
    }
    
    /**
     * Authenticate with an OAuth2 code
     * 
     * @param string $code
     * 
     * @return FamilySearchClient
     */
    public function authenticateViaOAuth2AuthCode($code)
    {
        $this->requireClientIdAndRedirectURI();
        $this->treeState->authenticateViaOAuth2AuthCode($code, $this->redirectURI, $this->clientId);
        return $this;
    }
    
    /**
     * Get the URL that the user should be sent to in order to
     * begin the OAuth2 redirect flow.
     * 
     * @return string $url
     */
    public function getOAuth2AuthorizationURI()
    {
        $this->requireClientIdAndRedirectURI();
        
        $url = $this->treeState->getLink(GedcomxRel::OAUTH2_AUTHORIZE)->getHref();
        $params = array(
            'response_type' => 'code',
            'redirect_uri' => $this->redirectURI,
            'client_id' => $this->clientId
        );
        return $url . '?' . http_build_query($params);
    }
    
    /**
     * Get the access token for this session
     * 
     * @return string
     */
    public function getAccessToken()
    {
        return $this->treeState->getAccessToken();
    }
    
    /**
     * Set the access token for this session
     * 
     * @return FamilySearchClient
     */
    public function setAccessToken($accessToken)
    {
        $this->treeState->authenticateWithAccessToken($accessToken);
        return $this;
    }
    
    /**
     * Get a list of valid pending modifications
     * 
     * @return array Array of \Gedcomx\Extensions\FamilySearch\Feature
     */
    public function getAvailablePendingModifications()
    {
        $uri = $this->homeState->getCollection()->getLink('pending-modifications')->getHref();
        $headers = ['Accept' => Gedcomx::JSON_APPLICATION_TYPE];
        $request = new Request('GET', $uri, $headers);
        $response = GedcomxApplicationState::send($this->client, $request);
        

        // Get each pending feature
        $json = json_decode($response->getBody(), true);
        $fsp = new FamilySearchPlatform($json);
        $features = array();
        foreach ($fsp->getFeatures() as $feature) {
            $features[] = $feature;
        }
        
        return $features;
    }
    
    /**
     * Sets a logger instance on the object
     *
     * @param Psr\Log\LoggerInterface $logger
     * @return null
     */
    public function setLogger(LoggerInterface $logger)
    {
        $this->stack->push(LoggerMiddleware::middleware($logger));
    }
    
    /**
     * Ensure the homeState propery exists
     */
    private function createHomeState()
    {
        if($this->homeState == null){
            $this->homeState = $this->stateFactory->newCollectionState(
                $this->homeURI,
                'GET',
                $this->client
            );
        }
    }
    
    /**
     * Ensure the treeState propery exists
     */
    private function createTreeState()
    {
        $this->createHomeState();
        $this->treeState = $this->stateFactory->newCollectionState(
            $this->homeState->getCollection()->getLink('family-tree')->getHref(),
            'GET',
            $this->client
        );
    }
    
    /**
     * Throw an exception if the clientId or redirectURI are not set.
     * 
     * @throw GedcomxApplicationException
     */
    private function requireClientIdAndRedirectURI()
    {
        if(!$this->clientId)
        {
            throw new GedcomxApplicationException('No clientId has been set. Unable to begin authentication.');
        }
        if(!$this->redirectURI)
        {
            throw new GedcomxApplicationException('No redirectURI has been set. Unable to begin authentication.');
        }
    }
    
}
