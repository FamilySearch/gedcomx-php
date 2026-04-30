<?php

namespace Gedcomx\Tests\Unit;

use Gedcomx\Tests\ApiTestCase;

/**
 * Validates test fixtures (XML and JSON files) are well-formed and loadable
 */
class FixtureValidationTest extends ApiTestCase
{
    public function testXmlFixturesAreWellFormed()
    {
        $xmlFiles = glob($this->filesDir . '*.xml');
        $this->assertNotEmpty($xmlFiles, 'No XML fixtures found');

        foreach ($xmlFiles as $xmlFile) {
            $dom = new \DOMDocument();
            $loaded = @$dom->load($xmlFile);
            $this->assertTrue(
                $loaded,
                "XML file {$xmlFile} is not well-formed or could not be loaded"
            );
        }
    }

    public function testJsonFixturesAreValid()
    {
        $jsonFiles = glob($this->filesDir . '*.json');
        $this->assertNotEmpty($jsonFiles, 'No JSON fixtures found');

        foreach ($jsonFiles as $jsonFile) {
            $content = file_get_contents($jsonFile);
            $decoded = json_decode($content, true);
            $this->assertNotNull(
                $decoded,
                "JSON file {$jsonFile} is not valid JSON: " . json_last_error_msg()
            );
            $this->assertEquals(
                JSON_ERROR_NONE,
                json_last_error(),
                "JSON file {$jsonFile} has errors: " . json_last_error_msg()
            );
        }
    }

    public function testGedxFilesAreReadable()
    {
        $gedxFiles = glob($this->filesDir . '*.gedx');
        $this->assertNotEmpty($gedxFiles, 'No GEDX fixtures found');

        foreach ($gedxFiles as $gedxFile) {
            $zip = new \ZipArchive();
            $result = $zip->open($gedxFile);
            $this->assertTrue(
                $result === true,
                "GEDX file {$gedxFile} could not be opened as ZIP archive"
            );
            $zip->close();
        }
    }

    public function testXmlFixturesHaveValidStructure()
    {
        $xmlFiles = glob($this->filesDir . '*.xml');

        foreach ($xmlFiles as $xmlFile) {
            $xml = simplexml_load_file($xmlFile);
            $this->assertNotFalse(
                $xml,
                "Could not parse XML structure in {$xmlFile}"
            );

            // Check for namespace declarations (GEDCOM X files should have namespaces)
            $namespaces = $xml->getNamespaces(true);
            $this->assertNotEmpty(
                $namespaces,
                "XML file {$xmlFile} should have namespace declarations"
            );
        }
    }

    public function testJsonFixturesHaveExpectedKeys()
    {
        $jsonFile = $this->filesDir . 'person.json';
        if (!file_exists($jsonFile)) {
            $this->markTestSkipped('person.json fixture not found');
        }

        $data = json_decode(file_get_contents($jsonFile), true);

        // Person JSON should have expected structure
        $this->assertArrayHasKey('id', $data, 'Person fixture should have id');

        // Validate common GEDCOM X fields if present
        if (isset($data['names'])) {
            $this->assertIsArray($data['names']);
        }
        if (isset($data['facts'])) {
            $this->assertIsArray($data['facts']);
        }
    }

    public function testXmlFixturesCanBeRoundTripped()
    {
        $xmlFile = $this->filesDir . 'record.xml';
        if (!file_exists($xmlFile)) {
            $this->markTestSkipped('record.xml fixture not found');
        }

        // Load original
        $dom1 = new \DOMDocument();
        $dom1->load($xmlFile);

        // Serialize and deserialize
        $xml = $dom1->saveXML();

        $dom2 = new \DOMDocument();
        $dom2->loadXML($xml);

        // Compare canonical forms
        $this->assertEquals(
            $dom1->C14N(),
            $dom2->C14N(),
            'XML round-trip should preserve structure'
        );
    }
}
