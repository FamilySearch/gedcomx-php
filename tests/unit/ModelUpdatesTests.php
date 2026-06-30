<?php

namespace Gedcomx\Tests\Unit;

use Gedcomx\Conclusion\DateInfo;
use Gedcomx\Conclusion\FamilyView;
use Gedcomx\Conclusion\Fact;
use Gedcomx\Conclusion\Event;
use Gedcomx\Conclusion\PlaceReference;
use Gedcomx\Common\ResourceReference;
use Gedcomx\Types\CalendarType;
use Gedcomx\Types\ConfidenceLevel;
use Gedcomx\Tests\ApiTestCase;

/**
 * Comprehensive tests for GEDCOM X model updates (SDK 4.3.0)
 * Tests CalendarType enum, DateInfo updates, and FamilyView class
 */
class ModelUpdatesTests extends ApiTestCase
{
    // ==================== CalendarType Tests ====================

    public function testCalendarTypeGregorian()
    {
        $this->assertEquals('http://gedcomx.org/Gregorian', CalendarType::GREGORIAN);
    }

    public function testCalendarTypeJulian()
    {
        $this->assertEquals('http://gedcomx.org/Julian', CalendarType::JULIAN);
    }

    public function testCalendarTypeHebrew()
    {
        $this->assertEquals('http://gedcomx.org/Hebrew', CalendarType::HEBREW);
    }

    public function testCalendarTypeFrenchRepublican()
    {
        $this->assertEquals('http://gedcomx.org/FrenchRepublican', CalendarType::FRENCH_REPUBLICAN);
    }

    public function testCalendarTypeHijri()
    {
        $this->assertEquals('http://gedcomx.org/Hijri', CalendarType::HIJRI);
    }

    // ==================== Updated DateInfo Tests ====================

    public function testDateInfoWithConfidence()
    {
        $date = new DateInfo();
        $date->setConfidence(ConfidenceLevel::HIGH);

        $this->assertEquals(ConfidenceLevel::HIGH, $date->getConfidence());
        $this->assertEquals('http://gedcomx.org/High', $date->getConfidence());
    }

    public function testDateInfoWithCalendar()
    {
        $date = new DateInfo();
        $date->setCalendar(CalendarType::GREGORIAN);

        $this->assertEquals(CalendarType::GREGORIAN, $date->getCalendar());
        $this->assertEquals('http://gedcomx.org/Gregorian', $date->getCalendar());
    }

    public function testDateInfoWithAlternateCalendarDates()
    {
        // Create primary date (Gregorian)
        $gregorianDate = new DateInfo();
        $gregorianDate->setOriginal('10 January 1752');
        $gregorianDate->setFormal('+1752-01-10');
        $gregorianDate->setCalendar(CalendarType::GREGORIAN);

        // Create alternate date (Julian)
        $julianDate = new DateInfo();
        $julianDate->setOriginal('30 December 1751');
        $julianDate->setFormal('+1751-12-30');
        $julianDate->setCalendar(CalendarType::JULIAN);

        // Set alternate dates
        $gregorianDate->setAlternateCalendarDates([$julianDate]);

        $this->assertCount(1, $gregorianDate->getAlternateCalendarDates());
        $this->assertEquals('30 December 1751', $gregorianDate->getAlternateCalendarDates()[0]->getOriginal());
        $this->assertEquals(CalendarType::JULIAN, $gregorianDate->getAlternateCalendarDates()[0]->getCalendar());
    }

    public function testDateInfoWithMultipleAlternateCalendars()
    {
        $primaryDate = new DateInfo();
        $primaryDate->setOriginal('1 January 2000');
        $primaryDate->setCalendar(CalendarType::GREGORIAN);

        $julianDate = new DateInfo();
        $julianDate->setOriginal('19 December 1999');
        $julianDate->setCalendar(CalendarType::JULIAN);

        $hebrewDate = new DateInfo();
        $hebrewDate->setOriginal('23 Tevet 5760');
        $hebrewDate->setCalendar(CalendarType::HEBREW);

        $primaryDate->setAlternateCalendarDates([$julianDate, $hebrewDate]);

        $this->assertCount(2, $primaryDate->getAlternateCalendarDates());
    }

    public function testDateInfoConstructionWithNewProperties()
    {
        $date = new DateInfo([
            'original' => '1900',
            'formal' => '+1900',
            'confidence' => ConfidenceLevel::MEDIUM,
            'calendar' => CalendarType::GREGORIAN
        ]);

        $this->assertEquals('1900', $date->getOriginal());
        $this->assertEquals('+1900', $date->getFormal());
        $this->assertEquals(ConfidenceLevel::MEDIUM, $date->getConfidence());
        $this->assertEquals(CalendarType::GREGORIAN, $date->getCalendar());
    }

    public function testDateInfoSerializationWithNewProperties()
    {
        $date = new DateInfo();
        $date->setOriginal('1 Jan 1800');
        $date->setFormal('+1800-01-01');
        $date->setConfidence(ConfidenceLevel::HIGH);
        $date->setCalendar(CalendarType::GREGORIAN);

        $array = $date->toArray();

        $this->assertArrayHasKey('original', $array);
        $this->assertArrayHasKey('formal', $array);
        $this->assertArrayHasKey('confidence', $array);
        $this->assertArrayHasKey('calendar', $array);
        $this->assertEquals(ConfidenceLevel::HIGH, $array['confidence']);
        $this->assertEquals(CalendarType::GREGORIAN, $array['calendar']);
    }

    public function testDateInfoSerializationWithAlternateCalendars()
    {
        $primaryDate = new DateInfo();
        $primaryDate->setOriginal('1752-01-10');
        $primaryDate->setCalendar(CalendarType::GREGORIAN);

        $julianDate = new DateInfo();
        $julianDate->setOriginal('1751-12-30');
        $julianDate->setCalendar(CalendarType::JULIAN);

        $primaryDate->setAlternateCalendarDates([$julianDate]);

        $array = $primaryDate->toArray();

        $this->assertArrayHasKey('alternateCalendarDates', $array);
        $this->assertCount(1, $array['alternateCalendarDates']);
        $this->assertEquals('1751-12-30', $array['alternateCalendarDates'][0]['original']);
    }

    public function testDateInfoDeserializationWithNewProperties()
    {
        $data = [
            'original' => 'January 1900',
            'formal' => '+1900-01',
            'confidence' => ConfidenceLevel::LOW,
            'calendar' => CalendarType::JULIAN
        ];

        $date = new DateInfo($data);

        $this->assertEquals('January 1900', $date->getOriginal());
        $this->assertEquals('+1900-01', $date->getFormal());
        $this->assertEquals(ConfidenceLevel::LOW, $date->getConfidence());
        $this->assertEquals(CalendarType::JULIAN, $date->getCalendar());
    }

    public function testDateInfoDeserializationWithAlternateCalendars()
    {
        $data = [
            'original' => '1752-01-10',
            'calendar' => CalendarType::GREGORIAN,
            'alternateCalendarDates' => [
                [
                    'original' => '1751-12-30',
                    'calendar' => CalendarType::JULIAN
                ]
            ]
        ];

        $date = new DateInfo($data);

        $this->assertEquals('1752-01-10', $date->getOriginal());
        $this->assertCount(1, $date->getAlternateCalendarDates());
        $this->assertInstanceOf(DateInfo::class, $date->getAlternateCalendarDates()[0]);
        $this->assertEquals('1751-12-30', $date->getAlternateCalendarDates()[0]->getOriginal());
    }

    public function testDateInfoNullHandling()
    {
        $date = new DateInfo();

        $this->assertNull($date->getConfidence());
        $this->assertNull($date->getCalendar());
        $this->assertNull($date->getAlternateCalendarDates());
    }

    // ==================== FamilyView Tests ====================

    public function testFamilyViewConstruction()
    {
        $familyView = new FamilyView();

        $this->assertInstanceOf(FamilyView::class, $familyView);
    }

    public function testFamilyViewWithParent1()
    {
        $familyView = new FamilyView();

        $parent1 = new ResourceReference();
        $parent1->setResource('https://familysearch.org/platform/persons/P-1');

        $familyView->setParent1($parent1);

        $this->assertNotNull($familyView->getParent1());
        $this->assertEquals('https://familysearch.org/platform/persons/P-1', $familyView->getParent1()->getResource());
    }

    public function testFamilyViewWithParent2()
    {
        $familyView = new FamilyView();

        $parent2 = new ResourceReference();
        $parent2->setResource('https://familysearch.org/platform/persons/P-2');

        $familyView->setParent2($parent2);

        $this->assertNotNull($familyView->getParent2());
        $this->assertEquals('https://familysearch.org/platform/persons/P-2', $familyView->getParent2()->getResource());
    }

    public function testFamilyViewWithChildren()
    {
        $familyView = new FamilyView();

        $child1 = new ResourceReference();
        $child1->setResource('https://familysearch.org/platform/persons/C-1');

        $child2 = new ResourceReference();
        $child2->setResource('https://familysearch.org/platform/persons/C-2');

        $familyView->setChildren([$child1, $child2]);

        $this->assertCount(2, $familyView->getChildren());
        $this->assertEquals('https://familysearch.org/platform/persons/C-1', $familyView->getChildren()[0]->getResource());
        $this->assertEquals('https://familysearch.org/platform/persons/C-2', $familyView->getChildren()[1]->getResource());
    }

    public function testFamilyViewAddChild()
    {
        $familyView = new FamilyView();

        $child1 = new ResourceReference();
        $child1->setResource('https://familysearch.org/platform/persons/C-1');

        $child2 = new ResourceReference();
        $child2->setResource('https://familysearch.org/platform/persons/C-2');

        $familyView->addChild($child1);
        $familyView->addChild($child2);

        $this->assertCount(2, $familyView->getChildren());
    }

    public function testFamilyViewCompleteFamilyUnit()
    {
        $familyView = new FamilyView();

        $parent1 = new ResourceReference();
        $parent1->setResource('https://familysearch.org/platform/persons/P-1');

        $parent2 = new ResourceReference();
        $parent2->setResource('https://familysearch.org/platform/persons/P-2');

        $child1 = new ResourceReference();
        $child1->setResource('https://familysearch.org/platform/persons/C-1');

        $child2 = new ResourceReference();
        $child2->setResource('https://familysearch.org/platform/persons/C-2');

        $child3 = new ResourceReference();
        $child3->setResource('https://familysearch.org/platform/persons/C-3');

        $familyView->setParent1($parent1);
        $familyView->setParent2($parent2);
        $familyView->setChildren([$child1, $child2, $child3]);

        $this->assertNotNull($familyView->getParent1());
        $this->assertNotNull($familyView->getParent2());
        $this->assertCount(3, $familyView->getChildren());
    }

    public function testFamilyViewConstructionWithArray()
    {
        $data = [
            'parent1' => ['resource' => 'https://familysearch.org/platform/persons/P-1'],
            'parent2' => ['resource' => 'https://familysearch.org/platform/persons/P-2'],
            'children' => [
                ['resource' => 'https://familysearch.org/platform/persons/C-1'],
                ['resource' => 'https://familysearch.org/platform/persons/C-2']
            ]
        ];

        $familyView = new FamilyView($data);

        $this->assertNotNull($familyView->getParent1());
        $this->assertNotNull($familyView->getParent2());
        $this->assertCount(2, $familyView->getChildren());
        $this->assertEquals('https://familysearch.org/platform/persons/P-1', $familyView->getParent1()->getResource());
    }

    public function testFamilyViewSerialization()
    {
        $familyView = new FamilyView();

        $parent1 = new ResourceReference();
        $parent1->setResource('https://familysearch.org/platform/persons/P-1');
        $familyView->setParent1($parent1);

        $parent2 = new ResourceReference();
        $parent2->setResource('https://familysearch.org/platform/persons/P-2');
        $familyView->setParent2($parent2);

        $child = new ResourceReference();
        $child->setResource('https://familysearch.org/platform/persons/C-1');
        $familyView->addChild($child);

        $array = $familyView->toArray();

        $this->assertArrayHasKey('parent1', $array);
        $this->assertArrayHasKey('parent2', $array);
        $this->assertArrayHasKey('children', $array);
        $this->assertCount(1, $array['children']);
    }

    public function testFamilyViewDeserialization()
    {
        $data = [
            'id' => 'FV-1',
            'parent1' => ['resource' => 'https://familysearch.org/platform/persons/P-1'],
            'parent2' => ['resource' => 'https://familysearch.org/platform/persons/P-2'],
            'children' => [
                ['resource' => 'https://familysearch.org/platform/persons/C-1']
            ]
        ];

        $familyView = new FamilyView($data);

        $this->assertEquals('FV-1', $familyView->getId());
        $this->assertInstanceOf(ResourceReference::class, $familyView->getParent1());
        $this->assertInstanceOf(ResourceReference::class, $familyView->getParent2());
        $this->assertCount(1, $familyView->getChildren());
    }

    public function testFamilyViewWithSingleParent()
    {
        $familyView = new FamilyView();

        $parent1 = new ResourceReference();
        $parent1->setResource('https://familysearch.org/platform/persons/P-1');

        $child = new ResourceReference();
        $child->setResource('https://familysearch.org/platform/persons/C-1');

        $familyView->setParent1($parent1);
        $familyView->addChild($child);

        $this->assertNotNull($familyView->getParent1());
        $this->assertNull($familyView->getParent2());
        $this->assertCount(1, $familyView->getChildren());
    }

    public function testFamilyViewNullHandling()
    {
        $familyView = new FamilyView();

        $this->assertNull($familyView->getParent1());
        $this->assertNull($familyView->getParent2());
        $this->assertNull($familyView->getChildren());
    }

    public function testFamilyViewEmptyChildren()
    {
        $familyView = new FamilyView();

        $parent1 = new ResourceReference();
        $parent1->setResource('https://familysearch.org/platform/persons/P-1');

        $familyView->setParent1($parent1);
        $familyView->setChildren([]);

        $this->assertIsArray($familyView->getChildren());
        $this->assertCount(0, $familyView->getChildren());
    }

    // ==================== HasDateAndPlace Interface Tests ====================

    public function testFactImplementsDateAndPlace()
    {
        $fact = new Fact();

        $date = new DateInfo();
        $date->setOriginal('1900');
        $fact->setDate($date);

        $place = new PlaceReference();
        $place->setOriginal('London');
        $fact->setPlace($place);

        $this->assertNotNull($fact->getDate());
        $this->assertNotNull($fact->getPlace());
        $this->assertEquals('1900', $fact->getDate()->getOriginal());
        $this->assertEquals('London', $fact->getPlace()->getOriginal());
    }

    public function testEventImplementsDateAndPlace()
    {
        $event = new Event();

        $date = new DateInfo();
        $date->setOriginal('15 March 1850');
        $event->setDate($date);

        $place = new PlaceReference();
        $place->setOriginal('Boston, Massachusetts');
        $event->setPlace($place);

        $this->assertNotNull($event->getDate());
        $this->assertNotNull($event->getPlace());
        $this->assertEquals('15 March 1850', $event->getDate()->getOriginal());
        $this->assertEquals('Boston, Massachusetts', $event->getPlace()->getOriginal());
    }

    public function testFactWithEnhancedDateInfo()
    {
        $fact = new Fact();

        $date = new DateInfo();
        $date->setOriginal('10 January 1752');
        $date->setFormal('+1752-01-10');
        $date->setCalendar(CalendarType::GREGORIAN);
        $date->setConfidence(ConfidenceLevel::HIGH);

        $fact->setDate($date);

        $this->assertEquals(CalendarType::GREGORIAN, $fact->getDate()->getCalendar());
        $this->assertEquals(ConfidenceLevel::HIGH, $fact->getDate()->getConfidence());
    }

    public function testEventWithEnhancedDateInfo()
    {
        $event = new Event();

        $date = new DateInfo();
        $date->setOriginal('1793-09-22');
        $date->setCalendar(CalendarType::FRENCH_REPUBLICAN);
        $date->setConfidence(ConfidenceLevel::MEDIUM);

        $event->setDate($date);

        $this->assertEquals(CalendarType::FRENCH_REPUBLICAN, $event->getDate()->getCalendar());
        $this->assertEquals(ConfidenceLevel::MEDIUM, $event->getDate()->getConfidence());
    }

    // ==================== Integration Tests ====================

    public function testCompleteGenealogyScenario()
    {
        // Create a family view with enhanced date info
        $familyView = new FamilyView();
        $familyView->setId('FV-Smith-Family');

        // Parents
        $father = new ResourceReference();
        $father->setResource('https://familysearch.org/platform/persons/JOHN-SMITH-1800');
        $familyView->setParent1($father);

        $mother = new ResourceReference();
        $mother->setResource('https://familysearch.org/platform/persons/MARY-JONES-1805');
        $familyView->setParent2($mother);

        // Children
        $child1 = new ResourceReference();
        $child1->setResource('https://familysearch.org/platform/persons/JAMES-SMITH-1825');
        $familyView->addChild($child1);

        $child2 = new ResourceReference();
        $child2->setResource('https://familysearch.org/platform/persons/SARAH-SMITH-1827');
        $familyView->addChild($child2);

        // Verify structure
        $this->assertEquals('FV-Smith-Family', $familyView->getId());
        $this->assertCount(2, $familyView->getChildren());

        // Create a birth fact with enhanced date
        $birthDate = new DateInfo();
        $birthDate->setOriginal('25 December 1800');
        $birthDate->setFormal('+1800-12-25');
        $birthDate->setCalendar(CalendarType::GREGORIAN);
        $birthDate->setConfidence(ConfidenceLevel::HIGH);

        $birthPlace = new PlaceReference();
        $birthPlace->setOriginal('Manchester, England');

        $birthFact = new Fact();
        $birthFact->setType('http://gedcomx.org/Birth');
        $birthFact->setDate($birthDate);
        $birthFact->setPlace($birthPlace);

        // Verify fact
        $this->assertEquals('25 December 1800', $birthFact->getDate()->getOriginal());
        $this->assertEquals('Manchester, England', $birthFact->getPlace()->getOriginal());
        $this->assertEquals(CalendarType::GREGORIAN, $birthFact->getDate()->getCalendar());
    }

    public function testRoundTripSerializationDateInfo()
    {
        // Create complex DateInfo
        $originalDate = new DateInfo();
        $originalDate->setOriginal('1752-01-10');
        $originalDate->setFormal('+1752-01-10');
        $originalDate->setCalendar(CalendarType::GREGORIAN);
        $originalDate->setConfidence(ConfidenceLevel::HIGH);

        $julianDate = new DateInfo();
        $julianDate->setOriginal('1751-12-30');
        $julianDate->setCalendar(CalendarType::JULIAN);

        $originalDate->setAlternateCalendarDates([$julianDate]);

        // Serialize
        $array = $originalDate->toArray();

        // Deserialize
        $restoredDate = new DateInfo($array);

        // Verify
        $this->assertEquals($originalDate->getOriginal(), $restoredDate->getOriginal());
        $this->assertEquals($originalDate->getFormal(), $restoredDate->getFormal());
        $this->assertEquals($originalDate->getCalendar(), $restoredDate->getCalendar());
        $this->assertEquals($originalDate->getConfidence(), $restoredDate->getConfidence());
        $this->assertCount(1, $restoredDate->getAlternateCalendarDates());
    }

    public function testRoundTripSerializationFamilyView()
    {
        // Create FamilyView
        $originalFamily = new FamilyView();
        $originalFamily->setId('TEST-FAMILY');

        $parent1 = new ResourceReference();
        $parent1->setResource('P-1');
        $originalFamily->setParent1($parent1);

        $parent2 = new ResourceReference();
        $parent2->setResource('P-2');
        $originalFamily->setParent2($parent2);

        $child = new ResourceReference();
        $child->setResource('C-1');
        $originalFamily->addChild($child);

        // Serialize
        $array = $originalFamily->toArray();

        // Deserialize
        $restoredFamily = new FamilyView($array);

        // Verify
        $this->assertEquals($originalFamily->getId(), $restoredFamily->getId());
        $this->assertEquals($originalFamily->getParent1()->getResource(), $restoredFamily->getParent1()->getResource());
        $this->assertEquals($originalFamily->getParent2()->getResource(), $restoredFamily->getParent2()->getResource());
        $this->assertCount(1, $restoredFamily->getChildren());
    }
}
