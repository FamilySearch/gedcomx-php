<?php

error_reporting(E_ALL);

// Convert PHP-level deprecations to exceptions during tests to ensure they fail the build.
// This is a safety net that catches E_DEPRECATED from PHP core or dependencies.
// Note: This does NOT catch all PHPUnit-internal deprecation reporting, so the primary
// defense is removing deprecated API usage (e.g., assertEqualXMLStructure replaced with
// assertXmlStringEqualsXmlString). This handler provides an additional layer of protection.
set_error_handler(function ($severity, $message, $file, $line) {
    if ($severity === E_DEPRECATED || $severity === E_USER_DEPRECATED) {
        throw new \ErrorException($message, 0, $severity, $file, $line);
    }
    // Let other errors pass through to default handler
    return false;
});

// Disable garbage collection
// https://scrutinizer-ci.com/blog/composer-gc-performance-who-is-affected-too
gc_disable();

require __DIR__ . '/../vendor/autoload.php';
