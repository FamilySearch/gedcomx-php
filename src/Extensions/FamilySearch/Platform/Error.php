<?php

    namespace Gedcomx\Extensions\FamilySearch\Platform;

    class Error
    {
        const JSON_IDENTIFIER = 'errors';

        private $code;
        private $label;
        private $message;
        private $stacktrace;
    }