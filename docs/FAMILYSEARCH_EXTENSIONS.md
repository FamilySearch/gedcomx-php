# FamilySearch Extensions

This document provides comprehensive documentation for the FamilySearch-specific extensions implemented in gedcomx-php.

## Table of Contents

1. [Overview](#overview)
2. [Ordinances Package](#ordinances-package)
3. [Places Package](#places-package)
4. [Names Package](#names-package)
5. [Records Package](#records-package)
6. [Search Package](#search-package)
7. [Vocab Package](#vocab-package)
8. [Users Package](#users-package)
9. [Artifacts Package](#artifacts-package)
10. [Complete Examples](#complete-examples)

## Overview

The FamilySearch extensions provide specialized functionality for working with FamilySearch-specific data, including:

- **LDS Temple Ordinances**: Managing baptism, endowment, sealing, and other temple ordinances
- **Places**: Enhanced geographic data and place descriptions
- **Names**: Name search and analysis
- **Records**: Historical record field information
- **Search**: Faceted search capabilities
- **Vocabularies**: Controlled vocabulary concepts and terms
- **Users**: Extended user agent information
- **Artifacts**: Digital artifact management and permissions

All classes are located under the `Gedcomx\Extensions\FamilySearch\Platform` namespace.

---

## Ordinances Package

### Purpose

The Ordinances package manages LDS temple ordinances, including reservations, participants, and status tracking for:
- Baptism
- Confirmation
- Initiatory
- Endowment
- Sealing to Spouse
- Sealing Child to Parents

### Class Hierarchy

**Enums:**
- `OrdinanceType` - Types of ordinances
- `OrdinanceStatus` - Status values (Completed, Ready, NotReady, etc.)
- `OrdinanceStatusReason` - Detailed reasons for status
- `OrdinanceRoleType` - Participant roles (Parent, Spouse)
- `OrdinanceSexType` - Sex designation for ordinances
- `OrdinanceReservationClaimType` - Reservation claim types
- `OrdinanceReservationAssigneeType` - Reservation assignee types
- `OrdinanceRollupStatus` - Aggregated ordinance status

**Data Classes:**
- `Ordinance` (extends `Conclusion`) - Main ordinance data
- `OrdinanceActions` - Available actions (reservable, shareable, printable)
- `OrdinanceParticipant` - Participant information
- `OrdinanceReservation` - Reservation details
- `OrdinanceSummary` - Summary counts
- `OrdinanceRollup` (extends `Conclusion`) - Rollup status

### Usage Examples

#### Creating an Ordinance

```php
use Gedcomx\Extensions\FamilySearch\Platform\Ordinances\Ordinance;
use Gedcomx\Extensions\FamilySearch\Platform\Ordinances\OrdinanceType;
use Gedcomx\Extensions\FamilySearch\Platform\Ordinances\OrdinanceStatus;
use Gedcomx\Common\ResourceReference;
use Gedcomx\Conclusion\Date;

$ordinance = new Ordinance();
$ordinance->setType(OrdinanceType::BAPTISM);
$ordinance->setStatus(OrdinanceStatus::COMPLETED);

// Set the person
$person = new ResourceReference();
$person->setResource('https://familysearch.org/platform/persons/PPPP-PPP');
$ordinance->setPerson($person);

// Set completion details
$ordinance->setTempleCode('SLAKE');
$ordinance->setFullName('John Smith');

$date = new Date();
$date->setOriginal('15 January 2020');
$ordinance->setCompleteDate($date);
```

#### Adding Participants (for Sealing)

```php
use Gedcomx\Extensions\FamilySearch\Platform\Ordinances\OrdinanceParticipant;
use Gedcomx\Extensions\FamilySearch\Platform\Ordinances\OrdinanceRoleType;
use Gedcomx\Extensions\FamilySearch\Platform\Ordinances\OrdinanceSexType;

$ordinance = new Ordinance();
$ordinance->setType(OrdinanceType::SEALING_TO_SPOUSE);

// Add spouse 1
$spouse1 = new OrdinanceParticipant();
$spouse1->setRoleType(OrdinanceRoleType::SPOUSE);
$spouse1->setSexType(OrdinanceSexType::MALE);
$spouse1->setFullName('John Doe');

$person1 = new ResourceReference();
$person1->setResource('https://familysearch.org/platform/persons/AAAA-AAA');
$spouse1->setParticipant($person1);

// Add spouse 2
$spouse2 = new OrdinanceParticipant();
$spouse2->setRoleType(OrdinanceRoleType::SPOUSE);
$spouse2->setSexType(OrdinanceSexType::FEMALE);
$spouse2->setFullName('Jane Doe');

$person2 = new ResourceReference();
$person2->setResource('https://familysearch.org/platform/persons/BBBB-BBB');
$spouse2->setParticipant($person2);

$ordinance->setParticipants([$spouse1, $spouse2]);
```

#### Working with Reservations

```php
use Gedcomx\Extensions\FamilySearch\Platform\Ordinances\OrdinanceReservation;
use Gedcomx\Extensions\FamilySearch\Platform\Ordinances\OrdinanceReservationClaimType;
use Gedcomx\Extensions\FamilySearch\Platform\Ordinances\OrdinanceReservationAssigneeType;

$reservation = new OrdinanceReservation();

$owner = new ResourceReference();
$owner->setResource('https://familysearch.org/platform/users/1234');
$reservation->setOwner($owner);

$reservation->setReserveDate(new \DateTime('2024-01-15'));
$reservation->setExpirationDate(new \DateTime('2024-07-15'));
$reservation->setClaimType(OrdinanceReservationClaimType::DEFAULT_TYPE);
$reservation->setAssigneeType(OrdinanceReservationAssigneeType::PERSONAL);

$ordinance->setReservation($reservation);
```

### Enum Value Descriptions

**OrdinanceType:**
- `BAPTISM` - Baptism for the dead
- `CONFIRMATION` - Confirmation
- `INITIATORY` - Initiatory ordinance
- `ENDOWMENT` - Endowment
- `SEALING_TO_SPOUSE` - Sealing of husband and wife
- `SEALING_CHILD_TO_PARENTS` - Sealing of child to parents

**OrdinanceStatus:**
- `BORN_IN_COVENANT` - Not needed (born in covenant)
- `COMPLETED` - Ordinance completed
- `READY` - Ready to be reserved
- `NOT_READY` - Not ready (more information needed)
- `RESERVED` - Currently reserved
- `NOT_AVAILABLE` - Not available for reservation

---

## Places Package

### Purpose

Enhanced geographic and place description functionality.

### Classes

- `FamilySearchPlaceType` (enum) - Place types (Place, PlaceGroup)
- `FeedbackInfo` - Place feedback and corrections
- `PlaceDescriptionInfo` - Additional place description metadata
- `PlaceAttribute` - Place attributes (population, etc.)

### Usage Example

```php
use Gedcomx\Extensions\FamilySearch\Platform\Places\FeedbackInfo;
use Gedcomx\Common\ResourceReference;

$feedback = new FeedbackInfo();
$feedback->setStatus('http://familysearch.org/v1/Pending');
$feedback->setResolution('http://familysearch.org/v1/Accepted');
$feedback->setDetails('The location coordinates are incorrect');

$place = new ResourceReference();
$place->setResource('https://familysearch.org/platform/places/12345');
$feedback->setPlace($place);
```

---

## Names Package

### Purpose

Name search and analysis functionality.

### Classes

- `NameSearchInfo` - Search result information for names

### Usage Example

```php
use Gedcomx\Extensions\FamilySearch\Platform\Names\NameSearchInfo;
use Gedcomx\Types\NamePartType;

$info = new NameSearchInfo();
$info->setText('Smith');
$info->setNameId('N-123456');
$info->setNamePartType(NamePartType::SURNAME);
$info->setWeight(95);

// Get the known name part type
$partType = $info->getKnownNamePartType(); // Returns "Surname"
```

---

## Records Package

### Purpose

Historical record field information and metadata.

### Classes

- `FieldInfo` - Field metadata (type, label, editability)
- `AlternateDate` (extends `Date`) - Alternative date representations
- `AlternatePlaceReference` (extends `PlaceReference`) - Alternative place references

### Usage Example

```php
use Gedcomx\Extensions\FamilySearch\Platform\Records\FieldInfo;

$field = new FieldInfo();
$field->setFieldType('http://gedcomx.org/BirthDate');
$field->setDisplayLabel('Birth Date');
$field->setStandard(true);
$field->setEditable(false);
$field->setDisplayable(true);
$field->setElementTypes(['date', 'text']);

if ($field->isStandard() && $field->isDisplayable()) {
    echo "Display: " . $field->getDisplayLabel();
}
```

---

## Search Package

### Purpose

Faceted search capabilities with hierarchical facets.

### Classes

- `Facet` - Search facet with recursive structure

### Usage Example

```php
use Gedcomx\Extensions\FamilySearch\Platform\Search\Facet;

// Create parent facet
$locationFacet = new Facet();
$locationFacet->setDisplayName('Birth Location');
$locationFacet->setCount(1000);

// Create child facets
$englandFacet = new Facet();
$englandFacet->setDisplayName('England');
$englandFacet->setCount(600);
$englandFacet->setParams('birthPlace:England');

$usaFacet = new Facet();
$usaFacet->setDisplayName('United States');
$usaFacet->setCount(400);
$usaFacet->setParams('birthPlace:USA');

$locationFacet->setFacets([$englandFacet, $usaFacet]);

// Iterate through facets
foreach ($locationFacet->facets() as $facet) {
    echo $facet->getDisplayName() . ": " . $facet->getCount() . "\n";
}
```

---

## Vocab Package

### Purpose

Controlled vocabulary concept management with terms, translations, and attributes.

### Classes

- `VocabConcept` (extends `HypermediaEnabledData`) - Vocabulary concept
- `VocabTerm` (extends `HypermediaEnabledData`) - Vocabulary term
- `VocabTranslation` (extends `HypermediaEnabledData`) - Term translation
- `VocabConceptAttribute` - Concept attribute
- `VocabConcepts` - Collection of concepts

### Usage Example

```php
use Gedcomx\Extensions\FamilySearch\Platform\Vocab\VocabConcept;
use Gedcomx\Extensions\FamilySearch\Platform\Vocab\VocabTerm;
use Gedcomx\Extensions\FamilySearch\Platform\Vocab\VocabTranslation;
use Gedcomx\Extensions\FamilySearch\Platform\Vocab\VocabConceptAttribute;
use Gedcomx\Common\TextValue;

// Create a concept
$concept = new VocabConcept();
$concept->setDescription('The event of birth');
$concept->setGedcomxUri('http://gedcomx.org/Birth');

// Add a term
$term = new VocabTerm();
$term->setTypeUri('http://gedcomx.org/Label');

$value1 = new TextValue();
$value1->setLang('en');
$value1->setValue('Birth');

$value2 = new TextValue();
$value2->setLang('es');
$value2->setValue('Nacimiento');

$term->setValues([$value1, $value2]);
$concept->setVocabTerms([$term]);

// Add an attribute
$attr = new VocabConceptAttribute();
$attr->setName('category');
$attr->setValue('event');
$concept->setAttributes([$attr]);

// Add a translation
$translation = new VocabTranslation('Birth', 'en');
```

---

## Users Package

### Purpose

Extended user agent information.

### Classes

- `AgentName` (extends `TextValue`) - Agent name with type

### Usage Example

```php
use Gedcomx\Extensions\FamilySearch\Platform\Users\AgentName;

// Constructor with parameters
$name = new AgentName('http://gedcomx.org/BirthName', 'John Smith', 'en');

// Or set properties individually
$name = new AgentName();
$name->setType('http://gedcomx.org/BirthName');
$name->setValue('John Smith');
$name->setLang('en');

echo $name->getType();  // http://gedcomx.org/BirthName
echo $name->getValue(); // John Smith
echo $name->getLang();  // en
```

---

## Artifacts Package

### Purpose

Digital artifact management, display states, and access permissions.

### Classes (Enums)

- `ArtifactType` - Artifact types (Audio, Document, Image, Portrait, Story, Video)
- `ArtifactDisplayState` - Display states (Processing, UploadFailed, Approved, Restricted)
- `ArtifactScreeningState` (deprecated) - Old screening states
- `ArtifactAccessPermission` - Access permissions (Allowed, Denied)

### Usage Example

```php
use Gedcomx\Extensions\FamilySearch\Platform\Artifacts\ArtifactDisplayState;
use Gedcomx\Extensions\FamilySearch\Platform\Artifacts\ArtifactAccessPermission;

// Check artifact state
$displayState = ArtifactDisplayState::APPROVED;

if ($displayState === ArtifactDisplayState::APPROVED) {
    $permission = ArtifactAccessPermission::ALLOWED;
    // Display the artifact
}
```

---

## Complete Examples

### Complete Ordinance Workflow

```php
use Gedcomx\Extensions\FamilySearch\Platform\Ordinances\Ordinance;
use Gedcomx\Extensions\FamilySearch\Platform\Ordinances\OrdinanceType;
use Gedcomx\Extensions\FamilySearch\Platform\Ordinances\OrdinanceStatus;
use Gedcomx\Extensions\FamilySearch\Platform\Ordinances\OrdinanceReservation;
use Gedcomx\Extensions\FamilySearch\Platform\Ordinances\OrdinanceParticipant;
use Gedcomx\Extensions\FamilySearch\Platform\Ordinances\OrdinanceRoleType;
use Gedcomx\Extensions\FamilySearch\Platform\Ordinances\OrdinanceReservationClaimType;
use Gedcomx\Common\ResourceReference;
use Gedcomx\Conclusion\Date;

// Create the ordinance
$ordinance = new Ordinance();
$ordinance->setType(OrdinanceType::BAPTISM);
$ordinance->setStatus(OrdinanceStatus::RESERVED);

// Set the person
$person = new ResourceReference();
$person->setResource('https://familysearch.org/platform/persons/PPPP-PPP');
$ordinance->setPerson($person);
$ordinance->setFullName('John William Smith');

// Add reservation
$reservation = new OrdinanceReservation();
$owner = new ResourceReference();
$owner->setResource('https://familysearch.org/platform/users/U123');
$reservation->setOwner($owner);
$reservation->setReserveDate(new \DateTime());
$reservation->setExpirationDate(new \DateTime('+6 months'));
$reservation->setClaimType(OrdinanceReservationClaimType::DEFAULT_TYPE);
$ordinance->setReservation($reservation);

// Serialize to JSON
$json = $ordinance->toJson();

// Deserialize from JSON
$decoded = json_decode($json, true);
$ordinance2 = new Ordinance($decoded);
```

### JSON Serialization Example

```php
// Ordinance JSON structure
{
    "type": "http://churchofjesuschrist.org/Baptism",
    "status": "http://familysearch.org/v1/Completed",
    "person": {
        "resource": "https://familysearch.org/platform/persons/PPPP-PPP"
    },
    "fullName": "John Smith",
    "templeCode": "SLAKE",
    "completeDate": {
        "original": "15 January 2020"
    }
}
```

### XML Serialization Example

```php
$ordinance = new Ordinance();
$ordinance->setType(OrdinanceType::BAPTISM);
$ordinance->setTempleCode('PROVO');

$writer = new \XMLWriter();
$writer->openMemory();
$writer->startDocument('1.0', 'UTF-8');
$ordinance->toXml($writer);
$xml = $writer->outputMemory();
```

---

## Testing

Comprehensive unit tests are available for all packages:

- `OrdinancesTests.php` - 15 tests covering all ordinance classes
- `PlacesTests.php` - 9 tests for places functionality
- `NamesTests.php` - 7 tests for name search
- `RecordsTests.php` - 10 tests for record fields
- `SearchTests.php` - 10 tests for faceted search
- `VocabTests.php` - 13 tests for vocabulary management
- `UsersAgentNameTests.php` - 11 tests for agent names
- `ArtifactsEnumsTests.php` - 12 tests for artifact enums

Run tests with:
```bash
composer test
```

---

## API Reference

For detailed API documentation of each class and method, refer to the inline PHPDoc comments in the source code located at:

```
src/Extensions/FamilySearch/Platform/
├── Ordinances/
├── Places/
├── Names/
├── Records/
├── Search/
├── Vocab/
├── Users/
└── Artifacts/
```

---

## Contributing

When working with FamilySearch extensions:

1. Follow existing class patterns for consistency
2. Include comprehensive PHPDoc comments
3. Add unit tests for all new functionality
4. Support both JSON and XML serialization
5. Implement visitor pattern where applicable
6. Use proper namespace URIs (familysearch.org/v1/ or churchofjesuschrist.org/)

---

## License

Copyright Intellectual Reserve, Inc.

Licensed under the Apache License, Version 2.0.
