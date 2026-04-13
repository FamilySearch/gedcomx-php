# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

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

Note: PHP 7.4 and 8.0 are end-of-life and not tested directly, but compatibility is ensured through:
- Composer dependency constraints (PHPUnit ^9.5 supports PHP 7.4+)
- All dependencies declare PHP 7.4+ compatibility
- No PHP 8.0+ specific syntax used in codebase

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
