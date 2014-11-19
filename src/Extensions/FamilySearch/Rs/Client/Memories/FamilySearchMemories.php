<?php 

namespace Gedcomx\Extensions\FamilySearch\Rs\Client\Memories;

use Gedcomx\Extensions\FamilySearch\Rs\Client\FamilySearchCollectionState;
use Guzzle\Http\Message\Request;
use Guzzle\Http\Message\Response;

class FamilySearchMemories extends FamilySearchCollectionState
{
    const URI = "https://familysearch.org/platform/collections/memories";
    const SANDBOX_URI = "https://sandbox.familysearch.org/platform/collections/memories";

    /**
     * @param Request  $request
     * @param Response $response
     *
     * @return FamilySearchCollectionState
     */
    protected function reconstruct(Request $request, Response $response)
    {
        return new FamilySearchMemories($this->client, $request, $response, $this->accessToken, $this->stateFactory);
    }
}