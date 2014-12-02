<?php

namespace Gedcomx\Functional;

use Gedcomx\GedcomxFile\GedcomxFile;

class GedcomxFileTests extends \PHPUnit_Framework_TestCase
{
    public function testReadGedcomxFile()
    {
        $gecomdx = new GedcomxFile(dirname(__DIR__).'\sample.gedx');

        $this->markTestIncomplete("Not yet implemented.");
    }
}