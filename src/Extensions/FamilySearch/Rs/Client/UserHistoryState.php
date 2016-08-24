<?php


namespace Gedcomx\Extensions\FamilySearch\Rs\Client;


use Gedcomx\Rs\Client\GedcomxApplicationState;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;

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
     * @param \GuzzleHttp\Psr7\Request  $request
     * @param \GuzzleHttp\Psr7\Response $response
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
     * @return \Gedcomx\Atom\Feed|null
     */
    protected function getScope()
    {
        return $this->getUserHistory();
    }

    /**
     * Gets the user history represented by this state instance.
     *
     * @return \Gedcomx\Atom\Feed|null
     */
    public function getUserHistory(){
        return $this->getEntity();
    }
}