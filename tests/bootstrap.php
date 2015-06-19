<?php

require __DIR__ . '/../vendor/autoload.php';

\VCR\VCR::configure()->enableRequestMatchers(array('method','url'));
\VCR\VCR::configure()->setMode('once');