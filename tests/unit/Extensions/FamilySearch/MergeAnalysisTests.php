<?php

namespace Gedcomx\Tests\Unit\Extensions\FamilySearch;

use Gedcomx\Extensions\FamilySearch\Platform\Tree\MergeAnalysis;
use Gedcomx\Tests\ApiTestCase;

class MergeAnalysisTests extends ApiTestCase
{
    public function testMergeAnalysisConstruction()
    {
        $analysis = new MergeAnalysis();

        $this->assertInstanceOf(MergeAnalysis::class, $analysis);
    }

    public function testMergeAnalysisGettersAndSetters()
    {
        $analysis = new MergeAnalysis();
        $analysis->setSurvivor(null);
        $analysis->setDuplicate(null);

        $this->assertNull($analysis->getSurvivor());
        $this->assertNull($analysis->getDuplicate());
    }

    public function testMergeAnalysisWithEmptyData()
    {
        $analysis = new MergeAnalysis([]);

        $this->assertInstanceOf(MergeAnalysis::class, $analysis);
    }
}
