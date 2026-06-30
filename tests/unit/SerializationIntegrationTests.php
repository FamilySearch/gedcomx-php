<?php

namespace Gedcomx\Tests\Unit;

use Gedcomx\Conclusion\DateInfo;
use Gedcomx\Conclusion\FamilyView;
use Gedcomx\Conclusion\Fact;
use Gedcomx\Conclusion\Person;
use Gedcomx\Common\ResourceReference;
use Gedcomx\Types\CalendarType;
use Gedcomx\Types\ConfidenceLevel;
use Gedcomx\Tests\ApiTestCase;

/**
 * Integration tests for serialization of new and updated GEDCOM X model classes
 * Tests JSON and XML serialization/deserialization for SDK 4.3.0 updates
 */
class SerializationIntegrationTests extends ApiTestCase
{
    // ==================== JSON Serialization Tests ====================

    public function testDateInfoJsonSerialization()
    {
        // Create DateInfo with all new properties
        $date = new DateInfo();
        $date->setOriginal('10 January 1752');
        $date->setFormal('+1752-01-10');
        $date->setConfidence(ConfidenceLevel::HIGH);
        $date->setCalendar(CalendarType::GREGORIAN);

        // Create alternate calendar date
        $julianDate = new DateInfo();
        $julianDate->setOriginal('30 December 1751');
        $julianDate->setFormal('+1751-12-30');
        $julianDate->setCalendar(CalendarType::JULIAN);
        $julianDate->setConfidence(ConfidenceLevel::MEDIUM);

        $date->setAlternateCalendarDates([$julianDate]);

        // Convert to array
        $array = $date->toArray();

        // Verify all properties are in array
        $this->assertArrayHasKey('original', $array);
        $this->assertArrayHasKey('formal', $array);
        $this->assertArrayHasKey('confidence', $array);
        $this->assertArrayHasKey('calendar', $array);
        $this->assertArrayHasKey('alternateCalendarDates', $array);

        $this->assertEquals('10 January 1752', $array['original']);
        $this->assertEquals(ConfidenceLevel::HIGH, $array['confidence']);
        $this->assertEquals(CalendarType::GREGORIAN, $array['calendar']);
        $this->assertIsArray($array['alternateCalendarDates']);
        $this->assertCount(1, $array['alternateCalendarDates']);
        $this->assertEquals('30 December 1751', $array['alternateCalendarDates'][0]['original']);
    }

    public function testDateInfoJsonEncoding()
    {
        // Create DateInfo
        $date = new DateInfo();
        $date->setOriginal('1800');
        $date->setFormal('+1800');
        $date->setConfidence(ConfidenceLevel::LOW);
        $date->setCalendar(CalendarType::JULIAN);

        // Encode to JSON
        $json = json_encode($date->toArray());
        $this->assertNotFalse($json);
        $this->assertStringContainsString('"original":"1800"', $json);
        // JSON encodes forward slashes as \/, which is valid
        $this->assertStringContainsString('confidence', $json);
        $this->assertStringContainsString('gedcomx.org', $json);
        $this->assertStringContainsString('calendar', $json);
    }

    public function testDateInfoJsonDecoding()
    {
        // Create JSON string
        $json = '{
            "original": "1 January 2000",
            "formal": "+2000-01-01",
            "confidence": "http://gedcomx.org/High",
            "calendar": "http://gedcomx.org/Gregorian",
            "alternateCalendarDates": [
                {
                    "original": "18 Tevet 5760",
                    "calendar": "http://gedcomx.org/Hebrew"
                }
            ]
        }';

        $array = json_decode($json, true);
        $date = new DateInfo($array);

        $this->assertEquals('1 January 2000', $date->getOriginal());
        $this->assertEquals('+2000-01-01', $date->getFormal());
        $this->assertEquals(ConfidenceLevel::HIGH, $date->getConfidence());
        $this->assertEquals(CalendarType::GREGORIAN, $date->getCalendar());
        $this->assertCount(1, $date->getAlternateCalendarDates());
        $this->assertEquals('18 Tevet 5760', $date->getAlternateCalendarDates()[0]->getOriginal());
        $this->assertEquals(CalendarType::HEBREW, $date->getAlternateCalendarDates()[0]->getCalendar());
    }

    public function testFamilyViewJsonSerialization()
    {
        // Create FamilyView
        $familyView = new FamilyView();
        $familyView->setId('FAMILY-1');

        $parent1 = new ResourceReference();
        $parent1->setResource('https://familysearch.org/persons/P-1');
        $familyView->setParent1($parent1);

        $parent2 = new ResourceReference();
        $parent2->setResource('https://familysearch.org/persons/P-2');
        $familyView->setParent2($parent2);

        $child1 = new ResourceReference();
        $child1->setResource('https://familysearch.org/persons/C-1');
        $familyView->addChild($child1);

        $child2 = new ResourceReference();
        $child2->setResource('https://familysearch.org/persons/C-2');
        $familyView->addChild($child2);

        // Convert to array
        $array = $familyView->toArray();

        // Verify structure
        $this->assertArrayHasKey('id', $array);
        $this->assertArrayHasKey('parent1', $array);
        $this->assertArrayHasKey('parent2', $array);
        $this->assertArrayHasKey('children', $array);
        $this->assertEquals('FAMILY-1', $array['id']);
        $this->assertCount(2, $array['children']);
    }

    public function testFamilyViewJsonEncoding()
    {
        // Create FamilyView
        $familyView = new FamilyView();
        $familyView->setId('TEST-FAMILY');

        $parent1 = new ResourceReference();
        $parent1->setResource('P-1');
        $familyView->setParent1($parent1);

        // Encode to JSON
        $json = json_encode($familyView->toArray());
        $this->assertNotFalse($json);
        $this->assertStringContainsString('"id":"TEST-FAMILY"', $json);
        $this->assertStringContainsString('"parent1"', $json);
    }

    public function testFamilyViewJsonDecoding()
    {
        // Create JSON string
        $json = '{
            "id": "FAMILY-SMITH",
            "parent1": {
                "resource": "https://familysearch.org/persons/JOHN-SMITH"
            },
            "parent2": {
                "resource": "https://familysearch.org/persons/MARY-JONES"
            },
            "children": [
                {"resource": "https://familysearch.org/persons/JAMES-SMITH"},
                {"resource": "https://familysearch.org/persons/SARAH-SMITH"}
            ]
        }';

        $array = json_decode($json, true);
        $familyView = new FamilyView($array);

        $this->assertEquals('FAMILY-SMITH', $familyView->getId());
        $this->assertNotNull($familyView->getParent1());
        $this->assertNotNull($familyView->getParent2());
        $this->assertCount(2, $familyView->getChildren());
        $this->assertEquals('https://familysearch.org/persons/JOHN-SMITH', $familyView->getParent1()->getResource());
        $this->assertEquals('https://familysearch.org/persons/JAMES-SMITH', $familyView->getChildren()[0]->getResource());
    }

    // ==================== XML Serialization Tests ====================

    public function testDateInfoXmlSerialization()
    {
        // Create DateInfo
        $date = new DateInfo();
        $date->setOriginal('1752-01-10');
        $date->setFormal('+1752-01-10');
        $date->setConfidence(ConfidenceLevel::HIGH);
        $date->setCalendar(CalendarType::GREGORIAN);

        // Create XMLWriter
        $writer = new \XMLWriter();
        $writer->openMemory();
        $writer->startDocument('1.0', 'UTF-8');
        $writer->startElementNs('gx', 'date', 'http://gedcomx.org/v1/');

        // Write XML
        $date->writeXmlContents($writer);

        $writer->endElement();
        $writer->endDocument();
        $xml = $writer->outputMemory();

        // Verify XML contains new elements
        $this->assertStringContainsString('<gx:original>1752-01-10</gx:original>', $xml);
        $this->assertStringContainsString('<gx:formal>+1752-01-10</gx:formal>', $xml);
        $this->assertStringContainsString('<gx:confidence>http://gedcomx.org/High</gx:confidence>', $xml);
        $this->assertStringContainsString('<gx:calendar>http://gedcomx.org/Gregorian</gx:calendar>', $xml);
    }

    public function testDateInfoXmlWithAlternateCalendars()
    {
        // Create DateInfo with alternate calendars
        $date = new DateInfo();
        $date->setOriginal('1752-01-10');
        $date->setCalendar(CalendarType::GREGORIAN);

        $julianDate = new DateInfo();
        $julianDate->setOriginal('1751-12-30');
        $julianDate->setCalendar(CalendarType::JULIAN);

        $date->setAlternateCalendarDates([$julianDate]);

        // Create XMLWriter
        $writer = new \XMLWriter();
        $writer->openMemory();
        $writer->startDocument('1.0', 'UTF-8');
        $writer->startElementNs('gx', 'date', 'http://gedcomx.org/v1/');

        // Write XML
        $date->writeXmlContents($writer);

        $writer->endElement();
        $writer->endDocument();
        $xml = $writer->outputMemory();

        // Verify nested alternateCalendarDate element
        $this->assertStringContainsString('<gx:alternateCalendarDate>', $xml);
        $this->assertStringContainsString('<gx:original>1751-12-30</gx:original>', $xml);
        $this->assertStringContainsString('<gx:calendar>http://gedcomx.org/Julian</gx:calendar>', $xml);
    }

    public function testDateInfoXmlDeserialization()
    {
        // Create XML
        $xml = '<?xml version="1.0" encoding="UTF-8"?>
        <gx:date xmlns:gx="http://gedcomx.org/v1/">
            <gx:original>1800-05-15</gx:original>
            <gx:formal>+1800-05-15</gx:formal>
            <gx:confidence>http://gedcomx.org/Medium</gx:confidence>
            <gx:calendar>http://gedcomx.org/Julian</gx:calendar>
        </gx:date>';

        // Parse XML
        $reader = new \XMLReader();
        $reader->XML($xml);
        $reader->read();

        $date = new DateInfo($reader);

        // Verify properties
        $this->assertEquals('1800-05-15', $date->getOriginal());
        $this->assertEquals('+1800-05-15', $date->getFormal());
        $this->assertEquals(ConfidenceLevel::MEDIUM, $date->getConfidence());
        $this->assertEquals(CalendarType::JULIAN, $date->getCalendar());
    }

    public function testFamilyViewXmlSerialization()
    {
        // Create FamilyView
        $familyView = new FamilyView();
        $familyView->setId('FAMILY-1');

        $parent1 = new ResourceReference();
        $parent1->setResource('P-1');
        $familyView->setParent1($parent1);

        $child = new ResourceReference();
        $child->setResource('C-1');
        $familyView->addChild($child);

        // Create XMLWriter
        $writer = new \XMLWriter();
        $writer->openMemory();
        $writer->startDocument('1.0', 'UTF-8');
        $writer->startElementNs('gx', 'familyView', 'http://gedcomx.org/v1/');

        // Write XML
        $familyView->writeXmlContents($writer);

        $writer->endElement();
        $writer->endDocument();
        $xml = $writer->outputMemory();

        // Verify XML structure - ResourceReference uses attributes, not child elements
        $this->assertStringContainsString('parent1', $xml);
        $this->assertStringContainsString('child', $xml);
        $this->assertStringContainsString('P-1', $xml);
        $this->assertStringContainsString('C-1', $xml);
    }

    // ==================== Complex Integration Tests ====================

    public function testCompletePersonWithEnhancedDateInfo()
    {
        // Create Person with enhanced birth date
        $person = new Person();
        $person->setId('PERSON-1');

        $birthFact = new Fact();
        $birthFact->setType('http://gedcomx.org/Birth');

        $birthDate = new DateInfo();
        $birthDate->setOriginal('25 December 1800');
        $birthDate->setFormal('+1800-12-25');
        $birthDate->setCalendar(CalendarType::GREGORIAN);
        $birthDate->setConfidence(ConfidenceLevel::HIGH);

        $birthFact->setDate($birthDate);
        $person->setFacts([$birthFact]);

        // Serialize to JSON
        $array = $person->toArray();
        $json = json_encode($array);

        // Deserialize from JSON
        $decodedArray = json_decode($json, true);
        $restoredPerson = new Person($decodedArray);

        // Verify complete data integrity
        $this->assertEquals('PERSON-1', $restoredPerson->getId());
        $this->assertCount(1, $restoredPerson->getFacts());
        $this->assertEquals('25 December 1800', $restoredPerson->getFacts()[0]->getDate()->getOriginal());
        $this->assertEquals(CalendarType::GREGORIAN, $restoredPerson->getFacts()[0]->getDate()->getCalendar());
        $this->assertEquals(ConfidenceLevel::HIGH, $restoredPerson->getFacts()[0]->getDate()->getConfidence());
    }

    public function testFamilyViewWithComplexStructure()
    {
        // Create complete family structure
        $familyView = new FamilyView();
        $familyView->setId('FAMILY-COMPLEX');

        // Parents
        $father = new ResourceReference();
        $father->setResource('https://familysearch.org/persons/FATHER-1');
        $father->setResourceId('FATHER-1');
        $familyView->setParent1($father);

        $mother = new ResourceReference();
        $mother->setResource('https://familysearch.org/persons/MOTHER-1');
        $mother->setResourceId('MOTHER-1');
        $familyView->setParent2($mother);

        // Children
        for ($i = 1; $i <= 5; $i++) {
            $child = new ResourceReference();
            $child->setResource("https://familysearch.org/persons/CHILD-{$i}");
            $child->setResourceId("CHILD-{$i}");
            $familyView->addChild($child);
        }

        // Serialize to JSON
        $array = $familyView->toArray();
        $json = json_encode($array);

        // Deserialize from JSON
        $decodedArray = json_decode($json, true);
        $restoredFamily = new FamilyView($decodedArray);

        // Verify complete data integrity
        $this->assertEquals('FAMILY-COMPLEX', $restoredFamily->getId());
        $this->assertEquals('https://familysearch.org/persons/FATHER-1', $restoredFamily->getParent1()->getResource());
        $this->assertEquals('https://familysearch.org/persons/MOTHER-1', $restoredFamily->getParent2()->getResource());
        $this->assertCount(5, $restoredFamily->getChildren());
        $this->assertEquals('https://familysearch.org/persons/CHILD-3', $restoredFamily->getChildren()[2]->getResource());
    }

    public function testNestedAlternateCalendarDatesJson()
    {
        // Create primary date
        $gregorianDate = new DateInfo();
        $gregorianDate->setOriginal('14 September 1752');
        $gregorianDate->setFormal('+1752-09-14');
        $gregorianDate->setCalendar(CalendarType::GREGORIAN);
        $gregorianDate->setConfidence(ConfidenceLevel::HIGH);

        // Create first alternate (Julian)
        $julianDate = new DateInfo();
        $julianDate->setOriginal('3 September 1752');
        $julianDate->setFormal('+1752-09-03');
        $julianDate->setCalendar(CalendarType::JULIAN);

        // Create second alternate (Hebrew)
        $hebrewDate = new DateInfo();
        $hebrewDate->setOriginal('15 Elul 5512');
        $hebrewDate->setCalendar(CalendarType::HEBREW);

        $gregorianDate->setAlternateCalendarDates([$julianDate, $hebrewDate]);

        // Full round-trip through JSON
        $json = json_encode($gregorianDate->toArray());
        $this->assertNotFalse($json);

        $decoded = json_decode($json, true);
        $this->assertIsArray($decoded);

        $restored = new DateInfo($decoded);

        // Verify all data preserved
        $this->assertEquals('14 September 1752', $restored->getOriginal());
        $this->assertEquals(CalendarType::GREGORIAN, $restored->getCalendar());
        $this->assertEquals(ConfidenceLevel::HIGH, $restored->getConfidence());
        $this->assertCount(2, $restored->getAlternateCalendarDates());
        $this->assertEquals(CalendarType::JULIAN, $restored->getAlternateCalendarDates()[0]->getCalendar());
        $this->assertEquals(CalendarType::HEBREW, $restored->getAlternateCalendarDates()[1]->getCalendar());
    }

    public function testEnumSerializationAsUriStrings()
    {
        // Verify enums serialize as URI strings, not PHP constants
        $date = new DateInfo();
        $date->setCalendar(CalendarType::GREGORIAN);
        $date->setConfidence(ConfidenceLevel::HIGH);

        $array = $date->toArray();

        // Should be full URI strings
        $this->assertEquals('http://gedcomx.org/Gregorian', $array['calendar']);
        $this->assertEquals('http://gedcomx.org/High', $array['confidence']);

        // Verify JSON encoding preserves URIs (slashes may be escaped as \/)
        $json = json_encode($array);
        $this->assertStringContainsString('gedcomx.org', $json);
        $this->assertStringContainsString('Gregorian', $json);
        $this->assertStringContainsString('High', $json);
    }

    public function testEmptyAndNullSerialization()
    {
        // Test that null/empty properties don't break serialization
        $date = new DateInfo();
        $date->setOriginal('1900');
        // Don't set confidence, calendar, or alternateCalendarDates

        $array = $date->toArray();
        $json = json_encode($array);

        $this->assertNotFalse($json);
        $this->assertArrayNotHasKey('confidence', $array);
        $this->assertArrayNotHasKey('calendar', $array);
        $this->assertArrayNotHasKey('alternateCalendarDates', $array);

        // Deserialize
        $restored = new DateInfo(json_decode($json, true));
        $this->assertEquals('1900', $restored->getOriginal());
        $this->assertNull($restored->getConfidence());
        $this->assertNull($restored->getCalendar());
    }

    public function testFamilyViewSingleParentSerialization()
    {
        // Test single-parent family serialization
        $familyView = new FamilyView();
        $familyView->setId('SINGLE-PARENT');

        $parent = new ResourceReference();
        $parent->setResource('P-1');
        $familyView->setParent1($parent);

        $child = new ResourceReference();
        $child->setResource('C-1');
        $familyView->addChild($child);

        // Don't set parent2

        $array = $familyView->toArray();
        $json = json_encode($array);

        $this->assertArrayHasKey('parent1', $array);
        $this->assertArrayNotHasKey('parent2', $array);

        // Round-trip
        $restored = new FamilyView(json_decode($json, true));
        $this->assertNotNull($restored->getParent1());
        $this->assertNull($restored->getParent2());
        $this->assertCount(1, $restored->getChildren());
    }
}
