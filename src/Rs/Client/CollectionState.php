<?php


namespace Gedcomx\Rs\Client;

use Gedcomx\Common\ResourceReference;
use Gedcomx\Conclusion\Fact;
use Gedcomx\Conclusion\Relationship;
use Gedcomx\Gedcomx;
use Gedcomx\Conclusion\Person;
use Gedcomx\Records\Collection;
use Gedcomx\Rs\Client\Exception\GedcomxApplicationException;
use Gedcomx\Rs\Client\Options\StateTransitionOption;
use Gedcomx\Rs\Client\Util\DataSource;
use Gedcomx\Rs\Client\Util\GedcomxPersonSearchQueryBuilder;
use Gedcomx\Source\SourceDescription;
use Gedcomx\Types\RelationshipType;
use Gedcomx\Util\MediaType;
use Guzzle\Http\Client;
use Guzzle\Http\Message\EntityEnclosingRequest;
use Guzzle\Http\Message\Request;
use Guzzle\Http\Message\Response;
use RuntimeException;

/**
 * The CollectionState is a collection of resources and exposes management of those resources.
 *
 * Class CollectionState
 *
 * @package Gedcomx\Rs\Client
 */
class CollectionState extends GedcomxApplicationState
{
    /**
     * Constructs a new collection state using the specified client, request, response, access token, and state factory.
     *
     * @param \Guzzle\Http\Client             $client
     * @param \Guzzle\Http\Message\Request    $request
     * @param \Guzzle\Http\Message\Response   $response
     * @param string                          $accessToken
     * @param \Gedcomx\Rs\Client\StateFactory $stateFactory
     */
    function __construct(Client $client, Request $request, Response $response, $accessToken, StateFactory $stateFactory)
    {
        parent::__construct($client, $request, $response, $accessToken, $stateFactory);
    }

    /**
     * Clones the current state instance.
     *
     * @param \Guzzle\Http\Message\Request  $request
     * @param \Guzzle\Http\Message\Response $response
     *
     * @return \Gedcomx\Rs\Client\CollectionState
     */
    protected function reconstruct(Request $request, Response $response)
    {
        return new CollectionState($this->client, $request, $response, $this->accessToken, $this->stateFactory);
    }

    /**
     * Returns the entity from the REST API response.
     *
     * @return \Gedcomx\Gedcomx
     */
    protected function loadEntity()
    {
        $json = json_decode($this->response->getBody(), true);
        return new Gedcomx($json);
    }

    /**
     * Gets the main data element represented by this state instance.
     *
     * @return \Gedcomx\Records\Collection|null
     */
    protected function getScope()
    {
        return $this->getCollection();
    }

    /**
     * Gets the first collection from the current list of collections (in the entity).
     *
     * @return Collection|null
     */
    public function getCollection()
    {
        if ($this->entity) {
            $collections = $this->entity->getCollections();
            if (count($collections) > 0) {
                return $collections[0];
            }
        }

        return null;
    }

    /**
     * Updates the specified collection.
     *
     * @param Collection $collection
     * @param StateTransitionOption $options,...
     * @return CollectionState|null
     */
    public function update(Collection $collection, StateTransitionOption $options = null)
    {
        $gx = new Gedcomx();
        $gx->setCollections(array($collection));
        return $this->post($gx, $options);
    }

    /**
     * Reads records from this collection.
     *
     * @param \Gedcomx\Rs\Client\Options\StateTransitionOption $options,...
     *
     * @return \Gedcomx\Rs\Client\RecordsState|null
     */
    public function readRecords(StateTransitionOption $options = null)
    {
        $link = $this->getLink(Rel::RECORDS);
        if ($link == null || $link->getHref() == null)
        {
            return null;
        }

        $request = $this->createAuthenticatedGedcomxRequest(Request::GET, $link->getHref());
        return $this->stateFactory->createState(
            "RecordsState",
            $this->client,
            $request,
            $this->passOptionsTo('invoke', array($request), func_get_args()),
            $this->accessToken
        );
    }

    /**
     * Adds a record to this collection.
     *
     * @param Gedcomx                                          $record
     * @param \Gedcomx\Rs\Client\Options\StateTransitionOption $options
     *
     * @return \Gedcomx\Rs\Client\RecordState|null
     * @throws \Gedcomx\Rs\Client\Exception\GedcomxApplicationException
     */
    public function addRecord($record, StateTransitionOption $options = null)
    {
        $link = $this->getLink(Rel::RECORDS);
        if ($link == null || $link->getHref() == null)
        {
            throw new GedcomxApplicationException(sprintf("Collection at %s doesn't support adding records.", $this->getUri()));
        }

        $request = $this->createAuthenticatedGedcomxRequest(Request::POST, $link->getHref());
        $request->setBody($record->toJson());
        return $this->stateFactory->createState(
            "RecordState",
            $this->client,
            $request,
            $this->passOptionsTo('invoke', array($request), func_get_args()),
            $this->accessToken
        );
    }

    /**
     * Reads the person record for the current user.
     *
     * @param \Gedcomx\Rs\Client\Options\StateTransitionOption $option,...
     *
     * @return PersonState|null
     */
    public function readPersonForCurrentUser(StateTransitionOption $option = null)
    {
        $link = $this->getLink(Rel::CURRENT_USER_PERSON);
        if ($link == null || $link->getHref() == null) {
            return null;
        }

        $request = $this->createAuthenticatedGedcomxRequest("GET", $link->getHref());
        return $this->stateFactory->createState(
            "PersonState",
            $this->client,
            $request,
            $this->passOptionsTo('invoke', array($request), func_get_args()),
            $this->accessToken
        );
    }

    /**
     * Creates a persons collection from the current collection.
     *
     * @param \Gedcomx\Rs\Client\Options\StateTransitionOption $option,...
     *
     * @return PersonsState|null
     */
    public function readPersons(StateTransitionOption $option = null)
    {
        throw new RuntimeException("function currently not implemented."); //todo: implement
    }

    /**
     * Reads the person specified by the URI.
     *
     * @param string $uri href from a Link object
     * @param StateTransitionOption $option,...
     *
     * @returns PersonState|null
     */
    public function readPerson($uri, StateTransitionOption $option = null)
    {
        if ($uri == null) {
            return null;
        }

        $request = $this->createAuthenticatedGedcomxRequest("GET", $uri);
        return $this->stateFactory->createState(
            "PersonState",
            $this->client,
            $request,
            $this->passOptionsTo('invoke', array($request), func_get_args()),
            $this->accessToken
        );
    }

    /**
     * Searches for persons based off the specified query.
     *
     * @param GedcomxSearchQuery|string $query
     * @param StateTransitionOption $option,...
     *
     * @return PersonSearchResultsState|null
     */
    public function searchForPersons($query, StateTransitionOption $option = null)
    {
        $searchLink = $this->getLink(Rel::PERSON_SEARCH);
        if ($searchLink === null || $searchLink->getTemplate() === null) {
            return null;
        }
        if ($query instanceof GedcomxPersonSearchQueryBuilder) {
            $queryString = $query->build();
        } else {
            $queryString = $query;
        }

        $uri = array(
            $searchLink->getTemplate(),
            array(
                "q" => $queryString,
                "access_token" => $this->accessToken
            )
        );

        $request = $this->createAuthenticatedFeedRequest("GET", $uri);
        return $this->stateFactory->createState(
            "PersonSearchResultsState",
            $this->client,
            $request,
            $this->passOptionsTo('invoke', array($request), func_get_args()),
            $this->accessToken
        );
    }

    /**
     * Adds a person to the current collection.
     *
     * @param Person|Gedcomx $person
     * @param StateTransitionOption $option,...
     *
     * @return PersonState|null
     */
    public function addPerson($person, StateTransitionOption $option = null)
    {
        $link = $this->getLink(Rel::PERSONS);
        if ($link === null || $link->getHref() === null) {
            return null;
        }

        if ($person instanceof Person) {
            $entity = new Gedcomx();
            $entity->addPerson($person);
        } else {
            $entity = $person;
        }

        $request = $this->createAuthenticatedGedcomxRequest("POST", $link->getHref());
        $request->setBody($entity->toJson());
        return $this->stateFactory->createState(
            'PersonState',
            $this->client,
            $request,
            $this->passOptionsTo('invoke', array($request), func_get_args()),
            $this->accessToken
        );
    }

    /**
     * Adds the array of relationships to the collection.
     *
     * @param array                 $relationships
     * @param StateTransitionOption $option,...
     *
     * @return RelationshipState
     * @throws GedcomxApplicationException
     */
    public function addRelationships(array $relationships, StateTransitionOption $option = null)
    {
        $link = $this->getLink(Rel::RELATIONSHIPS);
        if (link == null || link . getHref() == null) {
            throw new GedcomxApplicationException(String . format("Collection at %s doesn't support adding relationships.", getUri()));
        }

        $entity = new Gedcomx();
        $entity->setRelationships($relationships);
        $request = $this->createAuthenticatedGedcomxRequest(Request::POST, $link->getHref());
        return $this->stateFactory->createState(
            'RelationshipsState',
            $this->client,
            $request,
            $this->passOptionsTo('invoke', array($request), func_get_args()),
            $this->accessToken
        );
    }

    /**
     * Reads relationships from the current collection.
     *
     * @param \Gedcomx\Rs\Client\Options\StateTransitionOption $option,...
     *
     * @return \Gedcomx\Rs\Client\RelationshipsState|null
     */
    public function readRelationships(StateTransitionOption $option = null)
    {
        $link = $this->getLink(Rel::RELATIONSHIPS);
        if ($link == null || $link->getHref() == null) {
            return null;
        }

        $request = $this->createAuthenticatedGedcomxRequest(Request::GET, $link->getHref());
        return $this->stateFactory->createState(
            'RelationshipsState',
            $this->client,
            $request,
            $this->passOptionsTo('invoke', array($request), func_get_args()),
            $this->accessToken
        );
    }

    /**
     * Adds relationship to the collection.
     *
     * @param Relationship|Gedcomx $relationship
     * @param StateTransitionOption $option,...
     *
     * @throws GedcomxApplicationException
     * @return RelationshipState|null
     */
    public function addRelationship(Relationship $relationship, StateTransitionOption $option = null)
    {
        $link = $this->getLink(Rel::RELATIONSHIPS);
        if ($link == null || $link->getHref() == null) {
            throw new GedcomxApplicationException(sprintf("Collection at %s doesn't support adding relationships.", $this->getUri()));
        }

        $entity = new Gedcomx();
        $entity->addRelationship($relationship);
        $request = $this->createAuthenticatedGedcomxRequest(Request::POST, $link->getHref());
        /** @var EntityEnclosingRequest $request */
        $request->setBody($entity->toJson());
        return $this->stateFactory->createState(
            'RelationshipState',
            $this->client,
            $request,
            $this->passOptionsTo('invoke', array($request), func_get_args()),
            $this->accessToken
        );
    }

    /**
     * Adds a spouse relationship between the two persons and applies the specified fact.
     *
     * @param PersonState $person1
     * @param PersonState $person2
     * @param Fact $fact
     * @param StateTransitionOption $option,...
     *
     * @return RelationshipState|null
     */
    public function addSpouseRelationship(PersonState $person1, PersonState $person2, Fact $fact = null, StateTransitionOption $option = null)
    {
        $relationship = new Relationship();
        $relationship->setPerson1($person1->getResourceReference());
        $relationship->setPerson2($person2->getResourceReference());
        $relationship->setKnownType(RelationshipType::COUPLE);
        if ($fact != null) {
            $relationship->addFact($fact);
        }

        return $this->passOptionsTo('addRelationship', array($relationship), func_get_args());
    }

    /**
     * Adds a parent child relationship between the two persons and applies the specified fact.
     *
     * @param PersonState $parent
     * @param PersonState $child
     * @param Fact $fact
     * @param StateTransitionOption $option,...
     *
     * @return RelationshipState|null
     */
    public function AddParentChildRelationship(PersonState $parent, PersonState $child, Fact $fact = null, StateTransitionOption $option = null)
    {
        $relationship = new Relationship();
        $relationship->setPerson1(new ResourceReference($parent->getSelfUri()));
        $relationship->setPerson2(new ResourceReference($child->getSelfUri()));
        $relationship->setKnownType(RelationshipType::PARENTCHILD);
        if ($fact != null) {
            $relationship->addFact($fact);
        }

        return $this->passOptionsTo('addRelationship', array($relationship), func_get_args());
    }

    /**
     * Adds an artifact to the collection.
     *
     * @param \Gedcomx\Rs\Client\Util\DataSource               $artifact
     * @param \Gedcomx\Source\SourceDescription                $description
     * @param \Gedcomx\Rs\Client\GedcomxApplicationState       $state
     * @param \Gedcomx\Rs\Client\Options\StateTransitionOption 1,...
     *
     * @throws \Gedcomx\Rs\Client\Exception\GedcomxApplicationException
      * @return \Gedcomx\Rs\Client\SourceDescriptionState
     */
    public function addArtifact(DataSource $artifact, SourceDescription $description = null, GedcomxApplicationState $state = null, StateTransitionOption $option = null)
    {
        if ($state == null){
            $state = $this;
        }
        $link = $state->getLink(Rel::ARTIFACTS);
        if ($link == null || $link->getHref() == null) {
            throw new GedcomxApplicationException(sprintf("Resource at %s doesn't support adding artifacts.", state.getUri()));
        }

        /** @var \Guzzle\Http\Message\EntityEnclosingRequest $request */
        $request = $state->createAuthenticatedGedcomxRequest(Request::POST, $link->getHref());
        if ($artifact->isFile()) {
            $request->addPostFile($artifact->getPostFile());
            if ($artifact->getTitle()) {
                $request->setPostField('title', $artifact->getTitle());
            }
        } else {
            foreach ($artifact->getParameters() as $key => $value) {
                $request->setPostField($key, $value);
            }
        }
        if ($description != null) {
            if ($description->getTitles() != null) {
                foreach ($description->getTitles() as $value) {
                    $request->setPostField("title", $value->getValue());
                }
            }
            if ($description->getDescriptions() != null) {
                foreach ($description->getDescriptions() as $value) {
                    $request->setPostField("description", $value->getValue());
                }
            }
            if ($description->getCitations() != null) {
                foreach ($description->getCitations() as $citation) {
                    $request->setPostField("citation", $citation->getValue());
                }
            }
        }

        return $state->stateFactory->createState(
            'SourceDescriptionState',
            $this->client,
            $request,
            $state->passOptionsTo('invoke', array($request), func_get_args()),
            $state->accessToken
        );
    }

    /**
     * Reads the source descriptions for the current collection.
     *
     * @param StateTransitionOption $option,...
     *
     * @return SourceDescriptionsState|null
     */
    public function readSourceDescriptions(StateTransitionOption $option = null)
    {
        $link = $this->getLink(Rel::SOURCE_DESCRIPTIONS);
        if ($link == null || $link->getHref() == null) {
            return null;
        }

        $request = $this->createAuthenticatedGedcomxRequest("GET", $link->getHref());
        return $this->stateFactory->createState(
            "SourceDescriptionsState",
            $this->client,
            $request,
            $this->passOptionsTo('invoke', array($request), func_get_args()),
            $this->accessToken
        );
    }

    /**
     * Adds a source description to the current collection.
     *
     * @param SourceDescription $source
     * @param StateTransitionOption $option,...
     *
     * @throws GedcomxApplicationException
     * @return SourceDescriptionState|null
     */
    public function addSourceDescription(SourceDescription $source, StateTransitionOption $option = null)
    {
        $link = $this->getLink(Rel::SOURCE_DESCRIPTIONS);
        if ($link == null || $link->getHref() == null) {
            throw new GedcomxApplicationException(sprintf("Collection at %s doesn't support adding source descriptions.", getUri()));
        }

        $entity = new Gedcomx();
        $entity->addSourceDescription($source);
        $request = $this->createAuthenticatedGedcomxRequest(Request::POST, $link->getHref());
        $request->setBody($entity->toJson());
        return $this->stateFactory->createState(
            "SourceDescriptionState",
            $this->client,
            $request,
            $this->passOptionsTo('invoke', array($request), func_get_args()),
            $this->accessToken
        );
    }

    /**
     * Reads the collection specified by this state instance.
     *
     * @param \Gedcomx\Rs\Client\Options\StateTransitionOption $option,...
     *
     * @return \Gedcomx\Rs\Client\CollectionState|null
     */
    public function readCollection(StateTransitionOption $option = null)
    {
        $link = $this->getLink(Rel::COLLECTION);
        if ($link == null || $link->getHref() == null) {
            return null;
        }

        $request = $this->createAuthenticatedGedcomxRequest(Request::GET, $link->getHref());
        return $this->stateFactory->createState(
            'CollectionState',
            $this->client,
            $request,
            $this->passOptionsTo('invoke', array($request), func_get_args()),
            $this->accessToken
        );
    }

    /**
     * Reads the subcollections specified by this state instance.
     *
     * @param \Gedcomx\Rs\Client\Options\StateTransitionOption $option,...
     *
     * @return \Gedcomx\Rs\Client\CollectionsState|null
     */
    public function readSubcollections(StateTransitionOption $option = null)
    {
        $link = $this->getLink(Rel::SUBCOLLECTIONS);
        if ($link == null || $link->getHref() == null) {
            return null;
        }

        $request = $this->createAuthenticatedGedcomxRequest(Request::GET, $link->getHref());
        return $this->stateFactory->createState(
            'CollectionsState',
            $this->client,
            $request,
            $this->passOptionsTo('invoke', array($request), func_get_args()),
            $this->accessToken
        );
    }

    /**
     * Adds a collection to the subcollection resource specified by this state instance.
     *
     * @param Collection $collection
     * @param \Gedcomx\Rs\Client\Options\StateTransitionOption $option,...
     *
     * @throws \Gedcomx\Rs\Client\Exception\GedcomxApplicationException
     * @return \Gedcomx\Rs\Client\CollectionState|null
     */
    public function addCollection(Collection $collection, StateTransitionOption $option = null)
    {
        $link = $this->getLink(Rel::SUBCOLLECTIONS);
        if ($link == null || $link->getHref() == null) {
            throw new GedcomxApplicationException(sprintf("Collection at %s doesn't support adding subcollections.", $this->getUri()));
        }

        $request = $this->createAuthenticatedGedcomxRequest(Request::POST, $link->getHref());
        $entity = new Gedcomx();
        $entity->setCollections(array($collection));
        $request->setBody($entity->toJson());
        return $this->stateFactory->createState(
            'CollectionState',
            $this->client,
            $request,
            $this->passOptionsTo('invoke', array($request), func_get_args()),
            $this->accessToken
        );
    }

    /**
     * Reads the resources (a collection source descriptions) of the current user.
     *
     * @param \Gedcomx\Rs\Client\Options\StateTransitionOption $option,...
     *
     * @return \Gedcomx\Rs\Client\SourceDescriptionsState|null
     */
    public function readResourcesOfCurrentUser(StateTransitionOption $option = null)
    {
        $link = $this->getLink(Rel::CURRENT_USER_RESOURCES);
        if ($link == null || $link->getHref() == null) {
            return null;
        }

        $request = $this->createAuthenticatedGedcomxRequest(Request::GET, $link->getHref());
        return $this->stateFactory->createState(
            'SourceDescriptionsState',
            $this->client,
            $request,
            $this->passOptionsTo('invoke', array($request), func_get_args()),
            $this->accessToken
        );
    }
}