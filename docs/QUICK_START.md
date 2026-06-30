# Quick Start Guide - New Features

Quick examples to get started with FamilyView and multi-calendar support in GEDCOM X PHP SDK v4.3.0.

## FamilyView - 5 Minute Quickstart

### Basic Family

```php
<?php
require 'vendor/autoload.php';

use Gedcomx\Conclusion\FamilyView;
use Gedcomx\Common\ResourceReference;

// Create a family
$family = new FamilyView();
$family->setId('my-family');

// Add parents
$father = new ResourceReference();
$father->setResource('person-id-1');
$family->setParent1($father);

$mother = new ResourceReference();
$mother->setResource('person-id-2');
$family->setParent2($mother);

// Add children
$child = new ResourceReference();
$child->setResource('person-id-3');
$family->addChild($child);

// Convert to JSON
echo json_encode($family->toArray(), JSON_PRETTY_PRINT);
```

**Output:**
```json
{
    "id": "my-family",
    "parent1": {
        "resource": "person-id-1"
    },
    "parent2": {
        "resource": "person-id-2"
    },
    "children": [
        {
            "resource": "person-id-3"
        }
    ]
}
```

## Calendar Support - 5 Minute Quickstart

### Simple Calendar Usage

```php
<?php
require 'vendor/autoload.php';

use Gedcomx\Conclusion\DateInfo;
use Gedcomx\Types\CalendarType;
use Gedcomx\Types\ConfidenceLevel;

// Create a date with calendar type
$date = new DateInfo();
$date->setOriginal('25 December 1800');
$date->setFormal('+1800-12-25');
$date->setCalendar(CalendarType::GREGORIAN);
$date->setConfidence(ConfidenceLevel::HIGH);

echo $date->getCalendar(); // "http://gedcomx.org/Gregorian"
```

### Alternate Calendar Dates

```php
<?php
require 'vendor/autoload.php';

use Gedcomx\Conclusion\DateInfo;
use Gedcomx\Types\CalendarType;

// Primary date (Gregorian)
$gregorian = new DateInfo();
$gregorian->setOriginal('14 September 1752');
$gregorian->setCalendar(CalendarType::GREGORIAN);

// Alternate date (Julian)
$julian = new DateInfo();
$julian->setOriginal('3 September 1752');
$julian->setCalendar(CalendarType::JULIAN);

// Link them
$gregorian->setAlternateCalendarDates([$julian]);

// Serialize
echo json_encode($gregorian->toArray(), JSON_PRETTY_PRINT);
```

**Output:**
```json
{
    "original": "14 September 1752",
    "calendar": "http://gedcomx.org/Gregorian",
    "alternateCalendarDates": [
        {
            "original": "3 September 1752",
            "calendar": "http://gedcomx.org/Julian"
        }
    ]
}
```

## Available Calendar Types

```php
use Gedcomx\Types\CalendarType;

CalendarType::GREGORIAN          // Modern calendar (1582+)
CalendarType::JULIAN             // Pre-Gregorian European calendar
CalendarType::HEBREW             // Jewish calendar
CalendarType::HIJRI              // Islamic calendar
CalendarType::FRENCH_REPUBLICAN  // French Revolutionary calendar
```

## Available Confidence Levels

```php
use Gedcomx\Types\ConfidenceLevel;

ConfidenceLevel::HIGH    // High confidence (e.g., birth certificate)
ConfidenceLevel::MEDIUM  // Medium confidence (e.g., census record)
ConfidenceLevel::LOW     // Low confidence (e.g., estimated date)
```

## Complete Example: Person with Family

```php
<?php
require 'vendor/autoload.php';

use Gedcomx\Conclusion\Person;
use Gedcomx\Conclusion\Fact;
use Gedcomx\Conclusion\DateInfo;
use Gedcomx\Conclusion\FamilyView;
use Gedcomx\Common\ResourceReference;
use Gedcomx\Types\CalendarType;
use Gedcomx\Types\ConfidenceLevel;

// Create person with enhanced birth date
$person = new Person();
$person->setId('john-smith-1800');

$birthFact = new Fact();
$birthFact->setType('http://gedcomx.org/Birth');

$birthDate = new DateInfo();
$birthDate->setOriginal('10 January 1800');
$birthDate->setFormal('+1800-01-10');
$birthDate->setCalendar(CalendarType::GREGORIAN);
$birthDate->setConfidence(ConfidenceLevel::HIGH);

$birthFact->setDate($birthDate);
$person->setFacts([$birthFact]);

// Create family
$family = new FamilyView();
$family->setId('smith-family');

$parentRef = new ResourceReference();
$parentRef->setResourceId($person->getId());
$family->setParent1($parentRef);

echo "Person created with ID: " . $person->getId() . "\n";
echo "Birth date calendar: " . $person->getFacts()[0]->getDate()->getCalendar() . "\n";
echo "Family created with ID: " . $family->getId() . "\n";
```

## Next Steps

For comprehensive documentation with more examples and use cases, see:
- [NEW_FEATURES_GUIDE.md](NEW_FEATURES_GUIDE.md) - Complete feature documentation
- [GEDCOM X Specification](http://www.gedcomx.org) - Official specification
- [API Tests](../tests/unit/ModelUpdatesTests.php) - More code examples

## Need Help?

- Check the [full documentation](NEW_FEATURES_GUIDE.md)
- See the [test files](../tests/unit/) for more examples
- Visit the [GitHub repository](https://github.com/FamilySearch/gedcomx-php)
