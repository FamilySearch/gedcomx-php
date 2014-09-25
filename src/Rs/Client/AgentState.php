<?php


namespace Gedcomx\Rs\Client;

use Gedcomx\Agent\Agent;
use Gedcomx\Gedcomx;

class AgentState extends GedcomxApplicationState
{

    function __construct($client, $request, $response, $accessToken, $stateFactory)
    {
        parent::__construct($client, $request, $response, $accessToken, $stateFactory);
    }

    protected function reconstruct($request, $response)
    {
        return new AgentState($this->client, $request, $response, $this->accessToken, $this->stateFactory);
    }

    protected function loadEntity()
    {
        $json = json_decode($this->response->getBody(), true);
        return new Gedcomx($json);
    }

    /**
     * @return Agent
     */
    protected function getScope()
    {
        return $this->getAgent();
    }

    /**
     * @return Agent
     */
    public function getAgent()
    {
        if ($this->entity) {
            $agents = $this->entity->getAgents();
            if (count($agents) > 0) {
                return $agents[0];
            }
        }

        return null;
    }
}