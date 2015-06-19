<?php

require __DIR__ . '/../vendor/autoload.php';

\VCR\VCR::configure()->enableRequestMatchers(array('method','url','body'));
\VCR\VCR::configure()->setMode('once');
