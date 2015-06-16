<?php

namespace Gedcomx\Extensions\FamilySearch\Rs\Client;

use Gedcomx\Gedcomx;
use Gedcomx\Util\FilterableClient;
use Gedcomx\Rs\Client\Rel;
use Gedcomx\Rs\Client\Exception\GedcomxApplicationException;
use Gedcomx\Extensions\FamilySearch\FamilySearchPlatform;
use Gedcomx\Extensions\FamilySearch\Rs\Client\Util\ExperimentsFilter;
use Gedcomx\Extensions\FamilySearch\Rs\Client\FamilyTree\FamilyTreeStateFactory;

/**
 * API Client for the FamilySearch API
 *
 * Class Client
 *
 * @package Gedcomx\Extensions\FamilySearch\Rs\Client
 */
class FamilySearchClient {
    
    /**
     * Guzzle client object
     * 
     * @var \Gedcomx\Util\FilterableClient
     */
    private $client;
    
    /**
     * The redirect URI used during authentication via OAuth2
     *
     * @var string
     */
    private $redirectUri;
    
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
     * An access token for the current session
     * 
     * @var string
     */
    private $accessToken;
    
    /**
     * URI for the Discovery resource.
     * This is not used yet.
     * 
     * @var string
     */
    private $discoveryUri;
    
    /**
     * URI for the Collections resource.
     * 
     * @var string
     */
    private $collectionsUri;
    
    /**
     * @var \Gedcomx\Extensions\FamilySearch\Rs\Client\FamilyTree\FamilyTreeStateFactory
     */
    private $stateFactory;
    
    /**
     * @var \Gedcomx\Rs\Client\CollectionState
     */
    private $collectionState;
    
    /**
     * Construct a FamilySearch Client
     *
     * @param array $options A keyed of configuration options for the client.
     */
    public function __construct($options = array())
    {
        if(isset($options['redirectUri'])){
            $this->redirectUri = $options['redirectUri'];
        }
        if(isset($options['clientId'])){
            $this->clientId = $options['clientId'];
        }
        if(isset($options['accessToken'])){
            $this->accessToken = $options['accessToken'];
        }
        
        // environment option trumps
        if(isset($options['environment'])){
            switch($options['environment']){
                case 'production':
                    $this->discoveryUri = 'https://familysearch.org/platform/collections';
                    $this->collectionsUri = 'https://familysearch.org/platform/collections/tree';
                    break;
                case 'beta':
                    $this->discoveryUri = 'https://beta.familysearch.org/platform/collections';
                    $this->collectionsUri = 'https://beta.familysearch.org/platform/collections/tree';
                    break;
                case 'sandbox':
                    $this->discoveryUri = 'https://sandbox.familysearch.org/platform/collections';
                    $this->collectionsUri = 'https://sandbox.familysearch.org/platform/collections/tree';
                    break;
            }
        }
        
        // If the environment option is not set, look for the collectionsUri
        if(!$this->collectionsUri){
            if(isset($options['collectionsUri'])){
                $this->collectionsUri = $options['collectionsUri'];
            }
            
            // Otherwise default to production
            else {
                $this->collectionsUri = 'https://familysearch.org/platform/collections';
            }
        }
        
        $this->client = new FilterableClient('', array(
            "request.options" => array(
                "exceptions" => false
            )
        ));
        
        if(isset($options['pendingModifications']) && is_array($options['pendingModifications']) && count($options['pendingModifications']) > 0){
            $this->client->addFilter(new ExperimentsFilter($options['pendingModifications']));
        }
        
        $this->stateFactory = new FamilyTreeStateFactory();
        $this->createCollectionState();
    }
    
    /**
     * @return Gedcomx\Extensions\FamilySearch\Rs\Client\FamilyTree\FamilyTreeCollectionState
     */
    public function familytree()
    {
        return $this->collectionState;
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
        $this->requireClientIdAndRedirectUri();
        $this->collectionState->authenticateViaOAuth2Password($username, $password, $this->clientId, $this->clientSecret);
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
        $this->requireClientIdAndRedirectUri();
        $this->collectionState->authenticateViaOAuth2AuthCode($code, $this->redirectUri, $this->clientId);
        return $this;
    }
    
    /**
     * Get the URL that the user should be sent to in order to
     * begin the OAuth2 redirect flow.
     * 
     * @return string $url
     */
    public function getOAuth2AuthorizationURL()
    {
        $this->requireClientIdAndRedirectUri();
        
        $url = $this->collectionState->getLink(Rel::OAUTH2_AUTHORIZE)->getHref();
        $params = array(
            'response_type' => 'code',
            'redirect_uri' => $this->redirectUri,
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
        $this->createCollectionState();
        return $this->collectionState->getAccessToken();
    }
    
    /**
     * Get a list of valid pending modifications
     * 
     * @return array Array of \Gedcomx\Extensions\FamilySearch\Feature
     */
    public function getAvailablePendingModifications()
    {
        $request = $this->collectionState->getClient()->createRequest("GET", "https://sandbox.familysearch.org/platform/pending-modifications");
        $request->addHeader("Accept", Gedcomx::JSON_APPLICATION_TYPE);
        $response = $request->send($request);

        // Get each pending features
        $json = json_decode($response->getBody(true), true);
        $fsp = new FamilySearchPlatform($json);
        $features = [];
        foreach ($fsp->getFeatures() as $feature) {
            $features[] = $feature;
        }
        
        return $features;
    }
    
    /**
     * Ensure the collectionState propery exists
     */
    private function createCollectionState()
    {
        if($this->collectionState == null){
            $this->collectionState = $this->stateFactory->newCollectionState(
                $this->collectionsUri,
                'GET',
                $this->client
            );
        }
    }
    
    /**
     * Throw an exception if the clientId or redirectUri are not set.
     * 
     * @throw GedcomxApplicationException
     */
    private function requireClientIdAndRedirectUri()
    {
        if(!$this->clientId)
        {
            throw new GedcomxApplicationException('No clientId has been set. Unable to begin authentication.');
        }
        if(!$this->redirectUri)
        {
            throw new GedcomxApplicationException('No redirectUri has been set. Unable to begin authentication.');
        }
    }
    
}