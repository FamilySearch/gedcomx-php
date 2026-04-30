<?php

namespace Gedcomx\Tests\Unit\Extensions\FamilySearch;

use Gedcomx\Extensions\FamilySearch\Platform\Tree\MergeConflict;
use Gedcomx\Tests\ApiTestCase;

class MergeConflictTests extends ApiTestCase
{
    public function testMergeConflictConstruction()
    {
        $conflict = new MergeConflict();

        $this->assertInstanceOf(MergeConflict::class, $conflict);
    }

    public function testMergeConflictGettersAndSetters()
    {
        $conflict = new MergeConflict();

        $this->assertNull($conflict->getSurvivorResource());
        $this->assertNull($conflict->getDuplicateResource());
    }

    public function testMergeConflictWithEmptyData()
    {
        $conflict = new MergeConflict([]);

        $this->assertInstanceOf(MergeConflict::class, $conflict);
    }
}
