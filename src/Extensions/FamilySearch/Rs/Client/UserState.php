<?php

namespace Gedcomx\Extensions\FamilySearch\Rs\Client;

use Gedcomx\Extensions\FamilySearch\FamilySearchPlatform;
use Gedcomx\Rs\Client\GedcomxApplicationState;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;

/**
 * The UserState exposes management functions for a user.
 *
 * Class UserState
 *
 * @package Gedcomx\Extensions\FamilySearch\Rs\Client
 */
class UserState extends FamilySearchCollectionState{
    /**
     * Clones the current state instance.
     *
     * @param \GuzzleHttp\Psr7\Request  $request
     * @param \GuzzleHttp\Psr7\Response $response
     *
     * @return \Gedcomx\Extensions\FamilySearch\Rs\Client\UserState
     */
    protected function reconstruct(Request $request, Response $response)
    {
        return new UserState($this->client, $request, $response, $this->accessToken, $this->stateFactory);
    }

    /**
     * Gets the main data element represented by this state instance.
     *
     * @return \Gedcomx\Extensions\FamilySearch\Platform\Users\User
     */
    protected function getScope()
    {
        return $this->getUser();
    }

    /**
     * Gets the user represented by the current state instance.
     *
     * @return \Gedcomx\Extensions\FamilySearch\Platform\Users\User.php|null
     */
    public function getUser(){
        if ($this->getEntity() != null && $this->getEntity()->getUsers() != null) {
            $users = $this->getEntity()->getUsers();
            if (count($users) > 0) {
                return $users[0];
            }
        }

        return null;
    }
}