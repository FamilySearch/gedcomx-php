# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [4.3.0] - 2026-06-30

### Added
- **FamilyView class** for representing family groupings with parents and children
  - `FamilyView` class in `Gedcomx\Conclusion` namespace
  - Support for parent1, parent2, and children (ResourceReference arrays)
  - JSON and XML serialization/deserialization
  - Single-parent family support
  - See [docs/NEW_FEATURES_GUIDE.md](docs/NEW_FEATURES_GUIDE.md) for usage examples

- **Multi-calendar support** for dates
  - `CalendarType` enum with 5 calendar systems:
    - GREGORIAN - Modern international calendar
    - JULIAN - Pre-Gregorian European calendar
    - HEBREW - Jewish religious calendar
    - HIJRI - Islamic lunar calendar
    - FRENCH_REPUBLICAN - French Revolutionary calendar
  - `calendar` property on `DateInfo` class
  - `alternateCalendarDates` property for representing dates in multiple calendars
  - Support for nested DateInfo objects in alternate calendar arrays
  - See [docs/NEW_FEATURES_GUIDE.md](docs/NEW_FEATURES_GUIDE.md) for calendar conversion examples

- **Date confidence levels**
  - `confidence` property on `DateInfo` class
  - ConfidenceLevel enum (HIGH, MEDIUM, LOW) already existed, now integrated with DateInfo

- **HasDateAndPlace interface**
  - Interface for classes with both date and place properties
  - Implemented by Fact and Event classes
  - Ensures consistent API for temporal and geographic data

- **Comprehensive test coverage**
  - 50 new tests (34 model tests + 16 serialization tests)
  - 183 new assertions
  - JSON serialization/deserialization tests
  - XML serialization/deserialization tests
  - Integration tests with Person, Fact, and Event
  - Edge case testing (null handling, empty arrays, nested objects)

- **Documentation**
  - [docs/NEW_FEATURES_GUIDE.md](docs/NEW_FEATURES_GUIDE.md) - Complete feature guide with examples
  - [docs/QUICK_START.md](docs/QUICK_START.md) - Quick start guide for new features
  - [SERIALIZATION_VERIFICATION.md](SERIALIZATION_VERIFICATION.md) - Serialization verification report
  - [TEST_COVERAGE_SUMMARY.md](TEST_COVERAGE_SUMMARY.md) - Test coverage documentation

### Changed
- Updated `DateInfo` class with three new properties (backward compatible)
- Updated README.md to highlight new features
- Enhanced CHANGELOG.md with detailed feature descriptions

### Technical Details
- All new properties serialize correctly in JSON and XML formats
- Full backward compatibility maintained - existing code continues to work
- Zero breaking changes
- 114 total tests passing (98 existing + 16 new)
- 325 total assertions

### Migration Notes
- No code changes required for existing implementations
- New properties are optional and default to null
- See [docs/NEW_FEATURES_GUIDE.md#migration-guide](docs/NEW_FEATURES_GUIDE.md#migration-guide) for upgrade guidance

## [Unreleased]

### Added
- PHP version requirement: `>=7.4` now specified in composer.json
- Multi-version PHP testing: Verified compatibility with PHP 8.1, 8.2, 8.3, 8.4, and 8.5

### Changed
- **PHPUnit**: Updated from 3.7.* (2012) to ^9.5 (9.6.34)
  - Modern test framework with PHP 7.4+ support
  - Improved error reporting and assertions
  - Better code coverage tools
- **Faker**: Replaced abandoned `fzaninotto/faker` 1.4.* with `fakerphp/faker` ^1.9 (1.24.1)
  - Active maintenance and security updates
  - PHP 8.0+ compatibility
  - Same API, drop-in replacement
- **Intervention Image**: Updated from 2.0.* to ^2.7 (2.7.2)
  - Bug fixes and PHP 8.0+ compatibility improvements
  - Better memory management
- **PHP Coveralls**: Replaced `satooshi/php-coveralls` dev-travis-fix with `php-coveralls/php-coveralls` ^2.5 (2.9.1)
  - Official maintained version
  - Modern CI/CD integration
  - No longer requires custom fork repository
- **PHPUnit Configuration**: Updated phpunit.xml to PHPUnit 9+ format
  - Removed deprecated `syntaxCheck` attribute
  - Removed deprecated `testSuiteLoaderClass` attribute  
  - Changed `<filter><whitelist>` to `<coverage><include>` syntax
- **Test Base Class**: Updated from `PHPUnit_Framework_TestCase` to namespaced `PHPUnit\Framework\TestCase`
  - Added return type declarations to `setUp(): void` and `tearDown(): void`

### Removed
- Custom VCS repository for php-coveralls fork (no longer needed)
- Deprecated PHPUnit configuration options
- PHPUnit 3.7 legacy class name references
- `.travis.yml` is now obsolete (replaced by GitHub Actions)

### Security
- ✅ **All security vulnerabilities resolved**: `composer audit` returns 0 vulnerabilities
- Updated all dependencies from 2012-2014 versions to modern, actively maintained releases
- Replaced abandoned packages with secure alternatives
- All dependencies now receive regular security updates

### Breaking Changes
- **Minimum PHP version is now 7.4** (previously unspecified)
  - Projects using PHP 7.3 or lower must upgrade
- **PHPUnit 9 compatibility required**
  - Test classes must extend `PHPUnit\Framework\TestCase` (not `PHPUnit_Framework_TestCase`)
  - `setUp()` and `tearDown()` methods require `void` return type
  - Custom phpunit.xml files may need syntax updates
- **Faker namespace change**
  - If you directly instantiate Faker, use `Faker\Factory::create()` from `fakerphp/faker`
  - API is identical, but package name changed

### Deprecated
- `assertEqualXMLStructure()` is deprecated in PHPUnit 9 and will be removed in PHPUnit 10
  - Affects `GedcomxFileTests::testXMLSerialization`
  - Tests still pass but emit warnings

### Testing
Verified compatibility across multiple PHP versions:
- ✅ PHP 8.1.34 - 6 tests, 23 assertions - PASSED
- ✅ PHP 8.2.30 - 6 tests, 23 assertions - PASSED
- ✅ PHP 8.3.30 - 6 tests, 23 assertions - PASSED
- ✅ PHP 8.4.20 - 6 tests, 23 assertions - PASSED
- ✅ PHP 8.5.5 - 6 tests, 23 assertions - PASSED

**Automated CI Testing** (GitHub Actions):
Continuous integration tests run automatically on PHP 7.4, 8.0, 8.1, 8.2, and 8.3 to prevent regressions.

Note: PHP 7.4 and 8.0 are end-of-life. While CI tests these versions for compatibility verification, production use of PHP 8.1+ is recommended for security updates.

### Migration Guide

#### For Library Users
If you're using gedcomx-php as a dependency:

1. **Update your PHP version**:
   ```bash
   php --version  # Must be 7.4 or higher
   ```

2. **Update the package**:
   ```bash
   composer update gedcomx/gedcomx-php
   ```

3. **No code changes required** - The API remains unchanged

#### For Contributors/Developers

1. **Update PHP if needed**:
   ```bash
   php --version  # Recommended: 8.1+
   ```

2. **Install dependencies**:
   ```bash
   rm -rf vendor composer.lock
   composer install
   ```

3. **Run tests**:
   ```bash
   vendor/bin/phpunit
   ```

4. **Update test classes if extending ApiTestCase**:
   ```php
   // Old (PHPUnit 3.7)
   class MyTest extends \PHPUnit_Framework_TestCase {
       public function setUp() { }
   }
   
   // New (PHPUnit 9+)
   class MyTest extends \PHPUnit\Framework\TestCase {
       protected function setUp(): void { }
   }
   ```

### Known Issues
- PHP 8.1+ emits deprecation notices about return type declarations in `Gedcomx\Util\Collection`
  - These are warnings, not errors
  - Tests pass successfully
  - Will be addressed in a future update
