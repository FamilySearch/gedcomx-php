# GedcomX - PHP SDK  

[![Packagist](https://img.shields.io/packagist/v/gedcomx/gedcomx-php.svg)](https://packagist.org/packages/gedcomx/gedcomx-php)
[![Build Status](https://travis-ci.org/FamilySearch/gedcomx-php.svg?branch=master)](https://travis-ci.org/FamilySearch/gedcomx-php)
[![Coverage Status](https://coveralls.io/repos/FamilySearch/gedcomx-php/badge.svg?branch=master&service=github)](https://coveralls.io/github/FamilySearch/gedcomx-php?branch=master)
[![Dependency Status](https://www.versioneye.com/user/projects/5633c23236d0ab0016001f02/badge.svg?style=flat)](https://www.versioneye.com/user/projects/5633c23236d0ab0016001f02)

The PHP implementation of [GEDCOM X](http://www.gedcomx.org), including GEDCOM X extension projects. 
The library only provides classes for serialization and deserialization of GEDCOM X
data and FamilySearch extensions. See [gedcomx-php-client](https://github.com/FamilySearch/gedcomx-php-client)
for the FamilySearch API PHP SDK.

## Installation

### Prerequisites

Prior to installing the GedcomX PHP SDK you must install the following components.


* [Composer](https://getcomposer.org/doc/00-intro.md) (PHP Packages Manager)

    **Notes to Mac Developers:** 
  * Mac developers might need to install the Xcode developer tools as part of this process. 
  * Mac developers may need to set the `date.timezone` in /etc/php.ini to avoid seeing errors. See [PHP time zones](http://php.net/manual/en/timezones.php) to locate your time zone. For example, 
  ```
    `date.timezone = "America/Denver"` 
  ```

You can verify that each of the required components is installed by running the following commands one line at a time, at your command line or command prompt:
```
php -v
composer --version
git --version
```

### Installation Steps

**NOTE:** You only need to install the SDK one time for each PHP project you set up. 

The GedcomX PHP SDK uses [composer](https://getcomposer.org) to manage dependencies. These instructions assume that you have installed [Composer globally](https://getcomposer.org/doc/00-intro.md#globally).

Do **one** of the following steps to activate Composer and install the gedcomx-php SDK library:

* Run the following command at a command prompt from the root of your project:

    ```
    composer require gedcomx/gedcomx-php
    ```

* Add the following configuration to your composer.json file then run the `composer install` command at your command prompt.

    ```json
    {
        "require": {
        "gedcomx/gedcomx-php": "^1.1"
        }
    }
    ```

## Features

* **GEDCOM X Conceptual Model**

  Implementaton of the [GEDCOM X Conceptual Model](https://github.com/FamilySearch/gedcomx/blob/master/specifications/conceptual-model-specification.md) in a rich set of PHP Classes with getter and setter methods. Take a look at a [visual graph](https://github.com/FamilySearch/gedcomx/blob/master/specifications/support/conceptual-model-graph.pdf) of the GEDCOM X Conceptual Model.

* **GEDCOM X Serialization**

  XML and JSON serialization and deserialization of GEDCOM X. For more information, see the [examples](https://github.com/FamilySearch/gedcomx-php/wiki/GEDCOM-X-Serialization). 

## Changelog

* v3.0.0
  * Split out the API networking code into [gedcomx-php-client](https://github.com/FamilySearch/gedcomx-php-client)

* v2.3.0
  * Add the `generateClientSecret()` method to `GedcomxApplicationState`.

* v2.2.0
  * Add `logout()` method to application states.

* v2.1.1
  * Support throttling. Enable in `FamilySearchClient` by setting the `throttling` option to `true`.

* v2.0.1
  * Fix `FamilySearchClient` so that it automatically follows redirects.

* v2.0.0
  * Upgrade to Guzzle 6 which makes PHP 5.5 the minimum supported version.
  * `$state->getRequest()` returns a `GuzzleHttp\Psr7\Request`
  * `$state->getResponse()` returns a `GuzzleHttp\Psr7\Response`
  * Add a new `httpExceptions` configuration parameter on `FamilySearchClient` that causes an exception to be thrown when a 4xx or 5xx response is received from the API.

* v1.2.0
  * Add a custom user agent string when using the FamilySearchClient.
  * Register a [PSR-3](http://www.php-fig.org/psr/psr-3/) logger when using the FamilySearchClient.
  * Add a `setAccessToken()` method to the FamilySearchClient class.
  * Add `getPerson()` method to the PersonsState. Change PersonParentsState, PersonSpousesState, and PersonChildrenState to extend PersonsState.
  * Add `getStatus()` method to the FamilySearchClient class.

* v1.1.1: 
  * Fix bugs in the FamilySearchClient class

* v1.1.0: 
  * Introduce the FamilySearchClient
  * Fix automated tests
  * Improve runtime of automated tests with php-vcr
  * Remove apache/log4php dependency

* v1.0.0:
  * Initial stable build to enable Composer installation by version number.
