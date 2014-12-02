<?php

namespace Gedcomx\Functional;

use Gedcomx\GedcomxFile\GedcomxFile;

class GedcomxFileTests extends \PHPUnit_Framework_TestCase
{
    public function testReadGedcomxFile()
    {
        $gedcomx = new GedcomxFile(dirname(__DIR__).DIRECTORY_SEPARATOR.'sample.gedx');

        $this->assertEquals(
            'FamilySearch Platform API 0.1',
            $gedcomx->getAttribute('Created-By')
        );
        $this->assertEmpty($gedcomx->getWarnings(), "No warnings should have been generated.");
    }
}