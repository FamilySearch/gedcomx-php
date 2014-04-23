<?php


namespace Gedcomx\Rs\Api;

use Gedcomx\Conclusion\Relationship;
use Gedcomx\Gedcomx;
use Gedcomx\Conclusion\Person;
use Gedcomx\Records\Collection;
use Gedcomx\Source\SourceDescription;
use RuntimeException;

class CollectionState extends GedcomxApplicationState
{


    function __construct($client, $request, $response, $accessToken, $stateFactory)
    {
        parent::__construct($client, $request, $response, $accessToken, $stateFactory);
    }

    protected function reconstruct($request, $response)
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
     * @return PersonsState|null
     */
    public function readPersons()
    {
        throw new RuntimeException("function currently not implemented."); //todo: implement
    }

    /**
     * @param Person|Gedcomx $person
     * @return PersonState|null
     */
    public function addPerson($person)
    {
        throw new RuntimeException("function currently not implemented."); //todo: implement
    }

    /**
     * @return PersonState|null
     */
    public function readPersonForCurrentUser()
    {
        $link = $this->getLink(Rel::CURRENT_USER_PERSON);
        if (!$link) {
            return null;
        }

        $href = $link->getHref();
        if (!$href) {
            return null;
        }

        $request = $this->createAuthenticatedGedcomxRequest("GET");
        $request->setUrl($href);
        return $this->stateFactory->buildPersonState($this->client, $request, $this->client->send($request), $this->accessToken);
    }

    /**
     * @param GedcomxSearchQuery|string $query
     * @return PersonSearchResultsState|null
     */
    public function searchForPersons($query)
    {
        throw new RuntimeException("function currently not implemented."); //todo: implement
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
    public function addSpouseRelationship($person1, $person2)
    {
        throw new RuntimeException("function currently not implemented."); //todo: implement
    }

    /**
     * @param PersonState $person1
     * @param PersonState $person2
     * @return RelationshipState|null
     */
    public function addParentRelationship($person1, $person2)
    {
        throw new RuntimeException("function currently not implemented."); //todo: implement
    }

    /**
     * @param mixed $data The file
     * @param SourceDescription $description
     */
    public function addArtifact($data, $description = null)
    {
        throw new RuntimeException("function currently not implemented."); //todo: implement
    }

    /**
     * @return SourceDescriptionsState|null
     */
    public function readSourceDescriptions()
    {
        throw new RuntimeException("function currently not implemented."); //todo: implement
    }

    /**
     * @param SourceDescription|Gedcomx $description
     * @return SourceDescriptionState|null
     */
    public function addSourceDescription($description)
    {
        throw new RuntimeException("function currently not implemented."); //todo: implement
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