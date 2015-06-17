<?php

namespace Gedcomx\Extensions\FamilySearch\Rs\Client;

use Gedcomx\Gedcomx;
use Gedcomx\Util\FilterableClient;
use Gedcomx\Rs\Client\Rel as GedcomxRel;
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
     * URI for the Collections resource.
     * 
     * @var string
     */
    private $collectionsURI;
    
    /**
     * @var \Gedcomx\Extensions\FamilySearch\Rs\Client\FamilyTree\FamilyTreeStateFactory
     */
    private $stateFactory;
    
    /**
     * @var \Gedcomx\Rs\Client\CollectionState
     */
    private $treeState;
    
    /**
     * @var \Gedcomx\Rs\Client\CollectionsState
     */
    private $collectionsState;
    
    /**
     * Construct a FamilySearch Client
     *
     * @param array $options A keyed of configuration options for the client.
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
                $this->collectionsURI = 'https://familysearch.org/platform/collections';
                break;
            case 'beta':
                $this->collectionsURI = 'https://beta.familysearch.org/platform/collections';
                break;
            default:
                $this->collectionsURI = 'https://sandbox.familysearch.org/platform/collections';
                break;
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
        
        $this->createCollectionsState();
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
     * Get a list of valid pending modifications
     * 
     * @return array Array of \Gedcomx\Extensions\FamilySearch\Feature
     */
    public function getAvailablePendingModifications()
    {
        $request = $this->treeState->getClient()->createRequest("GET", "https://sandbox.familysearch.org/platform/pending-modifications");
        $request->addHeader("Accept", Gedcomx::JSON_APPLICATION_TYPE);
        $response = $request->send($request);

        // Get each pending features
        $json = json_decode($response->getBody(true), true);
        $fsp = new FamilySearchPlatform($json);
        $features = array();
        foreach ($fsp->getFeatures() as $feature) {
            $features[] = $feature;
        }
        
        return $features;
    }
    
    /**
     * Ensure the collectionsState propery exists
     */
    private function createCollectionsState()
    {
        if($this->collectionsState == null){
            $this->collectionsState = $this->stateFactory->newCollectionsState(
                $this->collectionsURI,
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
        $this->createCollectionsState();
        foreach($this->collectionsState->getCollections() as $collection){
            if($collection->getId() == 'FSFT'){
                $this->treeState = $this->collectionsState->readCollection($collection);
            }
        }
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