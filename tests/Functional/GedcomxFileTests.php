<?php

namespace Gedcomx\Functional;

use Gedcomx\Extensions\FamilySearch\Platform\Tree\ChildAndParentsRelationship;
use Gedcomx\Extensions\FamilySearch\Rs\Client\FamilyTree\FamilyTreeStateFactory;
use Gedcomx\GedcomxFile\DefaultXMLSerialization;
use Gedcomx\GedcomxFile\GedcomxFile;
use Gedcomx\Tests\ApiTestCase;
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
        $outputFile = $this->tempDir . 'relationship.xml';
        $serializer->serialze($relationship->getEntity(), $outputFile);

        $this->assertFileExists($outputFile,'XML file not created.');

        $generated = new \DOMDocument();
        $generated->loadXML(file_get_contents($outputFile));
        $control = new \DOMDocument();
        $control->loadXML(file_get_contents($this->testRootDir.'Functional/control.xml'));

        $this->assertEqualXMLStructure($generated->firstChild, $control->firstChild,'XML output does not match test file.');
    }
}