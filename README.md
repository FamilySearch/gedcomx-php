# Welcome

GEDCOM X PHP is the PHP implementation of [GEDCOM X](http://www.gedcomx.org), including GEDCOM X extension projects.

[Read about how to get involved.](http://www.gedcomx.org/Community.html)

#Installation

GEDCOM X PHP uses [composer](https://getcomposer.org) to manage dependencies. To install, add the following to your compser.json file and then run the `composer install` command.

```json
{
    "require": {
        "gedcomx/gedcomx-php": "dev-master"
    }
}
```

# Serialize GEDCOM X
 
```php
use Gedcomx\Gedcomx;
use Gedcomx\Conclusion\Person;
use Gedcomx\Conclusion\Relationship;
use Gedcomx\Conclusion\Document;
use Gedcomx\Source\SourceDescription;
...


$gedcomx = new Gedcomx();
$person1 = new Person();
$relationship1 = new Relationship();
$document1 = new Document();
$source1 = new SourceDescription();
...
$gedcomx->setPersons(array(person1, ...));
$gedcomx->setRelationships(array(relationship1, ...));
$gedcomx->setDocuments(array(document1, ...));
$gedcomx->setSourceDescriptions(array(source1, ...));

$json = $gedcomx->toJson();
```

# Deserialize GEDCOM X
 
```php
use Gedcomx\Gedcomx;
use Gedcomx\Conclusion\Person;
use Gedcomx\Conclusion\Relationship;
use Gedcomx\Conclusion\Document;
use Gedcomx\Source\SourceDescription;


/**
 * @var string
 */
$json = ...;

/**
 * @var Gedcomx
 */
$gedcomx = new Gedcomx(json_decode($json, true));

/**
 * @var Person
 */
$person1 = $gedcomx->getPersons()[0];

/**
 * @var Relationship
 */
$relationship1 = $gedcomx->getRelationships()[0];

/**
 * @var Document
 */
$document1 = $gedcomx->getDocuments()[0];

/**
 * @var SourceDescription
 */
$source1 = $gedcomx->getSourceDescriptions()[0];

...
```

# Accessing a GEDCOM X RS API

## Read the Person for the Current User

```php
use Gedcomx\Rs\Api\StateFactory;
use Gedcomx\Rs\Api\PersonState;
use Gedcomx\Conclusion\Person;

/**
 * @var StateFactory
 */
$stateFactory = new StateFactory();

/**
 * @var PersonState
 */
$currentPerson = $stateFactory
    ->newCollectionState("https://sandbox.familysearch.org/platform/collections/tree") //read the collection
    ->authenticateViaOAuth2Password("username", "password", "client_id") //authenticate if needed
    ->readPersonForCurrentUser(); //read the person for the current user

/**
 * @var Person
 */
$person = $currentPerson->getPerson();
```


## Search a Collection for Persons

```php
use Gedcomx\Rs\Api\StateFactory;
use Gedcomx\Rs\Api\PersonSearchResultsState;
use Gedcomx\Conclusion\Person;

/**
 * @var StateFactory
 */
$stateFactory = new StateFactory();

/**
 * @var PersonSearchResultsState
 */
$searchResults = $stateFactory
    ->newCollectionState("https://sandbox.familysearch.org/platform/collections/tree") //read the collection
    ->authenticateViaOAuth2Password("username", "password", "client_id") //authenticate if needed
    ->searchForPersons('givenName:Israel surname:Heaton gender:M birthDate:1880'); //search for Israel Heaton, b. 1880

//process the results
```

