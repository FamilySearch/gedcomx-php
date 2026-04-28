<?php

namespace Gedcomx\Tests\Unit\Extensions\FamilySearch;

use Gedcomx\Extensions\FamilySearch\Platform\Tree\Merge;
use Gedcomx\Tests\ApiTestCase;

class MergeTests extends ApiTestCase
{
    public function testMergeDeserialization()
    {
        $json = $this->loadJson('merge.json');
        $merge = new Merge($json);

        $this->assertCount(1, $merge->getResourcesToDelete());
        $this->assertCount(1, $merge->getResourcesToCopy());
    }

    public function testMergeGettersAndSetters()
    {
        $merge = new Merge();
        $merge->setResourcesToDelete([]);
        $merge->setResourcesToCopy([]);

        $this->assertIsArray($merge->getResourcesToDelete());
        $this->assertIsArray($merge->getResourcesToCopy());
    }

    public function testMergeWithoutResources()
    {
        $merge = new Merge([]);

        $this->assertEmpty($merge->getResourcesToDelete());
        $this->assertEmpty($merge->getResourcesToCopy());
    }
}
