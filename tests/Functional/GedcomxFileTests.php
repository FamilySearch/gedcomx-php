<?php

namespace Gedcomx\Functional;

use Gedcomx\Extensions\FamilySearch\Platform\Tree\ChildAndParentsRelationship;
use Gedcomx\Extensions\FamilySearch\Rs\Client\FamilyTree\FamilyTreeStateFactory;
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
        $gedcomx = new GedcomxFile($this->testRootDir.'sample.gedx');

        $this->assertEquals(
            'FamilySearch Platform API 0.1',
            $gedcomx->getAttribute('Created-By')
        );
        $this->assertEmpty($gedcomx->getWarnings(), "No warnings should have been generated.");
    }

    /**
     * @throws \Gedcomx\Rs\Client\Exception\GedcomxApplicationException
     */
    public function testXMLSerialization()
    {
        $people = XMLBuilder::XMLRelationshipData();

        $factory = new FamilyTreeStateFactory();
        $this->collectionState($factory);

        $father = $this->collectionState()->addPerson($people['father']);
        $mother = $this->collectionState()->addPerson($people['mother']);
        $child = $this->collectionState()->addPerson($people['child']);
        $this->queueForDelete($father,$mother,$child);

        $family = new ChildAndParentsRelationship();
        $family->setChild($child->getResourceReference());
        $family->setFather($father->getResourceReference());
        $family->setMother($mother->getResourceReference());

        $relationship = $this->collectionState()->addChildAndParentsRelationship($family)->get();
        $this->queueForDelete($relationship);

        $serializer = new DefaultXMLSerialization();
        $xml = $serializer->serialize($relationship->getEntity());

        $outputFile = $this->tempDir . 'relationship.xml';
        $fileHandle = fopen($outputFile, 'w');
        fwrite($fileHandle, $xml);
        fclose($fileHandle);

        $this->assertFileExists($outputFile,'XML file not created.');

        $generated = new \DOMDocument();
        $generated->loadXML(file_get_contents($outputFile));
        $control = new \DOMDocument();
        $control->loadXML(file_get_contents($this->testRootDir.'Functional/control.xml'));

        $this->assertEqualXMLStructure($generated->firstChild, $control->firstChild,'XML output does not match test file.');
    }

    public function testXMLDeserialization()
    {
        $resources = array();

        $gedcomx = new GedcomxFile($this->testRootDir.'sample.gedx');
        $entries = $gedcomx->getEntries();

        foreach($entries as $entry){

            if (strpos($entry->getContentType(),"xml") !== false) {
                $resources[] = $gedcomx->readResource($entry);
            }
        }

        $this->assertNotEmpty($resources,"No resources found in XML");
        $this->assertCount(4, $resources[0]->getPersons(), "Expecting four persons.");
    }

    public function testCreateGedxFile()
    {
        $people = XMLBuilder::XMLRelationshipData();

        $factory = new FamilyTreeStateFactory();
        $this->collectionState($factory);

        $father = $this->collectionState()->addPerson($people['father']);
        $mother = $this->collectionState()->addPerson($people['mother']);
        $child = $this->collectionState()->addPerson($people['child']);
        $this->queueForDelete($father,$mother,$child);

        $family = new ChildAndParentsRelationship();
        $family->setChild($child->getResourceReference());
        $family->setFather($father->getResourceReference());
        $family->setMother($mother->getResourceReference());

        $relationship = $this->collectionState()->addChildAndParentsRelationship($family)->get();
        $this->queueForDelete($relationship);

        $image1 = ArtifactBuilder::makeImage();
        $image2 = ArtifactBuilder::makeImage();

        $testfile = $this->tempDir . "test.gedx";
        $writeIt = new GedcomxOutput();
        $writeIt->addFamilySearchResource($relationship->getEntity());
        $writeIt->addFileResource($image1);
        $writeIt->addFileResource($image2);
        $writeIt->writeToFile($testfile);

        $this->assertFileExists($testfile, "test.gedx not written.");

        $readIt = new GedcomxFile($testfile);
        $this->assertEmpty($readIt->getWarnings(), "No warnings should have been generated.");
    }
}