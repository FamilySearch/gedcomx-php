<?php

error_reporting(E_ALL);

// Disable garbage collection
// https://scrutinizer-ci.com/blog/composer-gc-performance-who-is-affected-too
gc_disable();

require __DIR__ . '/../vendor/autoload.php';

\VCR\VCR::configure()->addRequestMatcher('custom_headers', function(\VCR\Request $first, \VCR\Request $second){
    $firstHeaders = $first->getHeaders();
    $secondHeaders = $second->getHeaders();
    unset($firstHeaders['User-Agent']);
    unset($secondHeaders['User-Agent']);
    return count(array_diff_assoc($firstHeaders, $secondHeaders)) === 0;
});
\VCR\VCR::configure()->enableRequestMatchers(array('method','url','query_string','body','custom_headers'));
\VCR\VCR::configure()->setMode('once');
\VCR\VCR::configure()->setWhiteList(array('vendor/guzzlehttp'));
