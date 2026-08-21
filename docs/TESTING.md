# Testing Guide

## Overview

This project uses PHPUnit 9.5+ for testing and supports PHP 7.4 through 8.3. All tests must pass on all supported PHP versions with zero deprecation warnings.

## Supported PHP Versions

- PHP 7.4
- PHP 8.0
- PHP 8.1
- PHP 8.2
- PHP 8.3

## Running Tests Locally

### Prerequisites

```bash
composer install
```

### Run All Tests

```bash
vendor/bin/phpunit
```

### Run Tests with Detailed Output

```bash
vendor/bin/phpunit --testdox
```

### Run Specific Test Suite

```bash
# Core model tests
vendor/bin/phpunit tests/unit/ConclusionModelsTests.php

# FamilySearch extension tests
vendor/bin/phpunit tests/unit/FamilySearchExtensionsTests.php

# Fixture validation tests
vendor/bin/phpunit tests/unit/FixtureValidationTests.php
```

## Test Coverage

### Running Coverage Reports Locally

Coverage report generation requires Xdebug or PCOV extension:

```bash
# Generate HTML coverage report
vendor/bin/phpunit --coverage-html build/coverage

# Generate Clover XML for CI
vendor/bin/phpunit --coverage-clover build/logs/clover.xml

# Generate text summary
vendor/bin/phpunit --coverage-text
```

### Coverage Baseline

**Baseline established:** April 2026  
**Test Count:** 64 tests, 142 assertions  
**Status:** ✅ 100% passing on PHP 7.4-8.3

The project maintains comprehensive test coverage for:

- ✅ **Core GEDCOM X models** (23 tests) - Person, Relationship, Fact, Name, Gender, Event, Document, PlaceDescription, etc.
- ✅ **Source models** (8 tests) - SourceDescription, SourceCitation, SourceReference, CitationField
- ✅ **Agent models** (7 tests) - Agent, Address, OnlineAccount
- ✅ **FamilySearch extensions** (16 tests) - ChildAndParentsRelationship, Discussion, Comment, User, etc.
- ✅ **File operations** (4 tests) - XML/JSON serialization, GEDCOMX archive operations
- ✅ **Fixture validation** (6 tests) - XML/JSON well-formedness, round-trip testing

**Detailed coverage breakdown:** See [TEST_COVERAGE.md](TEST_COVERAGE.md) for a complete list of tested models.

**Where to find coverage reports:**

- GitHub Actions CI: Coverage artifacts are uploaded on every push to `master` and for all pull requests
- Coveralls.io: [![Coverage Status](https://coveralls.io/repos/FamilySearch/gedcomx-php/badge.svg?branch=master&service=github)](https://coveralls.io/github/FamilySearch/gedcomx-php?branch=master)
- CI runs coverage on PHP 8.3 only to optimize build time

## Continuous Integration

Tests run automatically via GitHub Actions on:

- Every push to `master`
- Every pull request targeting `master`
- All supported PHP versions in parallel

### CI Workflow

The CI pipeline (`.github/workflows/ci.yml`) runs:

1. `composer validate --strict` - Validates composer.json and composer.lock
2. `composer install --prefer-dist --no-progress` - Installs dependencies
3. `vendor/bin/phpunit` - Runs the full test suite
4. Coverage generation (PHP 8.3 only) and upload to Coveralls

### Viewing CI Results

- GitHub Actions: https://github.com/FamilySearch/gedcomx-php/actions
- README badge shows current master status
- PR checks must pass before merging

## Test Structure

### Test Organization

```
tests/
├── bootstrap.php              # Test bootstrap (converts deprecations to exceptions)
├── files/                     # Test fixtures (XML, JSON, GEDX files)
├── unit/                      # Unit tests
│   ├── ConclusionModelsTests.php       # Core GEDCOM X model tests
│   ├── FamilySearchExtensionsTests.php # FamilySearch extension tests
│   ├── FixtureValidationTests.php      # Fixture validation tests
│   ├── GedcomxFileTests.php            # GEDCOMX file operation tests
│   ├── PersonTests.php                 # Person model tests
│   └── XMLTests.php                    # XML deserialization tests
└── tmp/                       # Temporary files (gitignored)
```

### Test Fixtures

Test fixtures are located in `tests/files/`:

- **XML fixtures**: GEDCOM X XML documents
- **JSON fixtures**: GEDCOM X JSON documents  
- **GEDX files**: ZIP-based GEDCOM X archives

All fixtures are validated for well-formedness as part of the test suite.

## Writing Tests

### Test Naming Convention

- Test files must end with `Tests.php` (plural)
- Test methods must start with `test`
- Use descriptive names: `testPersonWithMultipleFacts()`

### Test Coverage Requirements

New features should include tests for:

1. **Construction**: Object can be created with various inputs
2. **Getters/Setters**: All public properties are accessible
3. **JSON Serialization**: Model serializes to valid JSON
4. **JSON Deserialization**: JSON deserializes to correct model
5. **Round-trip**: Serialize → Deserialize → Serialize produces same result
6. **XML Serialization**: Model serializes to valid XML (where applicable)
7. **XML Deserialization**: XML deserializes to correct model (where applicable)

### Example Test

```php
public function testPersonWithName()
{
    $person = new Person();
    
    $nameForm = new NameForm();
    $nameForm->setFullText('John Smith');
    
    $name = new Name();
    $name->setNameForms([$nameForm]);
    
    $person->setNames([$name]);
    
    $this->assertCount(1, $person->getNames());
    $this->assertEquals('John Smith', $person->getNames()[0]->getNameForms()[0]->getFullText());
    
    // Test JSON round-trip
    $json = $person->toJson();
    $person2 = new Person(json_decode($json, true));
    $this->assertEquals('John Smith', $person2->getNames()[0]->getNameForms()[0]->getFullText());
}
```

## Strict Testing Mode

The test suite runs in strict mode to ensure code quality:

- **failOnWarning="true"**: PHPUnit fails on any warning
- **Deprecation handling**: `tests/bootstrap.php` converts E_DEPRECATED to exceptions
- **No suppressed errors**: All errors must be fixed, not suppressed

This ensures that:
- Code works correctly on all PHP versions
- No deprecation warnings on PHP 8.x
- Issues are caught early in development

## Troubleshooting

### "No code coverage driver available"

Install Xdebug or PCOV:

```bash
# macOS with Homebrew
pecl install xdebug

# Ubuntu/Debian
sudo apt-get install php-xdebug
```

### Tests fail with deprecation warnings

1. Check which PHP version introduced the deprecation
2. Fix the underlying issue (don't suppress the warning)
3. Verify tests pass on all supported PHP versions

### "Class not found" errors

```bash
composer dump-autoload
```

### Test fixtures not found

Ensure `tests/files/` directory exists with fixture files. Check paths in test assertions.

## Best Practices

1. **Run tests before committing**: `vendor/bin/phpunit`
2. **Test on multiple PHP versions** if making significant changes
3. **Keep tests fast**: Current suite runs in < 1 second
4. **Don't commit generated files**: `.phpunit.result.cache` and `build/` are gitignored
5. **Update this document** when adding new test infrastructure

## Questions or Issues?

- Open an issue: https://github.com/FamilySearch/gedcomx-php/issues
- Check CI logs for detailed error output
- Review existing tests for examples
