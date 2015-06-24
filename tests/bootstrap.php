<?php

require __DIR__ . '/../vendor/autoload.php';

gc_disable();

\VCR\VCR::configure()->enableRequestMatchers(array('method','url','query_string','body'));
\VCR\VCR::configure()->setMode('once');
