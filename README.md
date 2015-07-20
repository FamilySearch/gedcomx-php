

#GedcomX - PHP  [![Build Status](https://travis-ci.org/FamilySearch/gedcomx-php.svg?branch=master)](https://travis-ci.org/FamilySearch/gedcomx-php) [![Packagist](https://img.shields.io/packagist/v/gedcomx/gedcomx-php.svg)](https://packagist.org/packages/gedcomx/gedcomx-php)

The gedcomx-php SDK is the PHP implementation of [GEDCOM X](http://www.gedcomx.org), including GEDCOM X extension projects. The gedcomx-php SDK has the following features:

* **Sample App**

  The [gedcomx-php-sample-app](https://github.com/FamilySearch/gedcomx-php-sample-app) Github project. This project is a sample app that demonstrates basic functionality and usage of the gedcomx-php SDK. You can [run the app](http://gedcomx-php-sample-app.herokuapp.com) to see it in action.

* **GEDCOM X Conceptual Model**

  Implementaton of the [GEDCOM X Conceptual Model](https://github.com/FamilySearch/gedcomx/blob/master/specifications/conceptual-model-specification.md) in a rich set of PHP Classes with getter and setter methods. Take a look at a [visual graph](https://github.com/FamilySearch/gedcomx/blob/master/specifications/support/conceptual-model-graph.pdf) of the GEDCOM X Conceptual Model.

* **GEDCOM X Serialization**

  XML and JSON serialization and deserialization of GEDCOM X. For more information, see the [examples](https://github.com/FamilySearch/gedcomx-php/wiki/GEDCOM-X-Serialization). 

* **GEDCOM X RS Client**
  
  Functionality to interact with a [GEDCOM X RS](https://github.com/FamilySearch/gedcomx-rs/blob/master/specifications/rs-specification.md) compliant web service. GEDCOM X RS is a RESTful specification that defines a set of [application states](https://github.com/FamilySearch/gedcomx-rs/blob/master/specifications/rs-specification.md#4-application-states) for a genealogical data application. This allows you to read the state of a Person, Relationship, Source Description, or other state. Take a look as some [examples](https://github.com/FamilySearch/gedcomx-php/wiki/Accessing-a-GEDCOM-X-RS-API) of establishing a RESTful state.

* **FamilySearch API Interface**
  
  A robust mechanism for interacting with the [FamilySearch API](https://familysearch.org/developers/docs/api/resources). The FamilySearchClient class makes it easy to set the configuration to the appropriate API environment (Sandbox, Beta, Production), authenticate using OAuth2, and interact with the FamilySearch Family Tree and other services.

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

The gedcomx-php SDK uses [composer](https://getcomposer.org) to manage dependencies. These instructions assume that you have installed [Composer globally](https://getcomposer.org/doc/00-intro.md#globally). 

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
    **Note:** You must first instantiate a FamilySearch client with an access token before initiating any FamilySearch API access. See the [session setup code](https://github.com/FamilySearch/gedcomx-php-sample-app/blob/master/src/includes/setup.php).

# Documentation

Documentation for the gedcomx-php SDK is provided in the following forms:

* The [wiki](https://github.com/FamilySearch/gedcomx-php/wiki) provides instruction on how to use the gedcomx-php SDK in a production environment.
* The [gedcomx-php SDK Doc](http://familysearch.github.io/gedcomx-php/docs/index.html) contains documentation for all the namespaces, classes, and methods contained in the SDK.


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
