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

- **Test Files**: 28 test suites
- **Test Cases**: 99 tests with 237 assertions
- **Source Files**: 125+ PHP files
- **Core Models**: Complete GEDCOM X conceptual model class coverage
- **Extensions**: Comprehensive FamilySearch platform extension coverage

### Coverage Areas

#### Well-Covered Components (as of v3.2.0)

- **GEDCOM X file format**: .gedx reading and writing, XML serialization/deserialization
- **Core conclusion models**: Person, Gender, Fact, Name, NameForm, NamePart, Event, EventRole, Relationship, Document, PlaceDescription, PlaceReference, DateInfo, Identifier
- **Agent models**: Agent, Address, OnlineAccount
- **Source models**: SourceDescription, SourceReference, SourceCitation, Coverage, CitationField
- **FamilySearch extensions**: User, Discussion, Comment, DiscussionReference, ChildAndParentsRelationship, ChangeInfo, MatchInfo, Merge, MergeAnalysis, MergeConflict, ArtifactMetadata

#### Areas for Future Expansion

Additional test coverage could be added for:

1. **Abstract/Base Classes**
   - Subject and Conclusion base classes
   - HasFacts trait
   - DisplayProperties models

2. **Serialization Edge Cases**
   - JSON serialization for all models (currently only XML is comprehensively tested)
   - Malformed data handling
   - Namespace handling for extensions
   - Large file performance

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

- **Core model fixtures**: person.json, gender.json, fact.json, name.json, date-info.json, identifier.json, name-form.json, name-part.json, event.json, event-role.json, relationship.json, document.json, place-description.json, place-reference.json
- **Agent fixtures**: agent.json, address.json, online-account.json
- **Source fixtures**: source-description.json, source-citation.json, coverage.json, citation-field.json
- **FamilySearch extension fixtures**: user.json, discussion.json, comment.json, change-info.json, match-info.json, merge.json
- **XML fixtures**: record.xml, cap-relationship-control.xml
- **Archive files**: sample.gedx
- **Test images**: test-image.jpg (for GEDX file tests)

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
- ✅ Modern PHPUnit assertions (replaced deprecated `assertEqualXMLStructure` with `assertXmlStringEqualsXmlString`)
- ✅ PHPUnit configured with `failOnWarning="true"` to catch warnings and risky tests
- ✅ **Zero deprecations enforced** through multiple layers:
  1. **Removed deprecated API usage**: No deprecated PHPUnit assertions in test suite
  2. **Error handler safety net**: `tests/bootstrap.php` converts PHP-level E_DEPRECATED to exceptions
  3. **CI validation**: GitHub Actions tests on PHP 7.4-8.3 catch version-specific deprecations
  - Combined approach ensures acceptance criteria "No deprecation warnings in test output"

**External Dependencies**:

Test fixtures are used instead of runtime image generation, eliminating external dependencies and ensuring consistent test behavior across all PHP versions.

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
