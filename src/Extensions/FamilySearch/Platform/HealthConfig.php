<?php

    namespace Gedcomx\Extensions\FamilySearch\Platform;

    class HealthConfig
    {
        const JSON_IDENTIFIER = 'healthConfig';

        private $databaseVersion;
        private $buildVersion;
        private $buildDate;
        private $platformVersion;
    }