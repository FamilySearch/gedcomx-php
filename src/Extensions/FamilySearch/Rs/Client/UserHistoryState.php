<?php


namespace Gedcomx\Extensions\FamilySearch\Rs\Client;


use Gedcomx\Rs\Client\GedcomxApplicationState;
use Guzzle\Http\Message\Request;
use Guzzle\Http\Message\Response;

/**
 * The UserHistoryState exposes management functions for user history.
 *
 * Class UserHistoryState
 *
 * @package Gedcomx\Extensions\FamilySearch\Rs\Client
 */
class UserHistoryState extends FamilySearchCollectionState {
    /**
     * Clones the current state instance.
     *
     * @param \Guzzle\Http\Message\Request  $request
     * @param \Guzzle\Http\Message\Response $response
     *
     * @return \Gedcomx\Extensions\FamilySearch\Rs\Client\UserHistoryState
     */
    protected function reconstruct(Request $request, Response $response)
    {
        return new UserHistoryState($this->client, $request, $response, $this->accessToken, $this->stateFactory);
    }

    /**
     * Gets the main data element represented by this state instance.
     *
     * @return \Gedcomx\Source\SourceDescription[]|null
     */
    protected function getScope()
    {
        return $this->getUserHistory();
    }

    /**
     * Gets the user history represented by this state instance.
     *
     * @return \Gedcomx\Source\SourceDescription[]|null
     */
    public function getUserHistory(){
        if ($this->getEntity() != null && $this->getEntity()->getSourceDescriptions() != null) {
            return $this->getEntity()->getSourceDescriptions();
        }

        return null;
    }
}