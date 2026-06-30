# GEDCOM X PHP SDK - New Features Guide (v4.3.0)

This guide covers the new features added to align with GEDCOM X Java SDK 4.3.0, including FamilyView for family groupings and enhanced calendar support.

---

## Table of Contents

1. [FamilyView Class](#familyview-class)
   - [Overview](#familyview-overview)
   - [When to Use](#when-to-use-familyview)
   - [Basic Usage](#familyview-basic-usage)
   - [Advanced Examples](#familyview-advanced-examples)
2. [Calendar Type Support](#calendar-type-support)
   - [Overview](#calendar-overview)
   - [Available Calendar Types](#available-calendar-types)
   - [Basic Usage](#calendar-basic-usage)
   - [Alternate Calendar Dates](#alternate-calendar-dates)
3. [Date Confidence Levels](#date-confidence-levels)
4. [Complete Examples](#complete-examples)
5. [Migration Guide](#migration-guide)

---

## FamilyView Class

### FamilyView Overview

The `FamilyView` class provides a convenient way to represent a family unit with parents and children for display purposes. It groups family members together without requiring explicit relationship definitions.

**Namespace**: `Gedcomx\Conclusion\FamilyView`

**Key Features**:
- Represents family groupings with up to two parents
- Supports any number of children
- Single-parent families supported
- JSON and XML serialization
- Lightweight view model (not a formal relationship)

### When to Use FamilyView

**Use FamilyView when:**
- ✅ Displaying family groups in a UI
- ✅ Creating family tree visualizations
- ✅ Organizing persons into family units
- ✅ Representing household structures
- ✅ Building pedigree charts

**Use Relationship class when:**
- ❌ Defining formal couple relationships
- ❌ Specifying parent-child relationships with facts
- ❌ Recording relationship evidence
- ❌ Capturing relationship dates and places

**Key Difference**: FamilyView is a **view/display model**, while Relationship is a **conclusion model** with evidence and sources.

### FamilyView Basic Usage

#### Creating a Simple Family

```php
<?php

use Gedcomx\Conclusion\FamilyView;
use Gedcomx\Common\ResourceReference;

// Create a family view
$family = new FamilyView();
$family->setId('SMITH-FAMILY-1850');

// Add first parent (father)
$father = new ResourceReference();
$father->setResource('https://familysearch.org/platform/persons/JOHN-SMITH-1820');
$family->setParent1($father);

// Add second parent (mother)
$mother = new ResourceReference();
$mother->setResource('https://familysearch.org/platform/persons/MARY-JONES-1825');
$family->setParent2($mother);

// Add children
$child1 = new ResourceReference();
$child1->setResource('https://familysearch.org/platform/persons/JAMES-SMITH-1845');
$family->addChild($child1);

$child2 = new ResourceReference();
$child2->setResource('https://familysearch.org/platform/persons/SARAH-SMITH-1847');
$family->addChild($child2);

$child3 = new ResourceReference();
$child3->setResource('https://familysearch.org/platform/persons/WILLIAM-SMITH-1850');
$family->addChild($child3);

// The family now has 2 parents and 3 children
echo "Family has " . count($family->getChildren()) . " children\n";
```

#### Creating a Single-Parent Family

```php
<?php

use Gedcomx\Conclusion\FamilyView;
use Gedcomx\Common\ResourceReference;

// Create single-parent family
$family = new FamilyView();
$family->setId('JONES-FAMILY-1900');

// Only one parent
$parent = new ResourceReference();
$parent->setResource('https://familysearch.org/platform/persons/ELIZABETH-JONES-1880');
$family->setParent1($parent);
// parent2 remains null

// Add children
$child = new ResourceReference();
$child->setResource('https://familysearch.org/platform/persons/ROBERT-JONES-1905');
$family->addChild($child);

// Single-parent family created
```

### FamilyView Advanced Examples

#### Example 1: Building a Family from Person Objects

```php
<?php

use Gedcomx\Conclusion\FamilyView;
use Gedcomx\Conclusion\Person;
use Gedcomx\Common\ResourceReference;

// Assume you have Person objects
$johnPerson = new Person();
$johnPerson->setId('JOHN-SMITH-1820');

$maryPerson = new Person();
$maryPerson->setId('MARY-JONES-1825');

// Create FamilyView from Person IDs
$family = new FamilyView();
$family->setId('SMITH-FAMILY');

$fatherRef = new ResourceReference();
$fatherRef->setResourceId($johnPerson->getId());
$fatherRef->setResource('#' . $johnPerson->getId()); // Local reference
$family->setParent1($fatherRef);

$motherRef = new ResourceReference();
$motherRef->setResourceId($maryPerson->getId());
$motherRef->setResource('#' . $maryPerson->getId());
$family->setParent2($motherRef);
```

#### Example 2: Serializing to JSON

```php
<?php

use Gedcomx\Conclusion\FamilyView;
use Gedcomx\Common\ResourceReference;

// Create family
$family = new FamilyView();
$family->setId('FAMILY-JSON-EXAMPLE');

$parent1 = new ResourceReference();
$parent1->setResource('P-1');
$family->setParent1($parent1);

$child = new ResourceReference();
$child->setResource('C-1');
$family->addChild($child);

// Convert to JSON
$json = json_encode($family->toArray(), JSON_PRETTY_PRINT);
echo $json;
```

**Output**:
```json
{
    "id": "FAMILY-JSON-EXAMPLE",
    "parent1": {
        "resource": "P-1"
    },
    "children": [
        {
            "resource": "C-1"
        }
    ]
}
```

#### Example 3: Deserializing from JSON

```php
<?php

use Gedcomx\Conclusion\FamilyView;

// JSON from API or storage
$json = '{
    "id": "SMITH-FAMILY",
    "parent1": {"resource": "https://familysearch.org/persons/P-1"},
    "parent2": {"resource": "https://familysearch.org/persons/P-2"},
    "children": [
        {"resource": "https://familysearch.org/persons/C-1"},
        {"resource": "https://familysearch.org/persons/C-2"}
    ]
}';

// Parse and create FamilyView
$data = json_decode($json, true);
$family = new FamilyView($data);

// Access family members
echo "Parent 1: " . $family->getParent1()->getResource() . "\n";
echo "Parent 2: " . $family->getParent2()->getResource() . "\n";
echo "Children: " . count($family->getChildren()) . "\n";
```

#### Example 4: Bulk Children Assignment

```php
<?php

use Gedcomx\Conclusion\FamilyView;
use Gedcomx\Common\ResourceReference;

// Create multiple child references
$children = [];
for ($i = 1; $i <= 5; $i++) {
    $child = new ResourceReference();
    $child->setResource("https://familysearch.org/persons/CHILD-{$i}");
    $children[] = $child;
}

// Create family and set all children at once
$family = new FamilyView();
$family->setChildren($children);

// All 5 children added
echo "Total children: " . count($family->getChildren()) . "\n";
```

---

## Calendar Type Support

### Calendar Overview

The GEDCOM X PHP SDK now supports multiple calendar systems, allowing you to represent dates in various historical and cultural calendars. This is essential for accurate genealogical research across different time periods and cultures.

**Namespace**: `Gedcomx\Types\CalendarType`

### Available Calendar Types

```php
<?php

use Gedcomx\Types\CalendarType;

// All available calendar types
CalendarType::GREGORIAN          // "http://gedcomx.org/Gregorian"
CalendarType::JULIAN             // "http://gedcomx.org/Julian"
CalendarType::HEBREW             // "http://gedcomx.org/Hebrew"
CalendarType::FRENCH_REPUBLICAN  // "http://gedcomx.org/FrenchRepublican"
CalendarType::HIJRI              // "http://gedcomx.org/Hijri"
```

| Calendar Type | Description | Example Use Cases |
|--------------|-------------|-------------------|
| **GREGORIAN** | Modern international calendar (1582+) | Most modern dates worldwide |
| **JULIAN** | Pre-Gregorian calendar (until 1582) | Historical European dates |
| **HEBREW** | Jewish religious calendar | Jewish genealogical research |
| **FRENCH_REPUBLICAN** | Revolutionary France (1793-1805) | French Revolution period |
| **HIJRI** | Islamic lunar calendar | Islamic genealogical research |

### Calendar Basic Usage

#### Setting a Calendar Type

```php
<?php

use Gedcomx\Conclusion\DateInfo;
use Gedcomx\Types\CalendarType;

// Create date with Gregorian calendar
$date = new DateInfo();
$date->setOriginal('25 December 1800');
$date->setFormal('+1800-12-25');
$date->setCalendar(CalendarType::GREGORIAN);

echo $date->getCalendar(); // "http://gedcomx.org/Gregorian"
```

#### Using Different Calendars

```php
<?php

use Gedcomx\Conclusion\DateInfo;
use Gedcomx\Types\CalendarType;

// Hebrew calendar date
$hebrewDate = new DateInfo();
$hebrewDate->setOriginal('15 Shevat 5780');
$hebrewDate->setCalendar(CalendarType::HEBREW);

// Julian calendar date (pre-Gregorian reform)
$julianDate = new DateInfo();
$julianDate->setOriginal('3 September 1752');
$julianDate->setFormal('+1752-09-03');
$julianDate->setCalendar(CalendarType::JULIAN);

// Islamic calendar date
$hijriDate = new DateInfo();
$hijriDate->setOriginal('1 Muharram 1444');
$hijriDate->setCalendar(CalendarType::HIJRI);
```

### Alternate Calendar Dates

One of the most powerful features is the ability to represent the same date in multiple calendar systems. This is crucial for historical accuracy during calendar transitions.

#### Example: Gregorian Calendar Switch (1752)

When Britain adopted the Gregorian calendar in 1752, 11 days were skipped (September 3-13, 1752).

```php
<?php

use Gedcomx\Conclusion\DateInfo;
use Gedcomx\Types\CalendarType;

// Primary date in Gregorian calendar
$gregorianDate = new DateInfo();
$gregorianDate->setOriginal('14 September 1752');
$gregorianDate->setFormal('+1752-09-14');
$gregorianDate->setCalendar(CalendarType::GREGORIAN);

// Create alternate representation in Julian calendar
$julianDate = new DateInfo();
$julianDate->setOriginal('3 September 1752');
$julianDate->setFormal('+1752-09-03');
$julianDate->setCalendar(CalendarType::JULIAN);

// Link the alternate calendar date
$gregorianDate->setAlternateCalendarDates([$julianDate]);

// Now you have both representations
echo "Gregorian: " . $gregorianDate->getOriginal() . "\n";
echo "Julian: " . $gregorianDate->getAlternateCalendarDates()[0]->getOriginal() . "\n";
```

#### Example: Multiple Alternate Calendars

```php
<?php

use Gedcomx\Conclusion\DateInfo;
use Gedcomx\Types\CalendarType;

// Primary date
$primaryDate = new DateInfo();
$primaryDate->setOriginal('1 January 2000');
$primaryDate->setFormal('+2000-01-01');
$primaryDate->setCalendar(CalendarType::GREGORIAN);

// Julian equivalent
$julianDate = new DateInfo();
$julianDate->setOriginal('19 December 1999');
$julianDate->setFormal('+1999-12-19');
$julianDate->setCalendar(CalendarType::JULIAN);

// Hebrew equivalent
$hebrewDate = new DateInfo();
$hebrewDate->setOriginal('23 Tevet 5760');
$hebrewDate->setCalendar(CalendarType::HEBREW);

// Hijri equivalent
$hijriDate = new DateInfo();
$hijriDate->setOriginal('24 Ramadan 1420');
$hijriDate->setCalendar(CalendarType::HIJRI);

// Add all alternate calendars
$primaryDate->setAlternateCalendarDates([$julianDate, $hebrewDate, $hijriDate]);

// Access alternate dates
foreach ($primaryDate->getAlternateCalendarDates() as $altDate) {
    echo $altDate->getCalendar() . ": " . $altDate->getOriginal() . "\n";
}
```

#### Example: French Republican Calendar

```php
<?php

use Gedcomx\Conclusion\DateInfo;
use Gedcomx\Types\CalendarType;

// Event during French Revolution
$frenchDate = new DateInfo();
$frenchDate->setOriginal('1 Vendémiaire An II');
$frenchDate->setCalendar(CalendarType::FRENCH_REPUBLICAN);

// Gregorian equivalent
$gregorianDate = new DateInfo();
$gregorianDate->setOriginal('22 September 1793');
$gregorianDate->setFormal('+1793-09-22');
$gregorianDate->setCalendar(CalendarType::GREGORIAN);

$frenchDate->setAlternateCalendarDates([$gregorianDate]);
```

---

## Date Confidence Levels

Along with calendar support, you can now specify confidence levels for dates.

**Namespace**: `Gedcomx\Types\ConfidenceLevel`

### Available Confidence Levels

```php
<?php

use Gedcomx\Types\ConfidenceLevel;

ConfidenceLevel::HIGH    // "http://gedcomx.org/High"
ConfidenceLevel::MEDIUM  // "http://gedcomx.org/Medium"
ConfidenceLevel::LOW     // "http://gedcomx.org/Low"
```

### Usage Example

```php
<?php

use Gedcomx\Conclusion\DateInfo;
use Gedcomx\Types\CalendarType;
use Gedcomx\Types\ConfidenceLevel;

// High confidence date (from birth certificate)
$birthDate = new DateInfo();
$birthDate->setOriginal('15 May 1850');
$birthDate->setFormal('+1850-05-15');
$birthDate->setCalendar(CalendarType::GREGORIAN);
$birthDate->setConfidence(ConfidenceLevel::HIGH);

// Low confidence date (estimated from census)
$deathDate = new DateInfo();
$deathDate->setOriginal('About 1920');
$deathDate->setFormal('+1920');
$deathDate->setCalendar(CalendarType::GREGORIAN);
$deathDate->setConfidence(ConfidenceLevel::LOW);
```

---

## Complete Examples

### Example 1: Complete Family with Enhanced Dates

```php
<?php

use Gedcomx\Conclusion\FamilyView;
use Gedcomx\Conclusion\Person;
use Gedcomx\Conclusion\Fact;
use Gedcomx\Conclusion\DateInfo;
use Gedcomx\Common\ResourceReference;
use Gedcomx\Types\CalendarType;
use Gedcomx\Types\ConfidenceLevel;

// Create father with birth date
$father = new Person();
$father->setId('JOHN-SMITH-1820');

$fatherBirth = new Fact();
$fatherBirth->setType('http://gedcomx.org/Birth');

$fatherBirthDate = new DateInfo();
$fatherBirthDate->setOriginal('10 January 1820');
$fatherBirthDate->setFormal('+1820-01-10');
$fatherBirthDate->setCalendar(CalendarType::GREGORIAN);
$fatherBirthDate->setConfidence(ConfidenceLevel::HIGH);

$fatherBirth->setDate($fatherBirthDate);
$father->setFacts([$fatherBirth]);

// Create mother
$mother = new Person();
$mother->setId('MARY-JONES-1825');

// Create children
$child1 = new Person();
$child1->setId('JAMES-SMITH-1845');

$child2 = new Person();
$child2->setId('SARAH-SMITH-1847');

// Create family view
$family = new FamilyView();
$family->setId('SMITH-FAMILY-1850');

$fatherRef = new ResourceReference();
$fatherRef->setResourceId($father->getId());
$family->setParent1($fatherRef);

$motherRef = new ResourceReference();
$motherRef->setResourceId($mother->getId());
$family->setParent2($motherRef);

$child1Ref = new ResourceReference();
$child1Ref->setResourceId($child1->getId());
$family->addChild($child1Ref);

$child2Ref = new ResourceReference();
$child2Ref->setResourceId($child2->getId());
$family->addChild($child2Ref);

// Serialize to JSON
$familyJson = json_encode($family->toArray(), JSON_PRETTY_PRINT);
$fatherJson = json_encode($father->toArray(), JSON_PRETTY_PRINT);

echo "Family:\n{$familyJson}\n\n";
echo "Father:\n{$fatherJson}\n";
```

### Example 2: Historical Date with Calendar Conversion

```php
<?php

use Gedcomx\Conclusion\DateInfo;
use Gedcomx\Types\CalendarType;
use Gedcomx\Types\ConfidenceLevel;

/**
 * The British Calendar Act of 1751
 * Britain switched from Julian to Gregorian calendar in 1752
 * Wednesday, 2 September 1752 (Julian) was followed by
 * Thursday, 14 September 1752 (Gregorian)
 */

// An event on the last day before the switch
$lastJulianDay = new DateInfo();
$lastJulianDay->setOriginal('2 September 1752');
$lastJulianDay->setFormal('+1752-09-02');
$lastJulianDay->setCalendar(CalendarType::JULIAN);
$lastJulianDay->setConfidence(ConfidenceLevel::HIGH);

// This is also 13 September 1752 in Gregorian
$gregorianEquiv = new DateInfo();
$gregorianEquiv->setOriginal('13 September 1752');
$gregorianEquiv->setFormal('+1752-09-13');
$gregorianEquiv->setCalendar(CalendarType::GREGORIAN);

$lastJulianDay->setAlternateCalendarDates([$gregorianEquiv]);

// Serialize
$json = json_encode($lastJulianDay->toArray(), JSON_PRETTY_PRINT);
echo $json;
```

### Example 3: Jewish Genealogy with Hebrew Dates

```php
<?php

use Gedcomx\Conclusion\Person;
use Gedcomx\Conclusion\Fact;
use Gedcomx\Conclusion\DateInfo;
use Gedcomx\Conclusion\PlaceReference;
use Gedcomx\Types\CalendarType;

// Create person
$person = new Person();
$person->setId('ABRAHAM-COHEN-1850');

// Birth with Hebrew date
$birthFact = new Fact();
$birthFact->setType('http://gedcomx.org/Birth');

// Primary date in Hebrew calendar
$hebrewBirthDate = new DateInfo();
$hebrewBirthDate->setOriginal('15 Av 5610');
$hebrewBirthDate->setCalendar(CalendarType::HEBREW);

// Gregorian equivalent
$gregorianBirthDate = new DateInfo();
$gregorianBirthDate->setOriginal('August 6, 1850');
$gregorianBirthDate->setFormal('+1850-08-06');
$gregorianBirthDate->setCalendar(CalendarType::GREGORIAN);

$hebrewBirthDate->setAlternateCalendarDates([$gregorianBirthDate]);

$birthFact->setDate($hebrewBirthDate);

$birthPlace = new PlaceReference();
$birthPlace->setOriginal('Warsaw, Poland');
$birthFact->setPlace($birthPlace);

$person->setFacts([$birthFact]);
```

### Example 4: Multi-Generation Family Tree

```php
<?php

use Gedcomx\Conclusion\FamilyView;
use Gedcomx\Common\ResourceReference;

// Grandparents' family
$grandparentsFamily = new FamilyView();
$grandparentsFamily->setId('GENERATION-1');

$grandfather = new ResourceReference();
$grandfather->setResource('GRANDFATHER-1800');
$grandparentsFamily->setParent1($grandfather);

$grandmother = new ResourceReference();
$grandmother->setResource('GRANDMOTHER-1805');
$grandparentsFamily->setParent2($grandmother);

// Their child (who becomes parent in next generation)
$parent = new ResourceReference();
$parent->setResource('PARENT-1825');
$grandparentsFamily->addChild($parent);

// Parents' family
$parentsFamily = new FamilyView();
$parentsFamily->setId('GENERATION-2');
$parentsFamily->setParent1($parent);

$otherParent = new ResourceReference();
$otherParent->setResource('OTHER-PARENT-1828');
$parentsFamily->setParent2($otherParent);

// Grandchildren
for ($i = 1; $i <= 4; $i++) {
    $child = new ResourceReference();
    $child->setResource("CHILD-{$i}");
    $parentsFamily->addChild($child);
}

// Now you have two generations of families
$families = [$grandparentsFamily, $parentsFamily];
```

---

## Migration Guide

### Upgrading from Earlier Versions

If you're upgrading from an earlier version of the SDK, here's what you need to know:

#### 1. Existing DateInfo Objects

**Before** (still works):
```php
$date = new DateInfo();
$date->setOriginal('1900');
$date->setFormal('+1900');
```

**After** (enhanced with new properties):
```php
$date = new DateInfo();
$date->setOriginal('1900');
$date->setFormal('+1900');
$date->setCalendar(CalendarType::GREGORIAN);  // NEW
$date->setConfidence(ConfidenceLevel::HIGH);  // NEW
```

✅ **Backward compatible**: Old code continues to work without changes.

#### 2. Representing Families

**Before** (using Relationship):
```php
use Gedcomx\Conclusion\Relationship;

$relationship = new Relationship();
$relationship->setType('http://gedcomx.org/ParentChild');
$relationship->setPerson1($parentRef);
$relationship->setPerson2($childRef);
```

**After** (using FamilyView for display):
```php
use Gedcomx\Conclusion\FamilyView;

$family = new FamilyView();
$family->setParent1($parentRef);
$family->addChild($childRef);
```

✅ **Note**: Both approaches are valid. Use FamilyView for display, Relationship for formal conclusions.

#### 3. JSON/XML Serialization

✅ **Automatic**: All new properties serialize automatically using existing `toArray()` and `writeXmlContents()` methods.

**Example**:
```php
$date = new DateInfo();
$date->setOriginal('1900');
$date->setCalendar(CalendarType::GREGORIAN);

// Automatically includes calendar in output
$json = json_encode($date->toArray());
```

---

## Best Practices

### Calendar Selection Guidelines

1. **Use GREGORIAN** for:
   - Modern dates (post-1582 in Catholic countries, post-1752 in Britain)
   - Dates with no specific calendar context
   - International genealogy

2. **Use JULIAN** for:
   - Pre-1582 dates in Catholic countries
   - Pre-1752 dates in Britain and colonies
   - Historical European research

3. **Use HEBREW** for:
   - Jewish religious dates
   - Events recorded in Hebrew calendar
   - Synagogue records

4. **Use HIJRI** for:
   - Islamic dates
   - Events in Muslim-majority countries
   - Mosque records

5. **Use FRENCH_REPUBLICAN** for:
   - Dates during French Revolution (1793-1805)
   - French civil records from this period

### When to Use Alternate Calendars

✅ **Do use alternate calendars when:**
- Original records use different calendars
- Accuracy requires showing both representations
- Calendar transitions occur during the time period
- Research spans multiple calendar systems

❌ **Don't use alternate calendars when:**
- Only one calendar is relevant
- No ambiguity exists
- Overkill for the use case

### FamilyView vs Relationship

| Use Case | FamilyView | Relationship |
|----------|-----------|--------------|
| UI display of families | ✅ | ❌ |
| Family tree visualization | ✅ | ❌ |
| Pedigree charts | ✅ | ❌ |
| Formal genealogical conclusions | ❌ | ✅ |
| Recording relationship facts | ❌ | ✅ |
| Source citations for relationships | ❌ | ✅ |
| Evidence-based relationships | ❌ | ✅ |

---

## Additional Resources

- [GEDCOM X Specification](http://www.gedcomx.org)
- [GEDCOM X Date Format](https://github.com/FamilySearch/gedcomx/blob/master/specifications/date-format-specification.md)
- [Calendar Conversion Tools](https://www.fourmilab.ch/documents/calendar/)
- [API Reference](https://familysearch.github.io/gedcomx-php/)

---

## Quick Reference

### CalendarType Constants
```php
CalendarType::GREGORIAN
CalendarType::JULIAN
CalendarType::HEBREW
CalendarType::FRENCH_REPUBLICAN
CalendarType::HIJRI
```

### ConfidenceLevel Constants
```php
ConfidenceLevel::HIGH
ConfidenceLevel::MEDIUM
ConfidenceLevel::LOW
```

### FamilyView Methods
```php
$family->setParent1($ref)
$family->setParent2($ref)
$family->getParent1()
$family->getParent2()
$family->setChildren(array $refs)
$family->getChildren()
$family->addChild($ref)
```

### DateInfo New Methods
```php
$date->setCalendar($calendarType)
$date->getCalendar()
$date->setConfidence($confidenceLevel)
$date->getConfidence()
$date->setAlternateCalendarDates(array $dates)
$date->getAlternateCalendarDates()
```

---

**For questions or issues, please visit the [GitHub repository](https://github.com/FamilySearch/gedcomx-php).**
