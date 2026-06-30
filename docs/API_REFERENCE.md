# API Reference - New Features (v4.3.0)

Quick reference for the new classes, methods, and constants added in GEDCOM X PHP SDK v4.3.0.

---

## Table of Contents

1. [FamilyView Class](#familyview-class)
2. [DateInfo Enhancements](#dateinfo-enhancements)
3. [CalendarType Enum](#calendartype-enum)
4. [ConfidenceLevel Enum](#confidencelevel-enum)
5. [HasDateAndPlace Interface](#hasdateandplace-interface)

---

## FamilyView Class

**Namespace**: `Gedcomx\Conclusion\FamilyView`  
**Extends**: `Gedcomx\Common\ExtensibleData`

### Purpose
Represents a family unit with parents and children for display purposes.

### Properties

| Property | Type | Description |
|----------|------|-------------|
| `parent1` | `ResourceReference` | Reference to first parent (optional) |
| `parent2` | `ResourceReference` | Reference to second parent (optional) |
| `children` | `ResourceReference[]` | Array of child references (optional) |

### Constructor

```php
public function __construct($o = null)
```

**Parameters**:
- `$o` (mixed) - Either an array (for JSON) or XMLReader instance (optional)

**Example**:
```php
$family = new FamilyView();
$family = new FamilyView(['id' => 'FAMILY-1']);
$family = new FamilyView($xmlReader);
```

### Methods

#### Parent Methods

```php
public function getParent1(): ?ResourceReference
```
Returns the first parent reference, or null if not set.

```php
public function setParent1($parent1): void
```
Sets the first parent reference.

**Parameters**:
- `$parent1` (ResourceReference) - The parent reference

```php
public function getParent2(): ?ResourceReference
```
Returns the second parent reference, or null if not set.

```php
public function setParent2($parent2): void
```
Sets the second parent reference.

**Parameters**:
- `$parent2` (ResourceReference) - The parent reference

#### Children Methods

```php
public function getChildren(): ?array
```
Returns the array of child references, or null if not set.

**Returns**: `ResourceReference[]|null`

```php
public function setChildren($children): void
```
Sets the children array.

**Parameters**:
- `$children` (ResourceReference[]) - Array of child references

```php
public function addChild($child): void
```
Adds a single child to the family.

**Parameters**:
- `$child` (ResourceReference) - The child reference to add

#### Serialization Methods

```php
public function toArray(): array
```
Converts the FamilyView to an associative array for JSON serialization.

**Returns**: Associative array representation

```php
public function initFromArray(array $o): void
```
Initializes the FamilyView from an associative array (JSON deserialization).

**Parameters**:
- `$o` (array) - Associative array with family data

```php
public function writeXmlContents(\XMLWriter $writer): void
```
Writes the FamilyView to XML format.

**Parameters**:
- `$writer` (XMLWriter) - The XML writer instance

### Usage Examples

```php
// Create family
$family = new FamilyView();
$family->setId('MY-FAMILY');

// Add parents
$parent1 = new ResourceReference();
$parent1->setResource('person-1');
$family->setParent1($parent1);

// Add children
$child = new ResourceReference();
$child->setResource('person-2');
$family->addChild($child);

// Serialize
$json = json_encode($family->toArray());
```

---

## DateInfo Enhancements

**Namespace**: `Gedcomx\Conclusion\DateInfo`  
**Extends**: `Gedcomx\Common\ExtensibleData`

### New Properties (v4.3.0)

| Property | Type | Description |
|----------|------|-------------|
| `confidence` | `string` | URI representing confidence level (optional) |
| `calendar` | `string` | URI representing calendar type (optional) |
| `alternateCalendarDates` | `DateInfo[]` | Array of alternate calendar representations (optional) |

### New Methods

#### Confidence Methods

```php
public function getConfidence(): ?string
```
Returns the confidence level URI, or null if not set.

**Returns**: String like `"http://gedcomx.org/High"` or null

```php
public function setConfidence($confidence): void
```
Sets the confidence level.

**Parameters**:
- `$confidence` (string) - Confidence level URI (use ConfidenceLevel constants)

**Example**:
```php
use Gedcomx\Types\ConfidenceLevel;

$date->setConfidence(ConfidenceLevel::HIGH);
```

#### Calendar Methods

```php
public function getCalendar(): ?string
```
Returns the calendar type URI, or null if not set.

**Returns**: String like `"http://gedcomx.org/Gregorian"` or null

```php
public function setCalendar($calendar): void
```
Sets the calendar type.

**Parameters**:
- `$calendar` (string) - Calendar type URI (use CalendarType constants)

**Example**:
```php
use Gedcomx\Types\CalendarType;

$date->setCalendar(CalendarType::GREGORIAN);
```

#### Alternate Calendar Methods

```php
public function getAlternateCalendarDates(): ?array
```
Returns the array of alternate calendar dates, or null if not set.

**Returns**: `DateInfo[]|null`

```php
public function setAlternateCalendarDates($alternateCalendarDates): void
```
Sets the alternate calendar dates array.

**Parameters**:
- `$alternateCalendarDates` (DateInfo[]) - Array of DateInfo objects

**Example**:
```php
// Gregorian date with Julian alternate
$gregorian = new DateInfo();
$gregorian->setOriginal('14 September 1752');
$gregorian->setCalendar(CalendarType::GREGORIAN);

$julian = new DateInfo();
$julian->setOriginal('3 September 1752');
$julian->setCalendar(CalendarType::JULIAN);

$gregorian->setAlternateCalendarDates([$julian]);
```

### Existing Methods (Still Available)

```php
public function getOriginal(): ?string
public function setOriginal($original): void
public function getFormal(): ?string
public function setFormal($formal): void
public function getNormalizedExtensions(): ?array
public function setNormalizedExtensions($normalizedExtensions): void
public function addNormalizedExtension(TextValue $normalized): void
public function getFields(): ?array
public function setFields($fields): void
public function getDateTime(): \DateTime
public function toArray(): array
public function initFromArray(array $o): void
public function writeXmlContents(\XMLWriter $writer): void
```

---

## CalendarType Enum

**Namespace**: `Gedcomx\Types\CalendarType`

### Constants

```php
const GREGORIAN = "http://gedcomx.org/Gregorian"
```
The Gregorian calendar (modern international calendar, 1582+).

```php
const JULIAN = "http://gedcomx.org/Julian"
```
The Julian calendar (pre-Gregorian European calendar).

```php
const HEBREW = "http://gedcomx.org/Hebrew"
```
The Hebrew calendar (Jewish religious calendar).

```php
const HIJRI = "http://gedcomx.org/Hijri"
```
The Islamic calendar (Hijri lunar calendar).

```php
const FRENCH_REPUBLICAN = "http://gedcomx.org/FrenchRepublican"
```
The French Republican calendar (1793-1805).

### Usage

```php
use Gedcomx\Conclusion\DateInfo;
use Gedcomx\Types\CalendarType;

$date = new DateInfo();
$date->setOriginal('25 December 1800');
$date->setCalendar(CalendarType::GREGORIAN);

// Access the value
echo $date->getCalendar(); // "http://gedcomx.org/Gregorian"
```

### Calendar Selection Guide

| Calendar | Use For | Time Period |
|----------|---------|-------------|
| GREGORIAN | Modern dates | 1582+ (Catholic), 1752+ (Britain) |
| JULIAN | Historical European dates | Before Gregorian adoption |
| HEBREW | Jewish dates | Any period |
| HIJRI | Islamic dates | Any period (622 CE+) |
| FRENCH_REPUBLICAN | French Revolution dates | 1793-1805 |

---

## ConfidenceLevel Enum

**Namespace**: `Gedcomx\Types\ConfidenceLevel`

### Constants

```php
const HIGH = "http://gedcomx.org/High"
```
High confidence in the date accuracy.

**Use for**: Primary sources (birth certificates, church records, etc.)

```php
const MEDIUM = "http://gedcomx.org/Medium"
```
Medium confidence in the date accuracy.

**Use for**: Secondary sources (census records with dates, family records)

```php
const LOW = "http://gedcomx.org/Low"
```
Low confidence in the date accuracy.

**Use for**: Estimated dates, approximate dates ("about 1920")

### Usage

```php
use Gedcomx\Conclusion\DateInfo;
use Gedcomx\Types\ConfidenceLevel;

// High confidence (from birth certificate)
$birthDate = new DateInfo();
$birthDate->setOriginal('15 May 1850');
$birthDate->setConfidence(ConfidenceLevel::HIGH);

// Low confidence (estimated)
$deathDate = new DateInfo();
$deathDate->setOriginal('About 1920');
$deathDate->setConfidence(ConfidenceLevel::LOW);
```

---

## HasDateAndPlace Interface

**Namespace**: `Gedcomx\Conclusion\HasDateAndPlace`

### Purpose
Interface for model classes that have both a date and a place property. Provides a consistent contract for classes with temporal and geographic data.

### Methods

```php
public function getDate(): ?DateInfo
```
Returns the date associated with this conclusion.

```php
public function setDate($date): void
```
Sets the date associated with this conclusion.

**Parameters**:
- `$date` (DateInfo) - The date object

```php
public function getPlace(): ?PlaceReference
```
Returns the place associated with this conclusion.

```php
public function setPlace($place): void
```
Sets the place associated with this conclusion.

**Parameters**:
- `$place` (PlaceReference) - The place reference

### Implementing Classes

- `Gedcomx\Conclusion\Fact` - Facts have dates and places
- `Gedcomx\Conclusion\Event` - Events have dates and places

### Usage

```php
use Gedcomx\Conclusion\Fact;
use Gedcomx\Conclusion\DateInfo;
use Gedcomx\Conclusion\PlaceReference;
use Gedcomx\Types\CalendarType;

$fact = new Fact();
$fact->setType('http://gedcomx.org/Birth');

// Set date
$date = new DateInfo();
$date->setOriginal('1900');
$date->setCalendar(CalendarType::GREGORIAN);
$fact->setDate($date);

// Set place
$place = new PlaceReference();
$place->setOriginal('London, England');
$fact->setPlace($place);

// Access via interface methods
$birthDate = $fact->getDate();
$birthPlace = $fact->getPlace();
```

---

## Serialization Support

All new features fully support JSON and XML serialization/deserialization:

### JSON Example

```php
$family = new FamilyView();
$family->setId('FAMILY-1');
// ... set properties ...

// Serialize to JSON
$json = json_encode($family->toArray());

// Deserialize from JSON
$restored = new FamilyView(json_decode($json, true));
```

### XML Example

```php
$date = new DateInfo();
$date->setOriginal('1800');
$date->setCalendar(CalendarType::GREGORIAN);

// Serialize to XML
$writer = new XMLWriter();
$writer->openMemory();
$writer->startElementNs('gx', 'date', 'http://gedcomx.org/v1/');
$date->writeXmlContents($writer);
$writer->endElement();
$xml = $writer->outputMemory();

// Deserialize from XML
$reader = new XMLReader();
$reader->XML($xml);
$reader->read();
$restored = new DateInfo($reader);
```

---

## Backward Compatibility

✅ **All new features are backward compatible**:
- New properties default to null
- Existing code continues to work without changes
- No breaking changes to existing APIs
- Optional properties don't affect existing serialization

---

## Version Requirements

- **Minimum PHP**: 7.4+
- **GEDCOM X PHP SDK**: 4.3.0+
- **PHPUnit** (for testing): 9.5+

---

## Additional Resources

- [Complete Feature Guide](NEW_FEATURES_GUIDE.md)
- [Quick Start Guide](QUICK_START.md)
- [Test Examples](../tests/unit/ModelUpdatesTests.php)
- [GEDCOM X Specification](http://www.gedcomx.org)

---

**Last Updated**: 2026-06-30  
**SDK Version**: 4.3.0
