<?php

namespace Gedcomx\Extensions\FamilySearch;

/**
 * API Client for the FamilySearch API
 *
 * Class Client
 *
 * @package Gedcomx\Extensions\FamilySearch
 */
class Client {
    
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
     * An access token for the current session
     * 
     * @var string
     */
    private $accessToken;
    
    /**
     * URI for the Collections resource.
     * 
     * @var string
     */
    private $collectionsUri;
    
    /**
     * Construct a FamilySearch Client
     *
     * @param array $options A keyed of configuration options for the client.
     */
    public function __construct($options)
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
            $environment = $options['environment'];
            switch($environment){
                case 'production':
                    $this->collectionsUri = 'https://familysearch.org/platform/collections';
                    break;
                case 'beta':
                    $this->collectionsUri = 'https://beta.familysearch.org/platform/collections';
                    break;
                case 'sandbox':
                    $this->collectionsUri = 'https://sandbox.familysearch.org/platform/collections';
                    break;
            }
        }
        
        // If the environment option is not set, look for the collectionsUri
        if(!$this->collectionsUri && isset($options['collectionsUri'])){
            $this->collectionsUri = $options['collectionsUri'];
        }
        
        // Otherwise default to production
        else {
            $this->collectionsUri = 'https://familysearch.org/platform/collections';
        }
    }
    
}