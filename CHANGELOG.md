# Changelog

All notable changes to this project will be documented in this file.

## [4.4.0] - 2026-08-24

### Added
- **Complete FamilySearch Extensions** - Added all missing extension packages from gedcomx-java repository
  
- **Ordinances Package** (16 classes)
  - `Ordinance` - LDS temple ordinance with full lifecycle support
  - `OrdinanceActions` - Available actions (reserve, unreserve, print, etc.)
  - `OrdinanceParticipant` - Participant information with sex and living status
  - `OrdinanceReservation` - Reservation details with assignee and claim information
  - `OrdinanceRollup` - Aggregate ordinance statistics
  - `OrdinanceSummary` - Summary of person's ordinance status
  - Enums: `OrdinanceType`, `OrdinanceStatus`, `OrdinanceStatusReason`, `OrdinanceRoleType`, `OrdinanceSexType`, `OrdinanceRollupStatus`, `OrdinanceReservationAssigneeType`, `OrdinanceReservationClaimType`
  - Full support for baptism, endowment, sealing, and other temple ordinances
  
- **Places Package** (4 classes)
  - `PlaceDescriptionInfo` - Extended place information with geographic hierarchy
  - `FeedbackInfo` - User feedback on place descriptions
  - `PlaceAttribute` - Place attributes with qualifiers
  - `FamilySearchPlaceType` - Enum for place type classifications
  
- **Names Package** (1 class)
  - `NameSearchInfo` - Name search analysis with match scores and alternate spellings
  
- **Records Package** (3 classes)
  - `FieldInfo` - Historical record field metadata
  - `AlternateDate` - Alternate date representations
  - `AlternatePlaceReference` - Alternate place references
  
- **Search Package** (1 class)
  - `Facet` - Faceted search support with hierarchical facets
  
- **Vocabularies Package** (5 classes)
  - `VocabConcept` - Controlled vocabulary concept
  - `VocabConceptAttribute` - Concept attributes
  - `VocabConcepts` - Collection of vocabulary concepts
  - `VocabTerm` - Vocabulary term with language support
  - `VocabTranslation` - Term translations across languages
  
- **Artifacts Package** (3 classes)
  - `ArtifactDisplayState` - Display state for digital artifacts
  - `ArtifactScreeningState` - Screening state and review status
  - `ArtifactAccessPermission` - Access permission management
  
- **Users Package** (1 class)
  - `AgentName` - Extended agent name with given/surname support

### Changed
- Updated `FamilySearchPlatformModelVisitor` and `FamilySearchPlatformModelVisitorBase` to support all new extension classes
- Enhanced `HypermediaEnabledData` with additional link support
- Added `alternateCalendarDates` support to `Date` class in conclusion models

### Documentation
- Created comprehensive [FAMILYSEARCH_EXTENSIONS.md](docs/FAMILYSEARCH_EXTENSIONS.md) covering all extension packages
- Added detailed usage examples for ordinances, places, names, records, search, vocabularies, artifacts, and users
- Moved documentation into `docs/` folder for better organization
- Renamed and cleaned up existing documentation files

### Testing
- Added comprehensive test coverage for all new packages:
  - `OrdinancesTests.php` (266 lines) - Full ordinance lifecycle testing
  - `PlacesTests.php` (120 lines) - Place descriptions and feedback
  - `NamesTests.php` (99 lines) - Name search functionality
  - `RecordsTests.php` (166 lines) - Field info and alternate references
  - `SearchTests.php` (182 lines) - Faceted search testing
  - `VocabTests.php` (259 lines) - Vocabulary concepts and translations
  - `ArtifactsEnumsTests.php` (143 lines) - Artifact states and permissions
  - `UsersAgentNameTests.php` (158 lines) - Agent name handling
- Created `composer test` script for easier test execution
- All tests include JSON/XML serialization validation

### Technical Details
- Full parity with gedcomx-java FamilySearch extensions
- All classes support JSON and XML serialization/deserialization
- Backward compatible - no breaking changes to existing code
- Added ~9,255 lines of production code across 35 new files
- Added ~1,400 lines of test code

## [4.3.0] - 2026-06-30

### Added
- **FamilyView class** for representing family groupings with parents and children
  - `FamilyView` class in `Gedcomx\Conclusion` namespace
  - Support for parent1, parent2, and children (ResourceReference arrays)
  - JSON and XML serialization/deserialization
  - Single-parent family support

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

## [4.1.0] - 2026-04-20

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

## [3.1.0]

### Changed
- Migrate from Travis CI to GitHub Actions
- Add multi-version PHP testing (7.4, 8.0, 8.1, 8.2, 8.3)
- Update CI/CD pipeline with automated testing and coverage reporting
- Update README badges to reflect GitHub Actions status
- Added CHANGELOG.md for a more extensive overview on changes

## [3.0.0]

### Changed
- **BREAKING**: Split out the API networking code into [gedcomx-php-client](https://github.com/FamilySearch/gedcomx-php-client)
  - This library now focuses solely on serialization/deserialization
  - API client functionality moved to separate package

## [2.3.0]

### Added
- Add the `generateClientSecret()` method to `GedcomxApplicationState`

## [2.2.0]

### Added
- Add `logout()` method to application states

## [2.1.1]

### Added
- Support throttling. Enable in `FamilySearchClient` by setting the `throttling` option to `true`

## [2.0.1]

### Fixed
- Fix `FamilySearchClient` so that it automatically follows redirects

## [2.0.0]

### Changed
- **BREAKING**: Upgrade to Guzzle 6 which makes PHP 5.5 the minimum supported version
- `$state->getRequest()` returns a `GuzzleHttp\Psr7\Request`
- `$state->getResponse()` returns a `GuzzleHttp\Psr7\Response`

### Added
- Add a new `httpExceptions` configuration parameter on `FamilySearchClient` that causes an exception to be thrown when a 4xx or 5xx response is received from the API

## [1.2.0]

### Added
- Add a custom user agent string when using the FamilySearchClient
- Register a [PSR-3](http://www.php-fig.org/psr/psr-3/) logger when using the FamilySearchClient
- Add a `setAccessToken()` method to the FamilySearchClient class
- Add `getPerson()` method to the PersonsState
- Add `getStatus()` method to the FamilySearchClient class

### Changed
- Change PersonParentsState, PersonSpousesState, and PersonChildrenState to extend PersonsState

## [1.1.1]

### Fixed
- Fix bugs in the FamilySearchClient class

## [1.1.0]

### Added
- Introduce the FamilySearchClient
- Improve runtime of automated tests with php-vcr

### Changed
- Fix automated tests

### Removed
- Remove apache/log4php dependency

## [1.0.0]

### Added
- Initial stable build to enable Composer installation by version number
