<?php

namespace Gedcomx\Tests\Unit\Conclusion;

use Gedcomx\Conclusion\DateInfo;
use Gedcomx\Tests\ApiTestCase;

class DateInfoTests extends ApiTestCase
{
    public function testDateInfoDeserialization()
    {
        $json = $this->loadJson('date-info.json');
        $date = new DateInfo($json);

        $this->assertEquals('January 1, 1900', $date->getOriginal());
        $this->assertEquals('+1900-01-01', $date->getFormal());
        $this->assertCount(1, $date->getNormalizedExtensions());
    }

    public function testDateInfoGettersAndSetters()
    {
        $date = new DateInfo();
        $date->setOriginal('circa 1850');
        $date->setFormal('+1850');

        $this->assertEquals('circa 1850', $date->getOriginal());
        $this->assertEquals('+1850', $date->getFormal());
    }

    public function testDateInfoWithoutFormal()
    {
        $date = new DateInfo(['original' => 'about 1850']);

        $this->assertEquals('about 1850', $date->getOriginal());
        $this->assertNull($date->getFormal());
    }
}
