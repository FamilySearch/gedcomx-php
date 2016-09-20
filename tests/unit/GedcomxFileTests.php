<?php

namespace Gedcomx\Unit;

use Gedcomx\Extensions\FamilySearch\FamilySearchPlatform;
use Gedcomx\Extensions\FamilySearch\Platform\Tree\ChildAndParentsRelationship;
use Gedcomx\GedcomxFile\DefaultXMLSerialization;
use Gedcomx\GedcomxFile\GedcomxFile;
use Gedcomx\GedcomxFile\GedcomxOutput;
use Gedcomx\Tests\ApiTestCase;
use Gedcomx\Tests\ArtifactBuilder;
use Gedcomx\Tests\XMLBuilder;

class GedcomxFileTests extends ApiTestCase
{
    
    public function testReadGedcomxFile()
    {
        $gedcomx = new GedcomxFile($this->filesDir . 'sample.gedx');

        $this->assertEquals(
            'FamilySearch Platform API 0.1',
            $gedcomx->getAttribute('Created-By')
        );
        $this->assertEmpty($gedcomx->getWarnings(), "No warnings should have been generated.");
    }

    public function testXMLSerialization()
    {
        $relationship = new ChildAndParentsRelationship(array(
            'id' => 'MMMT-13L',
            'father' => array(
                'resource' => 'https://sandbox.familysearch.org/platform/tree/persons/KWWB-CH3',
                'resourceId' => 'KWWB-CH3'
            ),
            'mother' => array(
                'resource' => 'https://sandbox.familysearch.org/platform/tree/persons/KWWB-CHQ',
                'resourceId' => 'KWWB-CHQ'
            ),
            'child' => array(
                'resource' => 'https://sandbox.familysearch.org/platform/tree/persons/KWWB-CH7',
                'resourceId' => 'KWWB-CH7'
            )
        ));

        $serializer = new DefaultXMLSerialization();
        $xml = $serializer->serialize($relationship);

        $outputFile = $this->tempDir . 'relationship.xml';
        $fileHandle = fopen($outputFile, 'w');
        fwrite($fileHandle, $xml);
        fclose($fileHandle);

        $this->assertFileExists($outputFile,'XML file not created.');

        $generated = new \DOMDocument();
        $generated->loadXML(file_get_contents($outputFile));
        $control = new \DOMDocument();
        $control->loadXML(file_get_contents($this->filesDir . 'cap-relationship-control.xml'));

        $this->assertEqualXMLStructure($generated->firstChild, $control->firstChild,'XML output does not match test file.');
    }

    public function testXMLDeserialization()
    {
        $resources = array();

        $gedcomx = new GedcomxFile($this->testRootDir.'/files/sample.gedx');
        $entries = $gedcomx->getEntries();

        foreach($entries as $entry){
            if (strpos($entry->getContentType(),"xml") !== false) {
                $resources = $gedcomx->readResource($entry);
            }
        }

        $this->assertNotEmpty($resources,"No resources found in XML");
        $this->assertCount(4, $resources[0]->getPersons(), "Expecting four persons.");
    }

    public function testCreateGedxFile()
    {
        $relationship = new ChildAndParentsRelationship(array(
            'id' => 'MMMT-13L',
            'father' => array(
                'resource' => 'https://sandbox.familysearch.org/platform/tree/persons/KWWB-CH3',
                'resourceId' => 'KWWB-CH3'
            ),
            'mother' => array(
                'resource' => 'https://sandbox.familysearch.org/platform/tree/persons/KWWB-CHQ',
                'resourceId' => 'KWWB-CHQ'
            ),
            'child' => array(
                'resource' => 'https://sandbox.familysearch.org/platform/tree/persons/KWWB-CH7',
                'resourceId' => 'KWWB-CH7'
            )
        ));
        $fs = new FamilySearchPlatform();
        $fs->addChildAndParentsRelationship($relationship);

        $image1 = ArtifactBuilder::makeImage();
        $image2 = ArtifactBuilder::makeImage();

        $testfile = $this->tempDir . "test.gedx";
        $writeIt = new GedcomxOutput();
        $writeIt->addFamilySearchResource($fs);
        $writeIt->addFileResource($image1);
        $writeIt->addFileResource($image2);
        $writeIt->writeToFile($testfile);

        $this->assertFileExists($testfile, "test.gedx not written.");

        $readIt = new GedcomxFile($testfile);
        $this->assertEmpty($readIt->getWarnings(), "No warnings should have been generated.");
    }
}
