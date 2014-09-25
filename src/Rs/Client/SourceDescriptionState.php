<?php


namespace Gedcomx\Rs\Client;

use Gedcomx\Conclusion\Person;
use Gedcomx\Gedcomx;
use Gedcomx\Source\SourceDescription;
use RuntimeException;

class SourceDescriptionState extends GedcomxApplicationState
{

    function __construct($client, $request, $response, $accessToken, $stateFactory)
    {
        parent::__construct($client, $request, $response, $accessToken, $stateFactory);
    }

    protected function reconstruct($request, $response)
    {
        return new SourceDescriptionState($this->client, $request, $response, $this->accessToken, $this->stateFactory);
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
     * @param SourceDescription $description
     * @return SourceDescriptionState
     */
    public function update($description)
    {
        throw new RuntimeException("function currently not implemented."); //todo: implement
    }

    /**
     * @return PersonsState
     */
    public function readPersonas()
    {
        throw new RuntimeException("function currently not implemented."); //todo: implement
    }

    /**
     * @param Person|Gedcomx $persona
     * @return PersonState
     */
    public function addPersona($persona)
    {
        throw new RuntimeException("function currently not implemented."); //todo: implement
    }

}