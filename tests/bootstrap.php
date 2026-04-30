<?php

error_reporting(E_ALL);

// Convert deprecation warnings to exceptions so CI fails fast
set_error_handler(function ($errno, $errstr, $errfile, $errline) {
    if ($errno === E_DEPRECATED || $errno === E_USER_DEPRECATED) {
        throw new \ErrorException($errstr, 0, $errno, $errfile, $errline);
    }
    return false; // Let other error handlers handle non-deprecation errors
});

// Disable garbage collection
// https://scrutinizer-ci.com/blog/composer-gc-performance-who-is-affected-too
gc_disable();

require __DIR__ . '/../vendor/autoload.php';
