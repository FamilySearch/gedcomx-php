# Security Policy

## Supported Versions

We actively support the following versions of gedcomx-php with security updates:

| Version | PHP Versions | Supported |
| ------- |--------------| --------- |
| 4.x     | 7.4+         | ✅        |
| 3.x     | 7.4+         | ❌        |
| 2.x     | 5.5+         | ❌        |
| 1.x     | 5.3+         | ❌        |

## Reporting a Vulnerability

The FamilySearch team takes security vulnerabilities seriously. We appreciate your efforts to responsibly disclose your findings.

### How to Report

**Please do NOT report security vulnerabilities through public GitHub issues.**

Instead, please report security vulnerabilities by email to:

**devsupport@familysearch.org**

### What to Include

Please include the following information in your report:

- **Description**: A clear description of the vulnerability
- **Impact**: What could an attacker accomplish by exploiting this vulnerability?
- **Steps to Reproduce**: Detailed steps to reproduce the vulnerability
- **Proof of Concept**: Code snippets, screenshots, or example payloads (if applicable)
- **Affected Versions**: Which versions of gedcomx-php are affected
- **Suggested Fix**: If you have suggestions for how to fix the vulnerability (optional)
- **Your Contact Information**: So we can follow up with questions or updates

### What to Expect

After you submit a vulnerability report, you can expect:

1. **Acknowledgment**: We will acknowledge receipt of your report within 3 business days
2. **Assessment**: We will investigate and assess the severity of the issue
3. **Updates**: We will keep you informed of our progress
4. **Resolution**: We will work on a fix and coordinate disclosure timing with you
5. **Credit**: With your permission, we will credit you in the security advisory

### Security Update Process

When a security vulnerability is confirmed:

1. We will develop and test a fix
2. We will release a security patch as soon as possible
3. We will publish a security advisory on GitHub
4. We will update this SECURITY.md file if needed

## Security Best Practices

When using gedcomx-php in your applications:

### Input Validation

- **Validate all input data** before deserializing JSON or XML
- **Sanitize user-provided strings** before including them in GEDCOM X documents
- **Limit file sizes** when processing GEDCOMX archives (.gedx files)
- **Validate file types** before processing uploaded files

### XML External Entity (XXE) Prevention

When parsing XML with gedcomx-php:

```php
// Disable external entity loading to prevent XXE attacks
libxml_disable_entity_loader(true);

// Use LIBXML_NONET to disable network access during XML parsing
$xml = simplexml_load_string($xmlString, 'SimpleXMLElement', LIBXML_NONET);
```

### Dependency Management

- **Keep dependencies up to date**: Run `composer update` regularly
- **Monitor security advisories**: Use `composer audit` to check for known vulnerabilities
- **Use specific version constraints**: Avoid using `*` or overly broad version ranges

```bash
# Check for known security vulnerabilities
composer audit

# Update dependencies
composer update
```

### Data Handling

- **Never store sensitive data in plain text**: Use encryption for sensitive genealogical data
- **Implement access controls**: Ensure proper authentication and authorization
- **Validate deserialized objects**: Don't trust data from untrusted sources
- **Use HTTPS**: Always transmit GEDCOM X data over secure connections

### File Operations

When working with GEDCOMX archives:

```php
// Validate archive before extraction
$zip = new ZipArchive();
if ($zip->open($gedxFile) === true) {
    // Check for zip bombs (excessive compression ratios)
    $uncompressedSize = 0;
    for ($i = 0; $i < $zip->numFiles; $i++) {
        $stat = $zip->statIndex($i);
        $uncompressedSize += $stat['size'];
    }
    
    // Reject if uncompressed size exceeds reasonable limit
    if ($uncompressedSize > 100 * 1024 * 1024) { // 100 MB limit
        throw new Exception('Archive too large');
    }
    
    // Validate file paths to prevent directory traversal
    for ($i = 0; $i < $zip->numFiles; $i++) {
        $name = $zip->getNameIndex($i);
        if (strpos($name, '../') !== false || strpos($name, '..\\') !== false) {
            throw new Exception('Invalid file path in archive');
        }
    }
}
```

## Vulnerability Disclosure Policy

We follow a coordinated disclosure process:

1. **Private Disclosure**: Security researchers report vulnerabilities privately
2. **Fix Development**: We develop and test fixes privately
3. **Coordinated Release**: We coordinate with the reporter on disclosure timing
4. **Public Disclosure**: We publish security advisories after fixes are released

We typically aim for a 90-day disclosure timeline, but this may be adjusted based on the severity and complexity of the issue.

## Known Security Considerations

### Deserialization

The library deserializes JSON and XML data into PHP objects. When deserializing data from untrusted sources:

- Validate the structure and content before deserialization
- Set appropriate memory limits to prevent resource exhaustion
- Implement request timeouts to prevent denial-of-service

### Archive Processing

GEDCOMX archives (.gedx files) are ZIP files that may contain multiple resources:

- Validate ZIP structure before extraction
- Check for zip bombs (files with extreme compression ratios)
- Prevent directory traversal attacks during extraction
- Limit total extracted size

## Contact

For non-security issues, please use:
- **Issues**: https://github.com/FamilySearch/gedcomx-php/issues
- **Documentation**: https://github.com/FamilySearch/gedcomx-php

For security vulnerabilities, email: **devsupport@familysearch.org**

## Acknowledgments

We would like to thank the security researchers who have responsibly disclosed vulnerabilities to us. Your contributions help keep the genealogical community safe.

---

**Last Updated**: 2026-08-25
