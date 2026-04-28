<?php

namespace Gedcomx\Tests\Unit\Source;

use Gedcomx\Tests\ApiTestCase;
use Gedcomx\Source\SourceCitation;

class SourceCitationTests extends ApiTestCase
{
    public function testSourceCitationGettersAndSetters()
    {
        $citation = new SourceCitation();
        $citation->setValue('Smith, John. "Family History." Journal of Genealogy 10 (2020): 45-67.');

        $this->assertEquals('Smith, John. "Family History." Journal of Genealogy 10 (2020): 45-67.', $citation->getValue());
    }

    public function testSourceCitationWithLanguage()
    {
        $citation = new SourceCitation();
        $citation->setValue('Citation text');
        $citation->setLang('en');

        $this->assertEquals('Citation text', $citation->getValue());
        $this->assertEquals('en', $citation->getLang());
    }

    public function testSourceCitationEmpty()
    {
        $citation = new SourceCitation();

        $this->assertNull($citation->getValue());
    }
}
