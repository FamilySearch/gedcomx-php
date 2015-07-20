#GedcomX - PHP

[![Build Status](https://travis-ci.org/FamilySearch/gedcomx-php.svg?branch=master)](https://travis-ci.org/FamilySearch/gedcomx-php) [![Packagist](https://img.shields.io/packagist/v/gedcomx/gedcomx-php.svg)](https://packagist.org/packages/gedcomx/gedcomx-php)

#Installing the gedcomx-php SDK
## Prerequisites

Prior to installing the gedcomx-php SDK you must install the following components.

* [PHP 5.4+](http://php.net/manual/en/install.php) (The language of this SDK)
  * *You can install PHP using pre-built environments such as [WAMP Server](http://www.wampserver.com/) (Windows), [XAMPP](https://www.apachefriends.org/) (Windows), [MAMP](https://www.mamp.info/) (OS X), or [Vagrant](http://vagrantup.com/) (Any OS).*

* [Git](http://git-scm.com/downloads) (Versioning system for coding)
* [Composer](https://getcomposer.org/doc/00-intro.md) (PHP Packages Manager)

**Note:** Mac developers might need to install the Xcode developer tools as part of this process.

You can verify that each of the required components is installed by running the following commands one line at a time, at your command line or command prompt:
```
php -v
composer --version
git --version
```

## Installation Steps

Gedcomx-php uses [composer](https://getcomposer.org) to manage dependencies. These instructions assume that you have installed [Composer globally](https://getcomposer.org/doc/00-intro.md#globally). 

1.  To activate Composer, run the following command from the root of your project:

    ```
    composer require gedcomx/gedcomx-php
    ```

    Alternatively, you can add the following configuration to an existing composer.json file then run the `composer install` command from your command prompt.

    ```json
    {
        "require": {
        "gedcomx/gedcomx-php": "^1.1"
        }
    }
    ```

2. If you are not already using other Composer managed libraries, add the [Composer Autoloader](https://getcomposer.org/doc/01-basic-usage.md#autoloading) to your project.

    ```php
    require 'vendor/autoload.php';
    ```

3. Import desired classes by using the `use` operator:

    ```php
    use Gedcomx\Extensions\FamilySearch\Rs\Client\FamilySearchClient;
    ```

# Overview

The gedcomx-php SDK is the PHP implementation of [GEDCOM X](http://www.gedcomx.org), including GEDCOM X extension projects. The gedcomx-php SDK library has the following features:

* **GEDCOM X Conceptual Model**
  
  The gedcomx-php library implements the [GEDCOM X Conceptual Model](https://github.com/FamilySearch/gedcomx/blob/master/specifications/conceptual-model-specification.md) in a rich set of PHP Classes with getter and setter methods. A visual graph of the GEDCOM X Conceptual Model is [found here](https://github.com/FamilySearch/gedcomx/blob/master/specifications/support/conceptual-model-graph.pdf).

* **GEDCOM X Serialization**

  This library supports XML and JSON serialization and deserialization of GEDCOM X. For more information, check out the [examples](https://github.com/FamilySearch/gedcomx-php/wiki/GEDCOM-X-Serialization). 

* **GEDCOM X RS Client**
  
  The gedcomx-php library provides functionality to interact with a [GEDCOM X RS](https://github.com/FamilySearch/gedcomx-rs/blob/master/specifications/rs-specification.md) compliant web service. GEDCOM X RS is a RESTful specification that defines a set of [application states](https://github.com/FamilySearch/gedcomx-rs/blob/master/specifications/rs-specification.md#4-application-states) for a genealogical data application. This allows you to read the state of a Person, Relationship, Source Description, etc. For more information, check out the [examples](https://github.com/FamilySearch/gedcomx-php/wiki/Accessing-a-GEDCOM-X-RS-API).

* **FamilySearch SDK**
  
  This library provides a robust mechanism for interacting with the [FamilySearch API](https://familysearch.org/developers/). The FamilySearchClient class provides a nice mechanism to easily set the configuration to the appropriate API environment (Sandbox, Beta, Production), authenticate using OAuth2, and interact with the FamilySearch Family Tree and other services.

* **Sample App**

  The [gedcomx-php-sample-app](https://github.com/FamilySearch/gedcomx-php-sample-app) project demonstrates basic functionality and usage of the FamilySearch SDK. You can clone the [project from Github](https://github.com/FamilySearch/gedcomx-php-sample-app) and see the app in action [here](http://gedcomx-php-sample-app.herokuapp.com).

# Documentation

Documentation for the gedcomx-php library is provided in the following forms:

* [Wiki](https://github.com/FamilySearch/gedcomx-php/wiki): the wiki provides guides on how to use the gedcomx-php library.
* [API Doc](http://familysearch.github.io/gedcomx-php/docs/index.html): the API Doc contains documentation for all of the namespaces, classes, and methods contained in the SDK.
* [Sample App](https://github.com/FamilySearch/gedcomx-php-sample-app): the sample app provides running code examples for basic operations of the FamilySearch API.

# Changelog

* v1.1.1: 
  * Fix bugs in the FamilySearchClient class

* v1.1.0: 
  * Introduce the FamilySearchClient
  * Fix automated tests
  * Improve runtime of automated tests with php-vcr
  * Remove apache/log4php dependency

* v1.0.0:
  * Initial stable build to enable Composer installation by version number.
