<?php

namespace Gedcomx\Tests\Functional;

use Gedcomx\Extensions\FamilySearch\Rs\Client\FamilySearchClient;
use Gedcomx\Tests\ApiTestCase;

class FamilySearchClientTests extends ApiTestCase
{
    
    public function testCreateClient()
    {
        $client = new FamilySearchClient();
        $this->assertTrue(true);
    }
}