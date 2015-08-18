  

#GedcomX - PHP  [![Build Status](https://travis-ci.org/FamilySearch/gedcomx-php.svg?branch=master)](https://travis-ci.org/FamilySearch/gedcomx-php) [![Packagist](https://img.shields.io/packagist/v/gedcomx/gedcomx-php.svg)](https://packagist.org/packages/gedcomx/gedcomx-php)

The gedcomx-php SDK is the PHP implementation of [GEDCOM X](http://www.gedcomx.org), including GEDCOM X extension projects. 

The SDK includes a [tutorial](https://github.com/FamilySearch/gedcomx-php/wiki), a [gedcomx-php-sample-app](https://github.com/FamilySearch/gedcomx-php-sample-app) Github project, and the [gedcomx-php SDK Documentation](http://familysearch.github.io/gedcomx-php/index.html).

You can [run the sample app](http://gedcomx-php-sample-app.herokuapp.com) to demonstrate basic functionality and usage of the gedcomx-php SDK in action.

##See [Installing the SDK](https://github.com/FamilySearch/gedcomx-php/wiki/Installation)

The SDK includes the following features:
-
* **GEDCOM X Conceptual Model**

  Implementaton of the [GEDCOM X Conceptual Model](https://github.com/FamilySearch/gedcomx/blob/master/specifications/conceptual-model-specification.md) in a rich set of PHP Classes with getter and setter methods. Take a look at a [visual graph](https://github.com/FamilySearch/gedcomx/blob/master/specifications/support/conceptual-model-graph.pdf) of the GEDCOM X Conceptual Model.

* **GEDCOM X Serialization**

  XML and JSON serialization and deserialization of GEDCOM X. For more information, see the [examples](https://github.com/FamilySearch/gedcomx-php/wiki/GEDCOM-X-Serialization). 

* **GEDCOM X RS Client**
  
  Functionality to interact with a [GEDCOM X RS](https://github.com/FamilySearch/gedcomx-rs/blob/master/specifications/rs-specification.md) compliant web service. GEDCOM X RS is a RESTful specification that defines a set of [application states](https://github.com/FamilySearch/gedcomx-rs/blob/master/specifications/rs-specification.md#4-application-states) for a genealogical data application. This allows you to read the state of a Person, Relationship, Source Description, or other state. Take a look as some [examples](https://github.com/FamilySearch/gedcomx-php/wiki/Accessing-a-GEDCOM-X-RS-API) of establishing a RESTful state.

* **FamilySearch API Interface**
  
  A robust mechanism for interacting with the [FamilySearch API](https://familysearch.org/developers/docs/api/resources). The [FamilySearchClient](http://familysearch.github.io/gedcomx-php/class-Gedcomx.Extensions.FamilySearch.Rs.Client.FamilySearchClient.html) class makes it easy to set the configuration to the appropriate API environment (Sandbox, Beta, Production), authenticate using OAuth2, and interact with the FamilySearch Family Tree and other services.


# Changelog

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
