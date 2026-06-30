<?php

namespace Gedcomx\Tests\Unit;

use Gedcomx\Conclusion\FamilyView;
use Gedcomx\Conclusion\Person;
use Gedcomx\Conclusion\Fact;
use Gedcomx\Conclusion\Event;
use Gedcomx\Conclusion\DateInfo;
use Gedcomx\Conclusion\PlaceReference;
use Gedcomx\Conclusion\Gender;
use Gedcomx\Conclusion\Name;
use Gedcomx\Conclusion\NameForm;
use Gedcomx\Conclusion\NamePart;
use Gedcomx\Common\ResourceReference;
use Gedcomx\Types\CalendarType;
use Gedcomx\Types\ConfidenceLevel;
use Gedcomx\Types\GenderType;
use Gedcomx\Gedcomx;
use Gedcomx\Tests\ApiTestCase;

/**
 * Comprehensive integration tests to validate GEDCOM X specification compliance
 * and ensure all v4.3.0 features work together as a cohesive system.
 */
class SpecificationComplianceTests extends ApiTestCase
{
    /**
     * Comprehensive integration test: Family with enhanced dates across multiple calendars
     *
     * This test validates:
     * - FamilyView with 2 parents and 3 children
     * - DateInfo with confidence levels
     * - Multiple CalendarType systems (Gregorian, Julian, Hebrew)
     * - Alternate calendar dates
     * - Complete serialization/deserialization cycle
     * - Integration with Person, Fact, and Event classes
     */
    public function testCompleteGenealogyWithMultiCalendarSupport()
    {
        // ==================== Create Father ====================
        $father = new Person();
        $father->setId('JOHN-SMITH-1820');

        $fatherGender = new Gender();
        $fatherGender->setType(GenderType::MALE);
        $father->setGender($fatherGender);

        // Father's name
        $fatherNamePart = new NamePart();
        $fatherNamePart->setValue('John Smith');
        $fatherNamePart->setType('http://gedcomx.org/Given');

        $fatherNameForm = new NameForm();
        $fatherNameForm->setFullText('John Smith');
        $fatherNameForm->setParts([$fatherNamePart]);

        $fatherName = new Name();
        $fatherName->setNameForms([$fatherNameForm]);
        $father->setNames([$fatherName]);

        // Father's birth with calendar conversion (Julian to Gregorian)
        $fatherBirth = new Fact();
        $fatherBirth->setType('http://gedcomx.org/Birth');

        // Primary date in Gregorian
        $fatherBirthDateGregorian = new DateInfo();
        $fatherBirthDateGregorian->setOriginal('10 January 1820');
        $fatherBirthDateGregorian->setFormal('+1820-01-10');
        $fatherBirthDateGregorian->setCalendar(CalendarType::GREGORIAN);
        $fatherBirthDateGregorian->setConfidence(ConfidenceLevel::HIGH);

        // Alternate in Julian (historical context)
        $fatherBirthDateJulian = new DateInfo();
        $fatherBirthDateJulian->setOriginal('29 December 1819');
        $fatherBirthDateJulian->setFormal('+1819-12-29');
        $fatherBirthDateJulian->setCalendar(CalendarType::JULIAN);

        $fatherBirthDateGregorian->setAlternateCalendarDates([$fatherBirthDateJulian]);

        $fatherBirth->setDate($fatherBirthDateGregorian);

        $fatherBirthPlace = new PlaceReference();
        $fatherBirthPlace->setOriginal('Manchester, England');
        $fatherBirth->setPlace($fatherBirthPlace);

        $father->setFacts([$fatherBirth]);

        // ==================== Create Mother ====================
        $mother = new Person();
        $mother->setId('SARAH-JONES-1825');

        $motherGender = new Gender();
        $motherGender->setType(GenderType::FEMALE);
        $mother->setGender($motherGender);

        // Mother's name
        $motherNamePart = new NamePart();
        $motherNamePart->setValue('Sarah Jones');
        $motherNamePart->setType('http://gedcomx.org/Given');

        $motherNameForm = new NameForm();
        $motherNameForm->setFullText('Sarah Jones');
        $motherNameForm->setParts([$motherNamePart]);

        $motherName = new Name();
        $motherName->setNameForms([$motherNameForm]);
        $mother->setNames([$motherName]);

        // Mother's birth with Hebrew calendar
        $motherBirth = new Fact();
        $motherBirth->setType('http://gedcomx.org/Birth');

        // Primary date in Gregorian
        $motherBirthDateGregorian = new DateInfo();
        $motherBirthDateGregorian->setOriginal('15 March 1825');
        $motherBirthDateGregorian->setFormal('+1825-03-15');
        $motherBirthDateGregorian->setCalendar(CalendarType::GREGORIAN);
        $motherBirthDateGregorian->setConfidence(ConfidenceLevel::MEDIUM);

        // Alternate in Hebrew
        $motherBirthDateHebrew = new DateInfo();
        $motherBirthDateHebrew->setOriginal('23 Adar 5585');
        $motherBirthDateHebrew->setCalendar(CalendarType::HEBREW);

        $motherBirthDateGregorian->setAlternateCalendarDates([$motherBirthDateHebrew]);

        $motherBirth->setDate($motherBirthDateGregorian);

        $motherBirthPlace = new PlaceReference();
        $motherBirthPlace->setOriginal('London, England');
        $motherBirth->setPlace($motherBirthPlace);

        $mother->setFacts([$motherBirth]);

        // ==================== Create Children ====================
        $children = [];

        // Child 1
        $child1 = new Person();
        $child1->setId('JAMES-SMITH-1845');

        $child1Birth = new Fact();
        $child1Birth->setType('http://gedcomx.org/Birth');

        $child1BirthDate = new DateInfo();
        $child1BirthDate->setOriginal('20 June 1845');
        $child1BirthDate->setFormal('+1845-06-20');
        $child1BirthDate->setCalendar(CalendarType::GREGORIAN);
        $child1BirthDate->setConfidence(ConfidenceLevel::HIGH);

        $child1Birth->setDate($child1BirthDate);
        $child1->setFacts([$child1Birth]);
        $children[] = $child1;

        // Child 2
        $child2 = new Person();
        $child2->setId('MARY-SMITH-1847');

        $child2Birth = new Fact();
        $child2Birth->setType('http://gedcomx.org/Birth');

        $child2BirthDate = new DateInfo();
        $child2BirthDate->setOriginal('5 August 1847');
        $child2BirthDate->setFormal('+1847-08-05');
        $child2BirthDate->setCalendar(CalendarType::GREGORIAN);
        $child2BirthDate->setConfidence(ConfidenceLevel::HIGH);

        $child2Birth->setDate($child2BirthDate);
        $child2->setFacts([$child2Birth]);
        $children[] = $child2;

        // Child 3
        $child3 = new Person();
        $child3->setId('WILLIAM-SMITH-1850');

        $child3Birth = new Fact();
        $child3Birth->setType('http://gedcomx.org/Birth');

        $child3BirthDate = new DateInfo();
        $child3BirthDate->setOriginal('About 1850');
        $child3BirthDate->setFormal('+1850');
        $child3BirthDate->setCalendar(CalendarType::GREGORIAN);
        $child3BirthDate->setConfidence(ConfidenceLevel::LOW); // Estimated date

        $child3Birth->setDate($child3BirthDate);
        $child3->setFacts([$child3Birth]);
        $children[] = $child3;

        // ==================== Create FamilyView ====================
        $familyView = new FamilyView();
        $familyView->setId('SMITH-FAMILY-1820-1850');

        $fatherRef = new ResourceReference();
        $fatherRef->setResourceId($father->getId());
        $fatherRef->setResource('#' . $father->getId());
        $familyView->setParent1($fatherRef);

        $motherRef = new ResourceReference();
        $motherRef->setResourceId($mother->getId());
        $motherRef->setResource('#' . $mother->getId());
        $familyView->setParent2($motherRef);

        foreach ($children as $child) {
            $childRef = new ResourceReference();
            $childRef->setResourceId($child->getId());
            $childRef->setResource('#' . $child->getId());
            $familyView->addChild($childRef);
        }

        // ==================== Create Marriage Event ====================
        $marriageEvent = new Event();
        $marriageEvent->setId('MARRIAGE-JOHN-SARAH-1844');
        $marriageEvent->setType('http://gedcomx.org/Marriage');

        $marriageDate = new DateInfo();
        $marriageDate->setOriginal('15 May 1844');
        $marriageDate->setFormal('+1844-05-15');
        $marriageDate->setCalendar(CalendarType::GREGORIAN);
        $marriageDate->setConfidence(ConfidenceLevel::HIGH);

        $marriageEvent->setDate($marriageDate);

        $marriagePlace = new PlaceReference();
        $marriagePlace->setOriginal('St. Mary\'s Church, London');
        $marriageEvent->setPlace($marriagePlace);

        // ==================== Validate Structure ====================

        // Validate FamilyView
        $this->assertEquals('SMITH-FAMILY-1820-1850', $familyView->getId());
        $this->assertNotNull($familyView->getParent1());
        $this->assertNotNull($familyView->getParent2());
        $this->assertCount(3, $familyView->getChildren());
        $this->assertEquals('JOHN-SMITH-1820', $familyView->getParent1()->getResourceId());
        $this->assertEquals('SARAH-JONES-1825', $familyView->getParent2()->getResourceId());

        // Validate Father
        $this->assertEquals(GenderType::MALE, $father->getGender()->getType());
        $this->assertCount(1, $father->getFacts());
        $this->assertEquals('http://gedcomx.org/Birth', $father->getFacts()[0]->getType());
        $this->assertEquals(CalendarType::GREGORIAN, $father->getFacts()[0]->getDate()->getCalendar());
        $this->assertEquals(ConfidenceLevel::HIGH, $father->getFacts()[0]->getDate()->getConfidence());
        $this->assertCount(1, $father->getFacts()[0]->getDate()->getAlternateCalendarDates());
        $this->assertEquals(CalendarType::JULIAN, $father->getFacts()[0]->getDate()->getAlternateCalendarDates()[0]->getCalendar());

        // Validate Mother
        $this->assertEquals(GenderType::FEMALE, $mother->getGender()->getType());
        $this->assertCount(1, $mother->getFacts());
        $this->assertEquals(ConfidenceLevel::MEDIUM, $mother->getFacts()[0]->getDate()->getConfidence());
        $this->assertCount(1, $mother->getFacts()[0]->getDate()->getAlternateCalendarDates());
        $this->assertEquals(CalendarType::HEBREW, $mother->getFacts()[0]->getDate()->getAlternateCalendarDates()[0]->getCalendar());

        // Validate Children
        $this->assertEquals(ConfidenceLevel::HIGH, $children[0]->getFacts()[0]->getDate()->getConfidence());
        $this->assertEquals(ConfidenceLevel::HIGH, $children[1]->getFacts()[0]->getDate()->getConfidence());
        $this->assertEquals(ConfidenceLevel::LOW, $children[2]->getFacts()[0]->getDate()->getConfidence());

        // Validate Marriage Event
        $this->assertNotNull($marriageEvent->getDate());
        $this->assertNotNull($marriageEvent->getPlace());
        $this->assertEquals(CalendarType::GREGORIAN, $marriageEvent->getDate()->getCalendar());

        // ==================== Test Serialization ====================

        // Serialize FamilyView
        $familyArray = $familyView->toArray();
        $familyJson = json_encode($familyArray);
        $this->assertNotFalse($familyJson);

        // Deserialize FamilyView
        $restoredFamilyArray = json_decode($familyJson, true);
        $restoredFamily = new FamilyView($restoredFamilyArray);

        $this->assertEquals($familyView->getId(), $restoredFamily->getId());
        $this->assertEquals($familyView->getParent1()->getResourceId(), $restoredFamily->getParent1()->getResourceId());
        $this->assertEquals($familyView->getParent2()->getResourceId(), $restoredFamily->getParent2()->getResourceId());
        $this->assertCount(3, $restoredFamily->getChildren());

        // Serialize Person with enhanced DateInfo
        $fatherArray = $father->toArray();
        $fatherJson = json_encode($fatherArray);
        $this->assertNotFalse($fatherJson);

        // Deserialize Person
        $restoredFatherArray = json_decode($fatherJson, true);
        $restoredFather = new Person($restoredFatherArray);

        $this->assertEquals($father->getId(), $restoredFather->getId());
        $this->assertEquals(
            $father->getFacts()[0]->getDate()->getCalendar(),
            $restoredFather->getFacts()[0]->getDate()->getCalendar()
        );
        $this->assertEquals(
            $father->getFacts()[0]->getDate()->getConfidence(),
            $restoredFather->getFacts()[0]->getDate()->getConfidence()
        );
        $this->assertCount(
            1,
            $restoredFather->getFacts()[0]->getDate()->getAlternateCalendarDates()
        );

        // Serialize Event
        $eventArray = $marriageEvent->toArray();
        $eventJson = json_encode($eventArray);
        $this->assertNotFalse($eventJson);

        // Deserialize Event
        $restoredEventArray = json_decode($eventJson, true);
        $restoredEvent = new Event($restoredEventArray);

        $this->assertEquals($marriageEvent->getId(), $restoredEvent->getId());
        $this->assertEquals($marriageEvent->getDate()->getCalendar(), $restoredEvent->getDate()->getCalendar());

        // ==================== Test HasDateAndPlace Interface ====================

        // Verify Fact implements HasDateAndPlace
        $this->assertNotNull($fatherBirth->getDate());
        $this->assertNotNull($fatherBirth->getPlace());
        $this->assertInstanceOf(DateInfo::class, $fatherBirth->getDate());
        $this->assertInstanceOf(PlaceReference::class, $fatherBirth->getPlace());

        // Verify Event implements HasDateAndPlace
        $this->assertNotNull($marriageEvent->getDate());
        $this->assertNotNull($marriageEvent->getPlace());
        $this->assertInstanceOf(DateInfo::class, $marriageEvent->getDate());
        $this->assertInstanceOf(PlaceReference::class, $marriageEvent->getPlace());
    }

    /**
     * Test all CalendarType constants are valid URIs
     */
    public function testCalendarTypeConstants()
    {
        $calendars = [
            CalendarType::GREGORIAN,
            CalendarType::JULIAN,
            CalendarType::HEBREW,
            CalendarType::HIJRI,
            CalendarType::FRENCH_REPUBLICAN
        ];

        foreach ($calendars as $calendar) {
            $this->assertStringStartsWith('http://gedcomx.org/', $calendar);
            $this->assertNotEmpty($calendar);
        }
    }

    /**
     * Test all ConfidenceLevel constants are valid URIs
     */
    public function testConfidenceLevelConstants()
    {
        $levels = [
            ConfidenceLevel::HIGH,
            ConfidenceLevel::MEDIUM,
            ConfidenceLevel::LOW
        ];

        foreach ($levels as $level) {
            $this->assertStringStartsWith('http://gedcomx.org/', $level);
            $this->assertNotEmpty($level);
        }
    }

    /**
     * Test backward compatibility - existing code still works
     */
    public function testBackwardCompatibility()
    {
        // Old-style DateInfo without new properties
        $oldDate = new DateInfo();
        $oldDate->setOriginal('1900');
        $oldDate->setFormal('+1900');

        // Should work without setting calendar or confidence
        $this->assertEquals('1900', $oldDate->getOriginal());
        $this->assertEquals('+1900', $oldDate->getFormal());
        $this->assertNull($oldDate->getCalendar());
        $this->assertNull($oldDate->getConfidence());
        $this->assertNull($oldDate->getAlternateCalendarDates());

        // Serialization should not include null properties
        $array = $oldDate->toArray();
        $this->assertArrayNotHasKey('calendar', $array);
        $this->assertArrayNotHasKey('confidence', $array);
        $this->assertArrayNotHasKey('alternateCalendarDates', $array);
    }

    /**
     * Test XML serialization for all new features
     */
    public function testXmlSerialization()
    {
        // Create DateInfo with all new properties
        $date = new DateInfo();
        $date->setOriginal('1 January 2000');
        $date->setFormal('+2000-01-01');
        $date->setCalendar(CalendarType::GREGORIAN);
        $date->setConfidence(ConfidenceLevel::HIGH);

        $altDate = new DateInfo();
        $altDate->setOriginal('18 Tevet 5760');
        $altDate->setCalendar(CalendarType::HEBREW);

        $date->setAlternateCalendarDates([$altDate]);

        // Serialize to XML
        $writer = new \XMLWriter();
        $writer->openMemory();
        $writer->startDocument('1.0', 'UTF-8');
        $writer->startElementNs('gx', 'date', 'http://gedcomx.org/v1/');
        $date->writeXmlContents($writer);
        $writer->endElement();
        $writer->endDocument();
        $xml = $writer->outputMemory();

        // Verify XML contains all elements
        $this->assertStringContainsString('<gx:original>1 January 2000</gx:original>', $xml);
        $this->assertStringContainsString('<gx:calendar>http://gedcomx.org/Gregorian</gx:calendar>', $xml);
        $this->assertStringContainsString('<gx:confidence>http://gedcomx.org/High</gx:confidence>', $xml);
        $this->assertStringContainsString('<gx:alternateCalendarDate>', $xml);

        // Deserialize from XML
        $reader = new \XMLReader();
        $reader->XML($xml);
        $reader->read();
        $restoredDate = new DateInfo($reader);

        $this->assertEquals('1 January 2000', $restoredDate->getOriginal());
        $this->assertEquals(CalendarType::GREGORIAN, $restoredDate->getCalendar());
        $this->assertEquals(ConfidenceLevel::HIGH, $restoredDate->getConfidence());
        $this->assertCount(1, $restoredDate->getAlternateCalendarDates());
    }

    /**
     * Test that all new classes work within Gedcomx container
     */
    public function testGedcomxContainerIntegration()
    {
        $gx = new Gedcomx();

        // Add persons with enhanced dates
        $person = new Person();
        $person->setId('P-1');

        $birthFact = new Fact();
        $birthFact->setType('http://gedcomx.org/Birth');

        $birthDate = new DateInfo();
        $birthDate->setOriginal('1900');
        $birthDate->setCalendar(CalendarType::GREGORIAN);
        $birthDate->setConfidence(ConfidenceLevel::HIGH);

        $birthFact->setDate($birthDate);
        $person->setFacts([$birthFact]);

        $gx->setPersons([$person]);

        // Verify container serialization
        $array = $gx->toArray();
        $this->assertArrayHasKey('persons', $array);
        $this->assertCount(1, $array['persons']);
        $this->assertEquals('P-1', $array['persons'][0]['id']);
        $this->assertEquals(CalendarType::GREGORIAN, $array['persons'][0]['facts'][0]['date']['calendar']);
    }

    /**
     * Test edge cases and boundary conditions
     */
    public function testEdgeCases()
    {
        // Empty FamilyView
        $emptyFamily = new FamilyView();
        $this->assertNull($emptyFamily->getParent1());
        $this->assertNull($emptyFamily->getParent2());
        $this->assertNull($emptyFamily->getChildren());

        // FamilyView with only parent1
        $singleParentFamily = new FamilyView();
        $parent = new ResourceReference();
        $parent->setResource('P-1');
        $singleParentFamily->setParent1($parent);
        $this->assertNotNull($singleParentFamily->getParent1());
        $this->assertNull($singleParentFamily->getParent2());

        // DateInfo with only calendar (no confidence or alternates)
        $date = new DateInfo();
        $date->setOriginal('1900');
        $date->setCalendar(CalendarType::GREGORIAN);
        $this->assertEquals(CalendarType::GREGORIAN, $date->getCalendar());
        $this->assertNull($date->getConfidence());
        $this->assertNull($date->getAlternateCalendarDates());

        // Multiple alternate calendars
        $primaryDate = new DateInfo();
        $primaryDate->setOriginal('2000-01-01');
        $primaryDate->setCalendar(CalendarType::GREGORIAN);

        $julian = new DateInfo();
        $julian->setCalendar(CalendarType::JULIAN);

        $hebrew = new DateInfo();
        $hebrew->setCalendar(CalendarType::HEBREW);

        $hijri = new DateInfo();
        $hijri->setCalendar(CalendarType::HIJRI);

        $primaryDate->setAlternateCalendarDates([$julian, $hebrew, $hijri]);
        $this->assertCount(3, $primaryDate->getAlternateCalendarDates());
    }
}
