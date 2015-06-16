<?php

namespace Gedcomx\Extensions\FamilySearch\Rs\Client;

use Gedcomx\Util\FilterableClient;
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
     * An access token for the current session
     * 
     * @var string
     */
    private $accessToken;
    
    /**
     * URI for the Discovery resource.
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
            $environment = $options['environment'];
            switch($environment){
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
        if(!$this->collectionsUri && isset($options['collectionsUri'])){
            $this->collectionsUri = $options['collectionsUri'];
        }
        
        // Otherwise default to production
        else {
            $this->collectionsUri = 'https://familysearch.org/platform/collections';
        }
        
        $this->client = new FilterableClient('', array(
            "request.options" => array(
                "exceptions" => false
            )
        ));
        
        if(isset($options['pendingModifications']) && is_array($options['pendingModifications'])){
            $this->client->addFilter(new ExperimentsFilter($options['pendingModifications']));
        }
        
        $this->stateFactory = new FamilyTreeStateFactory();
    }
    
    /**
     * @return Gedcomx\Extensions\FamilySearch\Rs\Client\FamilyTree\FamilyTreeCollectionState
     */
    public function familytree(){
        if($this->collectionState == null){
            $this->collectionState = $this->stateFactory->newCollectionState(
                $this->collectionsUri,
                'GET',
                $this->client
            );
        }
        return $this->collectionState;
    }
    
}