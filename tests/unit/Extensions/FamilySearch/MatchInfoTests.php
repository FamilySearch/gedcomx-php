<?php

namespace Gedcomx\Tests\Unit\Extensions\FamilySearch;

use Gedcomx\Extensions\FamilySearch\Platform\Tree\MatchInfo;
use Gedcomx\Tests\ApiTestCase;

class MatchInfoTests extends ApiTestCase
{
    public function testMatchInfoDeserialization()
    {
        $json = $this->loadJson('match-info.json');
        $matchInfo = new MatchInfo($json);

        $this->assertEquals('http://familysearch.org/v1/Pending', $matchInfo->getStatus());
        $this->assertEquals('https://familysearch.org/platform/collections/records', $matchInfo->getCollection());
    }

    public function testMatchInfoGettersAndSetters()
    {
        $matchInfo = new MatchInfo();
        $matchInfo->setStatus('http://familysearch.org/v1/Accepted');
        $matchInfo->setCollection('https://familysearch.org/platform/collections/census');

        $this->assertEquals('http://familysearch.org/v1/Accepted', $matchInfo->getStatus());
        $this->assertEquals('https://familysearch.org/platform/collections/census', $matchInfo->getCollection());
    }

    public function testMatchInfoWithoutCollection()
    {
        $matchInfo = new MatchInfo([
            'status' => 'http://familysearch.org/v1/Rejected'
        ]);

        $this->assertEquals('http://familysearch.org/v1/Rejected', $matchInfo->getStatus());
        $this->assertNull($matchInfo->getCollection());
    }
}
