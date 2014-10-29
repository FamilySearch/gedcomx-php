<?php


namespace Gedcomx\Rs\Client;

use Gedcomx\Gedcomx;
use Gedcomx\Rs\Client\Options\StateTransitionOption;
use Gedcomx\Source\SourceDescription;
use Guzzle\Http\Client;
use Guzzle\Http\Message\Request;
use Guzzle\Http\Message\Response;
use RuntimeException;

class SourceDescriptionsState extends GedcomxApplicationState
{

    function __construct(Client $client, Request $request, Response $response, $accessToken, StateFactory $stateFactory)
    {
        parent::__construct($client, $request, $response, $accessToken, $stateFactory);
    }

    protected function reconstruct(Request $request, Response $response)
    {
        return new SourceDescriptionsState($this->client, $request, $response, $this->accessToken, $this->stateFactory);
    }

    protected function loadEntity()
    {
        $json = json_decode($this->response->getBody(), true);
        return new Gedcomx($json);
    }

    protected function getScope()
    {
        return $this->getEntity();
    }

    /**
     * @link https://familysearch.org/developers/docs/api/sources/Source_Descriptions_resource
     *
     * @throws \RuntimeException
     */
    public function readCollection()
    {
        throw new RuntimeException("Function currently not implemented in API.");
    }

    /**
     * @param \Gedcomx\Source\SourceDescription $source
     * @param Options\StateTransitionOption     $option
     *
     * @return SourceDescriptionState|null
     */
    public function addSourceDescription(SourceDescription $source, StateTransitionOption $option = null)
    {
        $entity = new Gedcomx();
        $entity->addSourceDescription($source);
        $request = $this->createAuthenticatedGedcomxRequest(Request::POST, $this->getSelfUri());
        return $this->stateFactory->createState(
            'SourceDescriptionState',
            $this->client,
            $request,
            $this->passOptionsTo('invoke', array($request), func_get_args()),
            $this->accessToken
        );
    }

    public function readNextPage()
    {
        return parent::readNextPage();
    }

    public function readPreviousPage()
    {
        return parent::readPreviousPage();
    }

    public function readFirstPage()
    {
        return parent::readFirstPage();
    }

    public function readLastPage()
    {
        return parent::readLastPage();
    }

}