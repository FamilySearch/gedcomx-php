# GedcomX - PHP SDK  

[![CI](https://github.com/FamilySearch/gedcomx-php/actions/workflows/ci.yml/badge.svg)](https://github.com/FamilySearch/gedcomx-php/actions/workflows/ci.yml)
[![Packagist](https://img.shields.io/packagist/v/gedcomx/gedcomx-php.svg)](https://packagist.org/packages/gedcomx/gedcomx-php)
[![PHP Version](https://img.shields.io/badge/php-7.4%20%7C%208.0%20%7C%208.1%20%7C%208.2%20%7C%208.3-blue.svg)](https://github.com/FamilySearch/gedcomx-php)
[![License](https://img.shields.io/badge/license-Apache%202.0-blue.svg)](LICENSE)

The PHP implementation of [GEDCOM X](http://www.gedcomx.org), including GEDCOM X extension projects. This SDK provides comprehensive classes for serialization and deserialization of GEDCOM X data and FamilySearch extensions.

**Version Parity**: This PHP SDK (v4.4+) has feature parity with [gedcomx-java SDK v4.3.0](https://github.com/FamilySearch/gedcomx-java).

For FamilySearch API integration, see [gedcomx-php-client](https://github.com/FamilySearch/gedcomx-php-client).

## Requirements

- **PHP 7.4 or higher** (tested on PHP 7.4, 8.0, 8.1, 8.2, 8.3)
- [Composer](https://getcomposer.org/doc/00-intro.md) (PHP dependency manager)

## Installation

### Prerequisites

Prior to installing the GedcomX PHP SDK you must have PHP 7.4+ and Composer installed.

    **Notes to Mac Developers:** 
  * Mac developers might need to install the Xcode developer tools as part of this process. 
  * Mac developers may need to set the `date.timezone` in /etc/php.ini to avoid seeing errors. See [PHP time zones](http://php.net/manual/en/timezones.php) to locate your time zone. For example, 
  ```
    `date.timezone = "America/Denver"` 
  ```

You can verify that each of the required components is installed by running the following commands one line at a time, at your command line or command prompt:
```
php -v
composer --version
git --version
```

### Installation Steps

**NOTE:** You only need to install the SDK one time for each PHP project you set up. 

The GedcomX PHP SDK uses [composer](https://getcomposer.org) to manage dependencies. These instructions assume that you have installed [Composer globally](https://getcomposer.org/doc/00-intro.md#globally).

Do **one** of the following steps to activate Composer and install the gedcomx-php SDK library:

* Run the following command at a command prompt from the root of your project:

    ```
    composer require gedcomx/gedcomx-php
    ```

* Add the following configuration to your composer.json file then run the `composer install` command at your command prompt.

    ```json
    {
        "require": {
        "gedcomx/gedcomx-php": "^1.1"
        }
    }
    ```

## Features

* **GEDCOM X Conceptual Model**

  Implementation of the [GEDCOM X Conceptual Model](https://github.com/FamilySearch/gedcomx/blob/master/specifications/conceptual-model-specification.md) in a rich set of PHP Classes with getter and setter methods. Take a look at a [visual graph](https://github.com/FamilySearch/gedcomx/blob/master/specifications/support/conceptual-model-graph.pdf) of the GEDCOM X Conceptual Model.

* **GEDCOM X Serialization**

  XML and JSON serialization and deserialization of GEDCOM X. For more information, see the [examples](https://github.com/FamilySearch/gedcomx-php/wiki/GEDCOM-X-Serialization).

* **FamilyView for Family Groupings**

  Display families with parents and children using the new `FamilyView` class. Perfect for family tree visualizations and pedigree charts.

* **Multi-Calendar Support**

  Represent dates in multiple calendar systems (Gregorian, Julian, Hebrew, Islamic, French Republican) with automatic alternate calendar date support.

* **FamilySearch Extensions**

  Full support for FamilySearch-specific extensions including:
  - **LDS Temple Ordinances** - Baptism, endowment, sealing, and other temple ordinances with reservations and participants
  - **Places** - Enhanced geographic data, place descriptions, and feedback
  - **Names** - Name search analysis and information
  - **Records** - Historical record field information and metadata
  - **Search** - Faceted search with hierarchical facets
  - **Vocabularies** - Controlled vocabulary concepts, terms, and translations
  - **Artifacts** - Digital artifact management, display states, and access permissions
  - **Users** - Extended user agent information

For detailed documentation and examples, see [FAMILYSEARCH_EXTENSIONS.md](docs/FAMILYSEARCH_EXTENSIONS.md). 

## Families and Calendars

The SDK includes advanced features for family groupings and multi-calendar support. The `FamilyView` class provides a convenient way to represent family units with parents and children for display purposes, while the calendar system supports multiple calendar types (Gregorian, Julian, Hebrew, Islamic, French Republican) with automatic alternate calendar date representations.

For comprehensive guides and examples, see [FAMILIES_AND_CALENDARS.md](docs/FAMILIES_AND_CALENDARS.md).

## Testing

The GedcomX PHP SDK includes a comprehensive test suite that runs on PHP 7.4, 8.0, 8.1, 8.2, and 8.3.

### Running Tests

```bash
# Install dependencies
composer install

# Run the test suite
vendor/bin/phpunit

# Run tests with detailed output
vendor/bin/phpunit --testdox

# Generate coverage report (requires Xdebug or PCOV)
vendor/bin/phpunit --coverage-html build/coverage
```

### Test Coverage

The test suite includes:

- ✅ Core GEDCOM X models (Person, Relationship, Fact, Name, Event, etc.)
- ✅ FamilySearch extension models (ChildAndParentsRelationship, etc.)
- ✅ XML and JSON serialization/deserialization
- ✅ GEDCOMX file operations (.gedx archives)
- ✅ Fixture validation (XML, JSON, GEDX)

Coverage reports are automatically generated by CI and uploaded to [Coveralls](https://coveralls.io/github/FamilySearch/gedcomx-php?branch=master).

### Continuous Integration

Tests run automatically on every push and pull request via [GitHub Actions](https://github.com/FamilySearch/gedcomx-php/actions). All supported PHP versions are tested in parallel.

For more details on testing, see [TESTING.md](docs/TESTING.md).

## Security

Security is a top priority for this SDK. We follow responsible disclosure practices and provide security best practices for handling genealogical data, XML parsing (XXE prevention), archive processing, and dependency management. 

For vulnerability reporting, security policies, and best practices, see [SECURITY.md](SECURITY.md).
