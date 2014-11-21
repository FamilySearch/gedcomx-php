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

class CollectionState extends GedcomxApplicationState
{
    function __construct(Client $client, Request $request, Response $response, $accessToken, StateFactory $stateFactory)
    {
        parent::__construct($client, $request, $response, $accessToken, $stateFactory);
    }

    protected function reconstruct(Request $request, Response $response)
    {
        return new CollectionState($this->client, $request, $response, $this->accessToken, $this->stateFactory);
    }

    protected function loadEntity()
    {
        $json = json_decode($this->response->getBody(), true);
        return new Gedcomx($json);
    }

    protected function getScope()
    {
        return $this->getCollection();
    }

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
     * @return RecordsState|null
     */
    public function readRecords()
    {
        throw new RuntimeException("function currently not implemented."); //todo: implement
    }

    /**
     * @param Gedcomx $record
     * @return RecordState|null
     */
    public function addRecord($record)
    {
        throw new RuntimeException("function currently not implemented."); //todo: implement
    }

	/**
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

		$request = $this->createAuthenticatedGedcomxRequest("GET",$link->getHref());
		return $this->stateFactory->createState(
            "PersonState",
            $this->client,
            $request,
            $this->passOptionsTo('invoke', array($request),func_get_args()),
            $this->accessToken
        );
	}

	/**
     * @param \Gedcomx\Rs\Client\Options\StateTransitionOption $option,...
     *
     * @return PersonsState|null
     */
    public function readPersons(StateTransitionOption $option = null)
    {
        throw new RuntimeException("function currently not implemented."); //todo: implement
    }

    /**
	 * @param string                $uri      href from a Link object
	 * @param StateTransitionOption $option,...
	 *
	 * @returns PersonState|null
     */
	public function readPerson( $uri, StateTransitionOption $option = null ){
		if( $uri == null ){
			return null;
		}

		$request = $this->createAuthenticatedGedcomxRequest("GET", $uri);
		return $this->stateFactory->createState(
            "PersonState",
            $this->client,
            $request,
            $this->passOptionsTo('invoke', array($request),func_get_args()),
            $this->accessToken
        );
	}

	/**
     * @param GedcomxSearchQuery|string $query
     * @param StateTransitionOption     $option,...
     *
     * @return PersonSearchResultsState|null
	 */
	public function searchForPersons($query, StateTransitionOption $option = null )
	{
		$searchLink = $this->getLink(Rel::PERSON_SEARCH);
		if ($searchLink === null || $searchLink->getTemplate() === null) {
			return null;
		}
        if( $query instanceof GedcomxPersonSearchQueryBuilder ){
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
     * @param Person|Gedcomx        $person
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

        if( $person instanceof Person ){
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
     * @param array $relationships
     * @param StateTransitionOption $option
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
     * @return RelationshipsState|null
     */
    public function readRelationships()
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

        return $this->passOptionsTo('addRelationship',array($relationship), func_get_args());
    }

    /**
     * @param PersonState $parent
     * @param PersonState $child
     * @param Fact $fact
     * @param StateTransitionOption $option,...
     *
     * @return RelationshipState|null
     */
    public function addParentRelationship(PersonState $parent, PersonState $child, Fact $fact = null, StateTransitionOption $option = null)
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
     * @param \Gedcomx\Rs\Client\Util\DataSource               $artifact
     * @param \Gedcomx\Source\SourceDescription                $description
     * @param \Gedcomx\Rs\Client\GedcomxApplicationState       $state
     * @param \Gedcomx\Rs\Client\Options\StateTransitionOption $option
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
     * @param StateTransitionOption $option,...
     *
     * @return SourceDescriptionsState|null
     */
    public function readSourceDescriptions(StateTransitionOption $option = null) {
        $link = $this->getLink(Rel::SOURCE_DESCRIPTIONS);
        if ($link == null || $link->getHref() == null) {
            return null;
        }

        $request = $this->createAuthenticatedGedcomxRequest("GET",$link->getHref());
        return $this->stateFactory->createState(
            "SourceDescriptionsState",
            $this->client,
            $request,
            $this->passOptionsTo('invoke', array($request), func_get_args()),
            $this->accessToken
        );
    }

    /**
     * @param SourceDescription $source
     * @param StateTransitionOption $option,...
     *
     * @throws GedcomxApplicationException
     * @return SourceDescriptionState|null
     */
    public function addSourceDescription(SourceDescription $source, StateTransitionOption $option = null)
    {
        $link = $this->getLink(Rel::SOURCE_DESCRIPTIONS);
        if ( $link == null || $link->getHref() == null) {
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
     * @param Collection                                       $collection
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
        $request->setBody($collection);
        return $this->stateFactory->createState(
            'CollectionState',
            $this->client,
            $request,
            $this->passOptionsTo('invoke', array($request), func_get_args()),
            $this->accessToken
        );
    }

    /**
     * @param \Gedcomx\Rs\Client\Options\StateTransitionOption $option
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