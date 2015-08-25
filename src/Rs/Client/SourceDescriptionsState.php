<?php


namespace Gedcomx\Rs\Client;

use Gedcomx\Gedcomx;
use Gedcomx\Rs\Client\Options\StateTransitionOption;
use Gedcomx\Source\SourceDescription;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use RuntimeException;

/**
 * The SourceDescriptionsState exposes management functions for a collection of source descriptions.
 */
class SourceDescriptionsState extends GedcomxApplicationState
{
    /**
     * Constructs a source descriptions state using the specified client, request, response, access token, and state factory.
     *
     * @param \GuzzleHttp\Client             $client
     * @param \GuzzleHttp\Psr7\Request    $request
     * @param \GuzzleHttp\Psr7\Response   $response
     * @param string                          $accessToken
     * @param \Gedcomx\Rs\Client\StateFactory $stateFactory
     */
    function __construct(Client $client, Request $request, Response $response, $accessToken, StateFactory $stateFactory)
    {
        parent::__construct($client, $request, $response, $accessToken, $stateFactory);
    }

    /**
     * Clones the current state instance.
     *
     * @param \GuzzleHttp\Psr7\Request  $request
     * @param \GuzzleHttp\Psr7\Response $response
     *
     * @return \Gedcomx\Rs\Client\SourceDescriptionsState
     */
    protected function reconstruct(Request $request, Response $response)
    {
        return new SourceDescriptionsState($this->client, $request, $response, $this->accessToken, $this->stateFactory);
    }

    /**
     * Returns the entity from the REST API response.
     *
     * @return \Gedcomx\Gedcomx
     */
    protected function loadEntity()
    {
        $json = json_decode($this->response->getBody(), true);
        return new Gedcomx($json);
    }

    /**
     * Gets the main data element represented by this state instance.
     *
     * @return object
     */
    protected function getScope()
    {
        return $this->getEntity();
    }

    /**
     * Reads the collection specified by this state instance.
     *
     * @return CollectionState|null
     */
    public function readCollection()
    {
        $link = $this->getLink(Rel::COLLECTION);
        if ($link == null || $link->getHref() == null)
        {
            return null;
        }

        $request = $this->createAuthenticatedGedcomxRequest('GET', $link->getHref());
        return $this->stateFactory->createState(
            'CollectionState',
            $this->client,
            $request,
            $this->passOptionsTo('invoke', array($request), func_get_args()),
            $this->accessToken
        );
    }

    /**
     * Adds a source description the current collection of source descriptions.
     *
     * @param \Gedcomx\Source\SourceDescription $source
     * @param Options\StateTransitionOption     $option,...
     *
     * @return SourceDescriptionState|null
     */
    public function addSourceDescription(SourceDescription $source, StateTransitionOption $option = null)
    {
        $entity = new Gedcomx();
        $entity->addSourceDescription($source);
        $request = $this->createAuthenticatedGedcomxRequest('POST', $this->getSelfUri(), [], null, $entity->toJson());
        return $this->stateFactory->createState(
            'SourceDescriptionState',
            $this->client,
            $request,
            $this->passOptionsTo('invoke', array($request), func_get_args()),
            $this->accessToken
        );
    }
}