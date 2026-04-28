# Testing Guide

## Overview

The GEDCOM X PHP SDK includes a comprehensive test suite using PHPUnit 9.5+ to ensure compatibility across PHP versions 7.4 through 8.3.

## Running Tests

### Prerequisites

- PHP 7.4, 8.0, 8.1, 8.2, or 8.3
- Composer dependencies installed
- Xdebug or PCOV extension (optional, for coverage reports)

### Basic Test Execution

Run the complete test suite:

```bash
vendor/bin/phpunit
```

### Running Specific Tests

Run a specific test file:

```bash
vendor/bin/phpunit tests/unit/PersonTests.php
```

Run a specific test method:

```bash
vendor/bin/phpunit --filter testPerson
```

## Code Coverage

### Generating Coverage Reports

**HTML Report** (most detailed):

```bash
vendor/bin/phpunit --coverage-html build/coverage
```

Then open `build/coverage/index.html` in your browser.

**Text Report** (terminal output):

```bash
vendor/bin/phpunit --coverage-text
```

**Clover XML** (for CI/CD integration):

```bash
vendor/bin/phpunit --coverage-clover build/logs/clover.xml
```

### Coverage Baseline (2026)

Current test coverage baseline established:

- **Test Files**: 3 test suites
- **Test Cases**: 6 tests, 12 assertions
- **Source Files**: 125 PHP files
- **Core Models**: GEDCOM X conceptual model classes
- **Extensions**: FamilySearch platform extensions

### Coverage Areas

#### Well-Covered Components

- GEDCOM X file format (.gedx) reading and writing
- XML serialization and deserialization
- Person model deserialization
- Child and Parents relationship structures

#### Areas Needing Expansion

Additional test coverage should be added for:

1. **GEDCOM X Core Models**
   - Agent and Address models
   - Conclusion types (Gender, Fact, Event, etc.)
   - Source descriptions and citations
   - Place descriptions and references
   - Name forms and name parts

2. **FamilySearch Extensions**
   - User models
   - Discussion and Comment models
   - Discussion references
   - Extended fact types

3. **Serialization**
   - JSON serialization (currently only XML is tested)
   - Edge cases and malformed data handling
   - Namespace handling for extensions

4. **Collections and Utilities**
   - Collection class operations
   - Reference resolvers
   - Model visitor patterns

## Test Structure

### Directory Layout

```
tests/
├── bootstrap.php          # Test initialization
├── ApiTestCase.php        # Base test case class
├── ArtifactBuilder.php    # Test artifact generation
├── PersonBuilder.php      # Test person creation
├── TestBuilder.php        # General test utilities
├── XMLBuilder.php         # XML test utilities
├── files/                 # Test fixtures
│   ├── person.json
│   ├── record.xml
│   ├── sample.gedx
│   └── cap-relationship-control.xml
├── tmp/                   # Temporary test output
└── unit/                  # Unit tests
    ├── PersonTests.php
    ├── XMLTests.php
    └── GedcomxFileTests.php
```

### Test Fixtures

Test fixtures are located in `tests/files/`:

- **person.json**: Sample person data in JSON format
- **record.xml**: Sample GEDCOM X record with extensions
- **sample.gedx**: Complete GEDCOM X archive file
- **cap-relationship-control.xml**: Expected XML output for relationships

### Base Test Class

All tests extend `Gedcomx\Tests\ApiTestCase`, which provides:

- Automatic setup and teardown
- Temporary directory management
- Test fixture loading helpers
- Common test utilities

## PHP Version Compatibility

### Tested Versions

The SDK is automatically tested on:

- PHP 7.4
- PHP 8.0
- PHP 8.1
- PHP 8.2
- PHP 8.3

### Compatibility Notes

**PHP 8.0+ Changes Addressed**:

- ✅ Explicit nullable type declarations (`?Type` instead of implicit null defaults)
- ✅ Return type declarations for interface implementations (ArrayAccess, Countable, IteratorAggregate)
- ✅ Modern PHPUnit assertions (removed deprecated `assertEqualXMLStructure`)

**Known External Deprecations**:

The `intervention/image` package (used only in tests for artifact generation) shows PHP 8+ deprecation warnings. These are outside our control and do not affect the SDK functionality.

## Continuous Integration

### GitHub Actions Workflow

The CI pipeline (`.github/workflows/ci.yml`) automatically:

1. Tests against all supported PHP versions
2. Validates composer.json and composer.lock
3. Runs the complete test suite
4. Generates code coverage reports
5. Uploads coverage to Coveralls (PHP 8.3 only)

### Running CI Locally

Simulate CI testing for a specific PHP version using Docker:

```bash
docker run --rm -v $(pwd):/app -w /app php:8.3-cli bash -c \
  "apt-get update && apt-get install -y git zip unzip && \
   php -r \"copy('https://getcomposer.org/installer', 'composer-setup.php');\" && \
   php composer-setup.php --install-dir=/usr/local/bin --filename=composer && \
   composer install --prefer-dist --no-progress && \
   vendor/bin/phpunit"
```

## Writing New Tests

### Best Practices

1. **Extend ApiTestCase**: Use the base test case for common functionality
2. **Use Fixtures**: Place test data files in `tests/files/`
3. **Clean Up**: Tests automatically clean up `tests/tmp/` after each run
4. **Assertions**: Use modern PHPUnit assertions
5. **Naming**: Test files should end with `Tests.php` (e.g., `PersonTests.php`)
6. **Namespace**: Use `Gedcomx\Tests\Unit` or `Gedcomx\Tests\Integration`

### Example Test

```php
<?php

namespace Gedcomx\Tests\Unit;

use Gedcomx\Tests\ApiTestCase;
use Gedcomx\Conclusion\Person;

class PersonTests extends ApiTestCase
{
    public function testPersonDeserialization()
    {
        $person = new Person($this->loadJson('person.json'));
        
        $this->assertEquals('PPPJ-MYZ', $person->getId());
        $this->assertCount(2, $person->getFacts());
        $this->assertCount(1, $person->getNames());
    }
}
```

## Troubleshooting

### Coverage Not Generated

If coverage reports aren't generated:

1. Install Xdebug:
   ```bash
   pecl install xdebug
   ```

2. Or install PCOV (faster):
   ```bash
   pecl install pcov
   ```

3. Verify installation:
   ```bash
   php -m | grep -i xdebug
   # or
   php -m | grep -i pcov
   ```

### Tests Failing on PHP 8+

If you see deprecation warnings or failures on PHP 8+:

1. Ensure you're using PHPUnit 9.5+ (check `composer.json`)
2. Verify explicit nullable types are used (`?Type` not `Type = null`)
3. Check that interface implementations have proper return types

### Permission Issues with tests/tmp/

Ensure the temporary directory is writable:

```bash
chmod 755 tests/tmp
```

## Contributing

When contributing new features:

1. Add unit tests for new functionality
2. Ensure all tests pass on all supported PHP versions
3. Maintain or improve code coverage
4. Follow existing test patterns and structure
5. Update this document if adding new test categories

## References

- [PHPUnit Documentation](https://phpunit.de/documentation.html)
- [GEDCOM X Specification](http://www.gedcomx.org)
- [FamilySearch API Documentation](https://www.familysearch.org/developers/)
