<?php


namespace Gedcomx\Rs\Client;

use Gedcomx\Conclusion\Relationship;
use Gedcomx\Gedcomx;
use Gedcomx\Conclusion\Person;
use Gedcomx\Records\Collection;
use Gedcomx\Rs\Client\Options\StateTransitionOption;
use Gedcomx\Rs\Client\Util\GedcomxPersonSearchQueryBuilder;
use Gedcomx\Source\SourceDescription;
use Guzzle\Http\Client;
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
		if ($link !== null) {
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
     * @return RelationshipsState|null
     */
    public function readRelationships()
    {
        throw new RuntimeException("function currently not implemented."); //todo: implement
    }

    /**
     * @param Relationship|Gedcomx $relationship
     * @return RelationshipState|null
     */
    public function addRelationship($relationship)
    {
        throw new RuntimeException("function currently not implemented."); //todo: implement
    }

    /**
     * @param PersonState $person1
     * @param PersonState $person2
     * @return RelationshipState|null
     */
    public function addSpouseRelationship(PersonState $person1, PersonState $person2)
    {
        throw new RuntimeException("function currently not implemented."); //todo: implement
    }

    /**
     * @param PersonState $person1
     * @param PersonState $person2
     * @return RelationshipState|null
     */
    public function addParentRelationship(PersonState $person1, PersonState $person2)
    {
        throw new RuntimeException("function currently not implemented."); //todo: implement
    }

    /**
     * @param mixed $data The file
     * @param SourceDescription $description
     */
    public function addArtifact($data, SourceDescription $description = null)
    {
        throw new RuntimeException("function currently not implemented."); //todo: implement
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
     * @return CollectionState|null
     */
    public function readCollection()
    {
        throw new RuntimeException("function currently not implemented."); //todo: implement
    }

    /**
     * @return CollectionsState|null
     */
    public function readSubcollections()
    {
        throw new RuntimeException("function currently not implemented."); //todo: implement
    }

    /**
     * @param Collection|Gedcomx $collection
     * @return CollectionState|null
     */
    public function addCollection($collection)
    {
        throw new RuntimeException("function currently not implemented."); //todo: implement
    }

    /**
     * @return SourceDescriptionsState|null
     */
    public function readResourcesOfCurrentUser()
    {
        throw new RuntimeException("function currently not implemented."); //todo: implement
    }
}