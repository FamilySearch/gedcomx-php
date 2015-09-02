<?php 

namespace Gedcomx\Extensions\FamilySearch\Rs\Client\Memories;

use Gedcomx\Extensions\FamilySearch\Rs\Client\FamilySearchCollectionState;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;

/**
 * The FamilySearchMemories is a collection of FamilySearch memories and exposes management of those memories.
 *
 * Class FamilySearchMemories
 *
 * @package Gedcomx\Extensions\FamilySearch\Rs\Client\Memories
 */
class FamilySearchMemories extends FamilySearchCollectionState
{
    /**
     * The default production environment URI for this collection.
     */
    const URI = "https://familysearch.org/platform/collections/memories";
    /**
     * The default sandbox environment URI for this collection.
     */
    const SANDBOX_URI = "https://sandbox.familysearch.org/platform/collections/memories";

    /**
     * Clones the current state instance.
     *
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