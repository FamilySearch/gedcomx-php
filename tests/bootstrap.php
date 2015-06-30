<?php

require __DIR__ . '/../vendor/autoload.php';

// Disable garbage collection
// https://scrutinizer-ci.com/blog/composer-gc-performance-who-is-affected-too
gc_disable();

\VCR\VCR::configure()->enableRequestMatchers(array('method','url','query_string','body'));
\VCR\VCR::configure()->setMode('once');
\VCR\VCR::configure()->setWhiteList(array('vendor/guzzle'));