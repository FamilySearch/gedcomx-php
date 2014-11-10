<?php


namespace Gedcomx\Extensions\FamilySearch\Rs\Client;


use Gedcomx\Rs\Client\GedcomxApplicationState;
use Guzzle\Http\Message\Request;
use Guzzle\Http\Message\Response;

class UserHistoryState extends FamilySearchCollectionState {

    protected function reconstruct(Request $request, Response $response)
    {
        return new UserHistoryState($this->client, $request, $response, $this->accessToken, $this->stateFactory);
    }

    protected function getScope()
    {
        return $this->getUserHistory();
    }

    /**
     * @return \Gedcomx\Source\SourceDescription[]|null
     */
    public function getUserHistory(){
        if ($this->getEntity() != null && $this->getEntity()->getSourceDescriptions() != null) {
            return $this->getEntity()->getSourceDescriptions();
        }

        return null;
    }
}