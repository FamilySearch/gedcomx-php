<?php

error_reporting(E_ALL);

// Disable garbage collection
// https://scrutinizer-ci.com/blog/composer-gc-performance-who-is-affected-too
gc_disable();

require __DIR__ . '/../vendor/autoload.php';
