<?php


namespace Gedcomx\Rs\Client;

use Gedcomx\Common\Attribution;
use Gedcomx\Common\EvidenceReference;
use Gedcomx\Common\Note;
use Gedcomx\Common\ResourceReference;
use Gedcomx\Conclusion\Conclusion;
use Gedcomx\Conclusion\Fact;
use Gedcomx\Conclusion\Gender;
use Gedcomx\Conclusion\Name;
use Gedcomx\Gedcomx;
use Gedcomx\Conclusion\Relationship;
use Gedcomx\Conclusion\Person;
use Gedcomx\Rs\Client\Exception\GedcomxApplicationException;
use Gedcomx\Rs\Client\Options\StateTransitionOption;
use Gedcomx\Source\SourceDescription;
use Gedcomx\Source\SourceReference;
use Gedcomx\Types;
use Gedcomx\Types\RelationshipType;
use Guzzle\Http\Client;
use Guzzle\Http\Message\EntityEnclosingRequest;
use Guzzle\Http\Message\Request;
use Guzzle\Http\Message\Response;
use RuntimeException;

class PersonState extends GedcomxApplicationState
{
    function __construct(Client $client, Request $request, Response $response, $accessToken, StateFactory $stateFactory)
    {
        parent::__construct($client, $request, $response, $accessToken, $stateFactory);
    }

    /**
     * Create new state object with the same request and response objects
     *
     * @param Request  $request
     * @param Response $response
     *
     * @return PersonState
     */
    protected function reconstruct(Request $request, Response $response)
    {
        return new PersonState($this->client, $request, $response, $this->accessToken, $this->stateFactory);
    }

    /**
     * Return the GedcomX object. Must call get() to populate the entity.
     *
     * @return Gedcomx
     */
    protected function loadEntity()
    {
        $json = json_decode($this->response->getBody(), true);
        return new Gedcomx($json);
    }

    /**
     * Get the Person object off the GedcomX entity
     *
     * @return Person
     */
    protected function getScope()
    {
        return $this->getPerson();
    }

    public function getSelfRel()
    {
        return Rel::PERSON;
    }
    /**
     * Get the Person object off the GedcomX entity
     *
     * @return Person
     */
    public function getPerson()
    {
        if ($this->entity) {
            $persons = $this->entity->getPersons();
            if (count($persons) > 0) {
                return $persons[0];
            }
        }

        return null;
    }

    /**
     * Get the relationships associated with this person
     *
     * @return Relationship[]|null
     */
    public function getRelationships()
    {
        if ($this->entity) {
            return $this->entity->getRelationships();
        }

        return null;
    }

    /**
     * Get the spouse relationship(s), if any, for this person.
     *
     * @return array|\Gedcomx\Conclusion\Relationship[]|null
     */
    public function getSpouseRelationships() {
        $relationships = $this->getRelationships();
        if ($relationships == null) {
            $relationships = array();
        }
        foreach( $relationships as $idx => $r ){
            if ($r->getKnownType() != RelationshipType::COUPLE) {
                unset($relationships[$idx]);
            }
        }

        return $relationships;
    }

    /**
     * Get the children, if any, of this person.
     *
     * @return array|\Gedcomx\Conclusion\Relationship[]|null
     */
    public function getChildRelationships() {
        $relationships = $this->getRelationships();
        if ($relationships == null) {
            $relationships = array();
        }
        foreach( $relationships as $idx => $r ){
            if ($r->getKnownType() != RelationshipType::PARENTCHILD || !$this->refersToMe($r->getPerson1())) {
                unset($relationships[$idx]);
            }
        }

        return $relationships;
    }

    /**
     * Get parents, if known, of this person.
     *
     * @return \Gedcomx\Conclusion\Relationship[]|null
     */
    public function getParentRelationships() {
        $relationships = $this->getRelationships();
        if ($relationships == null) {
            $relationships = array();
        }
        foreach( $relationships as $idx => $r ){
            if ($r->getKnownType() != RelationshipType::COUPLE) {
                unset($relationships[$idx]);
            }
        }

        return $relationships;
    }

    /**
     * Check a resource reference to see if its URI is the same as this person.
     *
     * @param ResourceReference $ref
     *
     * @return bool
     */
    protected function refersToMe(ResourceReference $ref) {
        return $ref != null && $ref->getResource() != null && $ref->getResource() == "#" . $this->getLocalSelfId();
    }

    /**
     * Create a new collection state.
     *
     * @param \Gedcomx\Rs\Client\Options\StateTransitionOption $option,...
     *
     * @return CollectionState|null
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
     * Read the ancestry of this person.
     *
     * @param \Gedcomx\Rs\Client\Options\StateTransitionOption $option,...
     *
     * @return AncestryResultsState|null
     */
    public function readAncestry(StateTransitionOption $option = null)
    {
        $link = $this->getLink(Rel::ANCESTRY);
        if (!$link||!$link->getHref()) {
            return null;
        }

        $request = $this->createAuthenticatedGedcomxRequest("GET", $link->getHref());
        $request->setUrl($link->getHref());
        return $this->stateFactory->createState(
            "AncestryResultsState",
            $this->client,
            $request,
            $this->passOptionsTo('invoke',array($request), func_get_args()),
            $this->accessToken
        );
    }

    /**
     * @param \Gedcomx\Rs\Client\Options\StateTransitionOption $option,...
     *
     * @return DescendancyResultsState|null
     */
    public function readDescendancy(StateTransitionOption $option = null)
    {
        $link = $this->getLink(Rel::DESCENDANCY);
        if (!$link||!$link->getHref()) {
            return null;
        }

        $request = $this->createAuthenticatedGedcomxRequest("GET", $link->getHref());
        $request->setUrl($link->getHref());
        return $this->stateFactory->createState(
            "DescendancyResultsState",
            $this->client,
            $request,
            $this->passOptionsTo('invoke',array($request), func_get_args()),
            $this->accessToken
        );
    }

    /**
     * @param \Gedcomx\Rs\Client\PersonState                   $persona
     * @param \Gedcomx\Rs\Client\Options\StateTransitionOption $option
     *
     * @return \Gedcomx\Rs\Client\PersonState
     */
    public function addPersona(PersonState $persona, StateTransitionOption $option = null)
    {
        return $this->passOptionsTo('addPersonEvidence', array($persona), func_get_args());
    }

    /**
     * @param \Gedcomx\Common\EvidenceReference                $reference
     * @param \Gedcomx\Rs\Client\Options\StateTransitionOption $option
     *
     * @return \Gedcomx\Rs\Client\PersonState
     */
    public function addPersonaReference(EvidenceReference $reference, StateTransitionOption $option = null)
    {
        return $this->passOptionsTo('addEvidenceReference', array($reference), func_get_args());
    }

    /**
     * @param \Gedcomx\Common\EvidenceReference[]              $refs
     * @param \Gedcomx\Rs\Client\Options\StateTransitionOption $option
     *
     * @return \Gedcomx\Rs\Client\PersonState
     */
    public function addPersonaReferences(array $refs, StateTransitionOption $option = null)
    {
        return $this->passOptionsTo('addEvidenceReferences', array($refs), func_get_args());
    }

    /**
     * @param \Gedcomx\Common\EvidenceReference                $reference
     * @param \Gedcomx\Rs\Client\Options\StateTransitionOption $option
     *
     * @return \Gedcomx\Rs\Client\PersonState
     */
    public function updatePersonaReference(EvidenceReference $reference, StateTransitionOption $option = null)
    {
        return $this->passOptionsTo('updateEvidenceReference', array($reference), func_get_args());
    }

    /**
     * @param \Gedcomx\Common\EvidenceReference[]              $refs
     * @param \Gedcomx\Rs\Client\Options\StateTransitionOption $option
     *
     * @return mixed
     */
    public function updatePersonaReferences(array $refs, StateTransitionOption $option = null)
    {
        return $this->passOptionsTo('updateEvidenceReferences', array($refs), func_get_args());
    }

    /**
     * @param \Gedcomx\Common\EvidenceReference                $reference
     * @param \Gedcomx\Rs\Client\Options\StateTransitionOption $option
     *
     * @return mixed
     */
    public function deletePersonaReference(EvidenceReference $reference, StateTransitionOption $option = null)
    {
        return $this->passOptionsTo('deleteEvidenceReference', array($reference), func_get_args());
    }

    /**
     * Load any Conclusions associated with this person.
     *
     * @param \Gedcomx\Rs\Client\Options\StateTransitionOption $option,...
     *
     * @return \Gedcomx\Rs\Client\PersonState $this
     */
    public function loadConclusions(StateTransitionOption $option = null)
    {
        return $this->passOptionsTo('loadEmbeddedResources', array(array(Rel::CONCLUSIONS)), func_get_args());
    }

    /**
     * Load any SourceReferences associated with this person.
     *
     * @param \Gedcomx\Rs\Client\Options\StateTransitionOption $option,...
     *
     * @return \Gedcomx\Rs\Client\PersonState $this
     */
    public function loadSourceReferences(StateTransitionOption $option = null)
    {
        return $this->passOptionsTo('loadEmbeddedResources', array(array(Rel::SOURCE_REFERENCES)), func_get_args());
    }

    /**
     * Load any MediaReferences associated with this person.
     *
     * @param \Gedcomx\Rs\Client\Options\StateTransitionOption $option,...
     *
     * @return \Gedcomx\Rs\Client\PersonState $this
     */
    public function loadMediaReferences(StateTransitionOption $option = null)
    {
        return $this->passOptionsTo('loadEmbeddedResources', array(array(Rel::MEDIA_REFERENCES)), func_get_args());
    }

    /**
     * Load any Evidence References associated with this person.
     *
     * @param \Gedcomx\Rs\Client\Options\StateTransitionOption $option,...
     *
     * @return \Gedcomx\Rs\Client\PersonState $this
     */
    public function loadEvidenceReferences(StateTransitionOption $option = null)
    {
        return $this->passOptionsTo('loadEmbeddedResources', array(array(Rel::EVIDENCE_REFERENCES)), func_get_args());
    }

    /**
     * Load any Notes associated with this person.
     *
     * @param \Gedcomx\Rs\Client\Options\StateTransitionOption $option,...
     *
     * @return \Gedcomx\Rs\Client\PersonState $this
     */
    public function loadNotes(StateTransitionOption $option = null)
    {
        return $this->passOptionsTo('loadEmbeddedResources', array(array(Rel::NOTES)), func_get_args());
    }

    /**
     * @param \Gedcomx\Rs\Client\Options\StateTransitionOption $option,...
     *
     * @return \Gedcomx\Rs\Client\PersonState
     */
    public function loadPersonaReferences(StateTransitionOption $option = null)
    {
        return $this->passOptionsTo('loadEvidenceReferences', array(), func_get_args());
    }

    /**
     * Read a specific note associated with this person.
     *
     * @param \Gedcomx\Common\Note $note
     * @param \Gedcomx\Rs\Client\Options\StateTransitionOption $option,...
     *
     * @throws GedcomxApplicationException
     * @return \Gedcomx\Rs\Client\PersonState
     */
    public function readNote(Note $note, StateTransitionOption $option = null)
    {
        $link = $note->getLink(Rel::NOTE);
        if( $link == null ){
            $link = $note->getLink(Rel::SELF);
        }
        if ($link == null || $link->getHref() == null ){
            throw new GedcomxApplicationException("Note cannot be read: missing link.");
        }

        $request = $this->createAuthenticatedGedcomxRequest(Request::GET, $link->getHref());
        return $this->stateFactory->createState(
            'PersonState',
            $this->client,
            $request,
            $this->passOptionsTo('invoke', array($request), func_get_args()),
            $this->accessToken
        );
    }

    /**
     * Load any parent relationships associated with this person.
     *
     * @param \Gedcomx\Rs\Client\Options\StateTransitionOption $option,...
     *
     * @return \Gedcomx\Rs\Client\PersonState $this
     */
    public function loadParentRelationships(StateTransitionOption $option = null)
    {
        $args = array(
            array(Rel::PARENT_RELATIONSHIPS),
        );
        return $this->passOptionsTo('loadEmbeddedResources', $args, func_get_args());
    }

    /**
     * Load any spouse relationships associated with this person.
     *
     * @param \Gedcomx\Rs\Client\Options\StateTransitionOption $option,...
     *
     * @return \Gedcomx\Rs\Client\PersonState $this
     */
    public function loadSpouseRelationships(StateTransitionOption $option = null)
    {
        $args = array(
            array(Rel::SPOUSE_RELATIONSHIPS),
        );
        return $this->passOptionsTo('loadEmbeddedResources', $args, func_get_args());
    }

    /**
     * Load any child relationships associated with this person.
     *
     * @param \Gedcomx\Rs\Client\Options\StateTransitionOption $option,...
     *
     * @return \Gedcomx\Rs\Client\PersonState $this
     */
    public function loadChildRelationships(StateTransitionOption $option = null)
    {
        $args = array(
            array(Rel::CHILD_RELATIONSHIPS),
        );
        return $this->passOptionsTo('loadEmbeddedResources', $args, func_get_args());
    }

    /**
     * Update a person
     *
     * @param \Gedcomx\Conclusion\Person               $person
     * @param \Gedcomx\Rs\Client\Options\StateTransitionOption $option,...
     *
     * @return \Gedcomx\Rs\Client\PersonState
     */
    public function update(Person $person, StateTransitionOption $option = null)
    {
        if ($this->getLink(Rel::CONCLUSIONS) != null && ($person->getNames() != null || $person->getFacts() != null || $person->getGender() != null)) {
            $this->passOptionsTo('updateConclusions', array($person), func_get_args());
        }

        if ($this->getLink(Rel::EVIDENCE_REFERENCES) != null && $person->getEvidence() != null) {
            $this->passOptionsTo('updateEvidenceReferences', array($person), func_get_args());
        }

        if ($this->getLink(Rel::MEDIA_REFERENCES) != null && $person->getMedia() != null) {
            $this->passOptionsTo('updateMediaReferences', array($person), func_get_args());
        }

        if ($this->getLink(Rel::SOURCE_REFERENCES) != null && $person->getSources() != null) {
            $this->passOptionsTo('updateSourceReferences', array($person), func_get_args());
        }

        if ($this->getLink(Rel::NOTES) != null && $person->getNotes() != null) {
            $this->passOptionsTo('updateNotes', array($person), func_get_args());
        }

        $gx = new Gedcomx();
        $gx->addPerson($person);
        /** @var EntityEnclosingRequest $request */
        $request = $this->createAuthenticatedGedcomxRequest(Request::POST, $this->getSelfUri() );
        $request->setBody($gx->toJson());
        return $this->stateFactory->createState(
            "PersonState",
            $this->client,
            $request,
            $this->passOptionsTo('invoke', array($request), func_get_args()),
            $this->accessToken
        );
    }

    /**
     * Add a gender conclusion to this person.
     *
     * @param \Gedcomx\Conclusion\Gender                       $gender
     * @param \Gedcomx\Rs\Client\Options\StateTransitionOption $option,...
     *
     * @return \Gedcomx\Rs\Client\PersonState
     */
    public function addGender(Gender $gender, StateTransitionOption $option = null)
    {
        $this->passOptionsTo('updateGender', array($gender), func_get_args());
    }

    /**
     * Update the gender conclusion on this person.
     *
     * @param \Gedcomx\Conclusion\Gender                       $gender
     * @param \Gedcomx\Rs\Client\Options\StateTransitionOption $option,...
     *
     * @return \Gedcomx\Rs\Client\PersonState
     */
    public function updateGender(Gender $gender, StateTransitionOption $option = null)
    {
        $person = $this->createEmptySelf();
        $person->setGender($gender);
        return $this->passOptionsTo('updateConclusions', array($person), func_get_args());
    }

    /**
     * Remove the gender conclusion on this person.
     *
     * @param \Gedcomx\Conclusion\Gender                       $gender
     * @param \Gedcomx\Rs\Client\Options\StateTransitionOption $option,...
     *
     * @return \Gedcomx\Rs\Client\PersonState
     */
    public function deleteGender(Gender $gender, StateTransitionOption $option = null)
    {
        return $this->passOptionsTo('deleteConclusion', array($gender), func_get_args());
    }


    /**
     * Add a name to this person.
     *
     * @param \Gedcomx\Conclusion\Name                         $name
     * @param \Gedcomx\Rs\Client\Options\StateTransitionOption $option,...
     *
     * @return \Gedcomx\Rs\Client\PersonState
     */
    public function addName(Name $name, StateTransitionOption $option = null)
    {
        return $this->passOptionsTo('addNames',array(array($name)), func_get_args());
    }

    /**
     * Add multiple names to this person.
     *
     * @param \Gedcomx\Conclusion\Name[]                       $names
     * @param \Gedcomx\Rs\Client\Options\StateTransitionOption $option,...
     *
     * @return \Gedcomx\Rs\Client\PersonState
     */
    public function addNames(array $names, StateTransitionOption $option = null)
    {
        $person = $this->createEmptySelf();
        $person->setNames($names);
        return $this->passOptionsTo('updateConclusions', array($person), func_get_args());
    }

    /**
     * Update a name on this person.
     *
     * @param \Gedcomx\Conclusion\Name                         $name
     * @param \Gedcomx\Rs\Client\Options\StateTransitionOption $option,...
     *
     * @return \Gedcomx\Rs\Client\PersonState
     */
    public function updateName(Name $name, StateTransitionOption $option = null)
    {
        return $this->passOptionsTo('updateNames', array(array($name)), func_get_args());
    }

    /**
     * Update multiple names on this person.
     *
     * @param \Gedcomx\Conclusion\Name[]                       $names
     * @param \Gedcomx\Rs\Client\Options\StateTransitionOption $option,...
     *
     * @return \Gedcomx\Rs\Client\PersonState
     */
    public function updateNames(array $names, StateTransitionOption $option = null )
    {
        $person = $this->createEmptySelf();
        $person->setNames($names);
        return $this->passOptionsTo('updateConclusions', array($person), func_get_args());
    }

    /**
     * Remove a name from this person.
     *
     * @param \Gedcomx\Conclusion\Name $name
     * @param \Gedcomx\Rs\Client\Options\StateTransitionOption $option,...
     *
     * @return \Gedcomx\Rs\Client\PersonState
     */
    public function deleteName(Name $name, StateTransitionOption $option = null)
    {
        return $this->passOptionsTo('deleteConclusion', array($name),func_get_args());
    }

    /**
     * Add a fact to this person.
     *
     * @param \Gedcomx\Conclusion\Fact                         $fact
     * @param \Gedcomx\Rs\Client\Options\StateTransitionOption $option,...
     *
     * @return \Gedcomx\Rs\Client\PersonState
     */
    public function addFact(Fact $fact, StateTransitionOption $option = null)
    {
        return $this->passOptionsTo('updateFacts', array(array($fact)), func_get_args());
    }

    /**
     * Add multiple facts to this person.
     *
     * @param \Gedcomx\Conclusion\Fact[]                       $facts
     * @param \Gedcomx\Rs\Client\Options\StateTransitionOption $option,...
     *
     * @return \Gedcomx\Rs\Client\PersonState
     */
    public function addFacts(array $facts, StateTransitionOption $option = null)
    {
        return $this->passOptionsTo('updateFacts', array($facts), func_get_args());
    }

    /**
     * Update a fact on this person.
     *
     * @param \Gedcomx\Conclusion\Fact                         $fact
     * @param \Gedcomx\Rs\Client\Options\StateTransitionOption $option,...
     *                                                             or an array of StateTransitionOption objects
     *
     * @return \Gedcomx\Rs\Client\PersonState
     */
    public function updateFact(Fact $fact, StateTransitionOption $option = null)
    {
        return $this->passOptionsTo('updateFacts', array($fact), func_get_args());
    }

    /**
     * Update multiple facts on this person.
     *
     * @param \Gedcomx\Conclusion\Fact[]                       $facts
     * @param \Gedcomx\Rs\Client\Options\StateTransitionOption $option,...
     *                                                             or an array of StateTransitionOption objects
     *
     * @return \Gedcomx\Rs\Client\PersonState
     */
    public function updateFacts(array $facts, StateTransitionOption $option = null)
    {
        $person = $this->createEmptySelf();
        $person->setFacts($facts);
        return $this->passOptionsTo('updateConclusions', array($person), func_get_args());
    }

    /**
     * Remove a fact from this person.
     *
     * @param \Gedcomx\Conclusion\Fact                         $fact
     * @param \Gedcomx\Rs\Client\Options\StateTransitionOption $option,...
     *
     * @return \Gedcomx\Rs\Client\PersonState
     */
    public function deleteFact(Fact $fact, StateTransitionOption $option = null)
    {
        return $this->passOptionsTo('deleteConclusion',array($fact),func_get_args());
    }

    /**
     * Update this person with the current conclusion objects.
     *
     * @param \Gedcomx\Gedcomx|\Gedcomx\Conclusion\Person      $obj
     * @param \Gedcomx\Rs\Client\Options\StateTransitionOption $option,...
     *
     * @throws Exception\GedcomxApplicationException
     * @return \Gedcomx\Rs\Client\PersonState
     */
    public function updateConclusions($obj, StateTransitionOption $option = null) {
        if( $obj instanceof Person ){
            $gx = new Gedcomx();
            $gx->addPerson($obj);
        } else if( $obj instanceof Gedcomx ){
            $gx = $obj;
        } else {
            throw new GedcomxApplicationException( "Unsupported object type: ".get_class($obj) );
        }

        $target = $this->getSelfUri();
        $conclusionsLink = $this->getLink(Rel::CONCLUSIONS);
        if ($conclusionsLink != null && $conclusionsLink->getHref() != null) {
            $target = $conclusionsLink->getHref();
        }
        /** @var EntityEnclosingRequest $request */
        $request = $this->createAuthenticatedGedcomxRequest(Request::POST, $target);
        $request->setBody($gx->toJson());
        return $this->stateFactory->createState(
            "PersonState",
            $this->client,
            $request,
            $this->passOptionsTo('invoke',array($request), func_get_args()),
            $this->accessToken
        );
    }

    /**
     * Add a source reference to this person based on a RecordState.
     *
     * @param RecordState                                      $record
     * @param \Gedcomx\Rs\Client\Options\StateTransitionOption $option,...
     *
     * @throws Exception\GedcomxApplicationException
     * @return \Gedcomx\Rs\Client\PersonState|null
     */
    public function addSourceReferenceRecord(RecordState $record, StateTransitionOption $option = null)
    {
        $reference = new SourceReference();
        $reference->setDescriptionRef($record->getSelfUri());

        return $this->passOptionsTo('addSourceReferences',array(array($reference)), func_get_args());
    }

    /**
     * Add a source reference to this person based on a SourceReference.
     *
     * @param SourceReference                                  $reference
     * @param \Gedcomx\Rs\Client\Options\StateTransitionOption $option,...
     *
     * @throws GedcomxApplicationException
     * @return \Gedcomx\Rs\Client\PersonState|null
     */
    public function addSourceReferenceObj(SourceReference $reference, StateTransitionOption $option = null)
    {
        return $this->passOptionsTo('addSourceReferences',array(array($reference)), func_get_args());
    }

    /**
     * Add a source reference to this person based on a SourceDescription.
     *
     * @param SourceDescriptionState                           $stateObj
     * @param \Gedcomx\Rs\Client\Options\StateTransitionOption $option,...
     *
     * @throws GedcomxApplicationException
     * @return \Gedcomx\Rs\Client\PersonState|null
     */
    public function addSourceReferenceState(SourceDescriptionState $stateObj, StateTransitionOption $option = null)
    {
        $reference = new SourceReference();
        $reference->setDescriptionRef($stateObj->getSelfUri());

        return $this->passOptionsTo('addSourceReferences',array(array($reference)), func_get_args());
    }

    /**
     * Add multiple source references to this person.
     *
     * @param \Gedcomx\Source\SourceReference[]                $refs
     * @param \Gedcomx\Rs\Client\Options\StateTransitionOption $option,...
     *
     * @return \Gedcomx\Rs\Client\PersonState|null
     */
    public function addSourceReferences(array $refs, StateTransitionOption $option = null) {
        $person = $this->createEmptySelf();
        $person->setSources($refs);

        return $this->passOptionsTo('updateSourceReferences', array($person), func_get_args());
    }

    /**
     * Update a source reference on this person.
     *
     * @param \Gedcomx\Source\SourceReference                  $sourceReference
     * @param \Gedcomx\Rs\Client\Options\StateTransitionOption $option,...
     *
     * @return \Gedcomx\Rs\Client\PersonState
     */
    public function updateSourceReference(SourceReference $sourceReference, StateTransitionOption $option = null)
    {
        return $this->passOptionsTo('updateSourceReferences', array($sourceReference), func_get_args());
    }

    /**
     * Update multiple source references on this person.
     *
     * @param \Gedcomx\Conclusion\Person|\Gedcomx\Source\SourceReference[] $source
     * @param \Gedcomx\Rs\Client\Options\StateTransitionOption             $option,...
     *
     * @return \Gedcomx\Rs\Client\PersonState
     */
    public function updateSourceReferences($source, StateTransitionOption $option = null)
    {
        $person = null;
        if($source instanceof Person){
           $person = $source;
        } else {
            $person = $this->createEmptySelf();
            $person->setSources($source);
        }
        $target = $this->getSelfUri();
        $link = $this->getLink(Rel::SOURCE_REFERENCES);
        if( $link != null && $link->getHref() != null ){
            $target = $link->getHref();
        }

        $gedcom = new Gedcomx();
        $gedcom->setPersons(array($person));
        /** @var EntityEnclosingRequest $request */
        $request = $this->createAuthenticatedGedcomxRequest("POST", $target);
        $request->setBody( $gedcom->toJson() );

        return $this->stateFactory->createState(
            "PersonState",
            $this->client,
            $this->request,
            $this->passOptionsTo('invoke',array($request), func_get_args()),
            $this->accessToken
        );
    }

    /**
     * Remove a source reference from this person.
     *
     * @param \Gedcomx\Source\SourceReference                  $reference
     * @param \Gedcomx\Rs\Client\Options\StateTransitionOption $option,...
     *
     * @throws GedcomxApplicationException
     * @return \Gedcomx\Rs\Client\PersonState
     */
    public function deleteSourceReference(SourceReference $reference, StateTransitionOption $option = null)
    {
        $link = $reference->getLink(Rel::SOURCE_REFERENCE);
        if ($link == null ) {
            $link = $reference->getLink(Rel::SELF);
        }
        if ($link == null || $link->getHref() == null) {
            throw new GedcomxApplicationException("Source reference cannot be deleted: missing link.");
        }

        $request = $this->createAuthenticatedGedcomxRequest(Request::DELETE, $link->getHref());
        return $this->stateFactory->createState(
            'PersonState',
            $this->client,
            $request,
            $this->passOptionsTo('invoke', array($request), func_get_args()),
            $this->accessToken
        );
    }

    /**
     * @param \Gedcomx\Rs\Client\SourceDescriptionState        $description
     * @param \Gedcomx\Rs\Client\Options\StateTransitionOption $option
     *
     * @return \Gedcomx\Rs\Client\PersonState
     */
    public function addMediaDescription(SourceDescriptionState $description, StateTransitionOption $option = null)
    {
        $reference = new SourceReference();
        $reference->setDescriptionRef($description->getSelfUri());
        return $this->passOptionsTo('addMediaReference', array($reference), func_get_args());
    }

    /**
     * @param \Gedcomx\Source\SourceReference                  $mediaReference
     * @param \Gedcomx\Rs\Client\Options\StateTransitionOption $option,...
     *
     * @return \Gedcomx\Rs\Client\PersonState
     */
    public function addMediaReference(SourceReference $mediaReference, StateTransitionOption $option = null)
    {
        return $this->passOptionsTo('addMediaReferences', array(array($mediaReference)), func_get_args());
    }

    /**
     * @param \Gedcomx\Source\SourceReference[]                $mediaReferences
     * @param \Gedcomx\Rs\Client\Options\StateTransitionOption $option,...
     *
     * @return \Gedcomx\Rs\Client\PersonState
     */
    public function addMediaReferences(array $mediaReferences, StateTransitionOption $option = null)
    {
        $person = $this->createEmptySelf();
        $person->setMedia($mediaReferences);
        return $this->passOptionsTo('updateReferences', array($person, Rel::MEDIA_REFERENCES), func_get_args());
    }

    /**
     * @param \Gedcomx\Source\SourceReference                  $mediaReference
     * @param \Gedcomx\Rs\Client\Options\StateTransitionOption $option,...
     *
     * @return \Gedcomx\Rs\Client\PersonState
     */
    public function updateMediaReference(SourceReference $mediaReference, StateTransitionOption $option = null)
    {
        return $this->passOptionsTo('updateMediaReferences', array(array($mediaReference)), func_get_args());
    }

    /**
     * @param \Gedcomx\Source\SourceReference[]                $mediaReferences
     * @param \Gedcomx\Rs\Client\Options\StateTransitionOption $option,...
     *
     * @return \Gedcomx\Rs\Client\PersonState
     */
    public function updateMediaReferences(array $mediaReferences, StateTransitionOption $option = null)
    {
        $person = $this->createEmptySelf();
        $person->setMedia($mediaReferences);
        return $this->passOptionsTo('updateReferences', array($person, Rel::MEDIA_REFERENCES), func_get_args());
    }

    /**
     * @param \Gedcomx\Source\SourceReference                  $mediaReference
     * @param \Gedcomx\Rs\Client\Options\StateTransitionOption $option,...
     *
     * @return \Gedcomx\Rs\Client\PersonState
     * @throws \Gedcomx\Rs\Client\Exception\GedcomxApplicationException
     */
    public function deleteMediaReference(SourceReference $mediaReference, StateTransitionOption $option = null)
    {
        $link = $mediaReference->getLink(Rel::MEDIA_REFERENCE);
        $link = $link == null ? $mediaReference->getLink(Rel::SELF) : $link;
        if ($link == null || $link->getHref() == null) {
            throw new GedcomxApplicationException("Media reference cannot be deleted: missing link.");
        }

        $request = $this->createAuthenticatedGedcomxRequest(Request::DELETE, $link->getHref());
        return $this->stateFactory->createState(
            'PersonState',
            $this->client,
            $request,
            $this->passOptionsTo('invoke',array($request), func_get_args()),
            $this->accessToken
        );
    }

    /**
     * @param \Gedcomx\Rs\Client\PersonState                   $person
     * @param \Gedcomx\Rs\Client\Options\StateTransitionOption $option,...
     *
     * @return \Gedcomx\Rs\Client\PersonState
     */
    public function addPersonEvidence(PersonState $person, StateTransitionOption $option = null)
    {
        $reference = new EvidenceReference();
        $reference->setResource($person->getSelfUri());

        return $this->passOptionsTo('addEvidenceReferences', array(array($reference)), func_get_args());
    }


    /**
     * @param \Gedcomx\Common\EvidenceReference                $evidenceReference
     * @param \Gedcomx\Rs\Client\Options\StateTransitionOption $option,...
     *
     * @return \Gedcomx\Rs\Client\PersonState
     */
    public function addEvidenceReference(EvidenceReference $evidenceReference, StateTransitionOption $option = null)
    {
        return $this->passOptionsTo('addEvidenceReferences', array(array($evidenceReference)), func_get_args());
    }

    /**
     * @param \Gedcomx\Common\EvidenceReference[]              $evidenceReferences
     * @param \Gedcomx\Rs\Client\Options\StateTransitionOption $option,...
     *
     * @return \Gedcomx\Rs\Client\PersonState
     */
    public function addEvidenceReferences(array $evidenceReferences, StateTransitionOption $option = null)
    {
        $person = $this->createEmptySelf();
        $person->setEvidence($evidenceReferences);
        return $this->passOptionsTo('updateReferences', array($person, Rel::EVIDENCE_REFERENCES), func_get_args());
    }

    /**
     * @param \Gedcomx\Common\EvidenceReference                $evidenceReference
     * @param \Gedcomx\Rs\Client\Options\StateTransitionOption $option,...
     *
     * @return \Gedcomx\Rs\Client\PersonState
     */
    public function updateEvidenceReference(EvidenceReference $evidenceReference, StateTransitionOption $option = null)
    {
        return $this->passOptionsTo('updateEvidenceReferences', array(array($evidenceReference)), func_get_args());
    }

    /**
     * @param \Gedcomx\Common\EvidenceReference[]              $evidenceReferences
     * @param \Gedcomx\Rs\Client\Options\StateTransitionOption $option,...
     *
     * @return \Gedcomx\Rs\Client\PersonState
     */
    public function updateEvidenceReferences(array $evidenceReferences, StateTransitionOption $option = null)
    {
        $person = $this->createEmptySelf();
        $person->setEvidence($evidenceReferences);
        $this->passOptionsTo('updateReferences', array($person, Rel::EVIDENCE_REFERENCES), func_get_args());
    }

    /**
     * @param \Gedcomx\Common\EvidenceReference                $reference
     * @param \Gedcomx\Rs\Client\Options\StateTransitionOption $option,...
     *
     * @return \Gedcomx\Rs\Client\PersonState
     * @throws \Gedcomx\Rs\Client\Exception\GedcomxApplicationException
     */
    public function deleteEvidenceReference(EvidenceReference $reference, StateTransitionOption $option = null)
    {
        $link = $reference->getLink(Rel::EVIDENCE_REFERENCE);
        $link = $link == null ? $reference->getLink(Rel::SELF) : $link;
        if ($link == null || $link->getHref() == null) {
            throw new GedcomxApplicationException("Evidence reference cannot be deleted: missing link.");
        }

        $request = $this->createAuthenticatedGedcomxRequest(Request::DELETE, $link->getHref());
        return $this->stateFactory->createState(
            'PersonState',
            $this->client,
            $request,
            $this->passOptionsTo('invoke', array($request), func_get_args()),
            $this->accessToken
        );

    }

    protected function updateReferences(Person $person, $rel, StateTransitionOption $option = null)
    {
        $target = $this->getSelfUri();
        $conclusionsLink = $this->getLink($rel);
        if ($conclusionsLink != null && $conclusionsLink->getHref() != null) {
            $target = $conclusionsLink->getHref();
        }

        $gx = new Gedcomx();
        $gx->setPersons(array($person));
        /** @var \Guzzle\Http\Message\EntityEnclosingRequest $request */
        $request = $this->createAuthenticatedGedcomxRequest(Request::POST, $target);
        $request->setBody($gx->toJson());

        return $this->stateFactory->createState(
            'PersonState',
            $this->client,
            $request,
            $this->passOptionsTo('invoke', array($request), func_get_args()),
            $this->accessToken
        );
    }

    /**
     * Add a note to this person.
     *
     * @param \Gedcomx\Common\Note $note
     * @param \Gedcomx\Rs\Client\Options\StateTransitionOption $option,...
     *
     * @return \Gedcomx\Rs\Client\PersonState
     */
    public function addNote(Note $note, StateTransitionOption $option = null)
    {
        return $this->passOptionsTo('addNotes', array(array($note)), func_get_args());
    }

    /**
     * Add multiple notes to this person.
     *
     * @param \Gedcomx\Common\Note[]                           $notes
     * @param \Gedcomx\Rs\Client\Options\StateTransitionOption $option,...
     *
     * @return \Gedcomx\Rs\Client\PersonState
     */
    public function addNotes(array $notes, StateTransitionOption $option = null)
    {
        return $this->passOptionsTo('updateNotes',array($notes), func_get_args());
    }

    /**
     * Update a note on this person.
     *
     * @param \Gedcomx\Common\Note                             $note
     * @param \Gedcomx\Rs\Client\Options\StateTransitionOption $option,...
     *
     * @return \Gedcomx\Rs\Client\PersonState
     */
    public function updateNote(Note $note, StateTransitionOption $option = null)
    {
        return $this->passOptionsTo('updateNotes', array(array($note)), func_get_args());
    }

    /**
     * Update multiple notes on this person.
     *
     * @param \Gedcomx\Common\Note[]                           $notes
     * @param \Gedcomx\Rs\Client\Options\StateTransitionOption $option,...
     *
     * @return \Gedcomx\Rs\Client\PersonState
     */
    public function updateNotes(array $notes, StateTransitionOption $option = null)
    {
        $person = $this->createEmptySelf();
        $person->setNotes($notes);

        return $this->passOptionsTo('updatePersonNotes',array($person), func_get_args());
    }

    /**
     * Update notes added to a person.
     *
     * @param \Gedcomx\Conclusion\Person                       $person
     * @param \Gedcomx\Rs\Client\Options\StateTransitionOption $option,...
     *
     * @return \Gedcomx\Rs\Client\PersonState
     */
    public function updatePersonNotes(Person $person, StateTransitionOption $option = null)
    {
        $target = $this->getSelfUri();
        $conclusionsLink = $this->getLink(Rel::NOTES);
        if ($conclusionsLink != null && $conclusionsLink->getHref() != null) {
            $target = $conclusionsLink->getHref();
        }

        $gx = new Gedcomx();
        $gx->setPersons(array($person));
        /** @var EntityEnclosingRequest $request */
        $request = $this->createAuthenticatedGedcomxRequest(Request::POST, $target);
        $request->setBody($gx->toJson());

        return $this->stateFactory->createState(
            'PersonState',
            $this->client,
            $request,
            $this->passOptionsTo('invoke', array($request), func_get_args()),
            $this->accessToken
        );
    }

    /**
     * Remove a note from a person.
     *
     * @param \Gedcomx\Common\Note $note
     * @param \Gedcomx\Rs\Client\Options\StateTransitionOption $option,...
     *
     * @throws GedcomxApplicationException
     * @return \Gedcomx\Rs\Client\PersonState
     */
    public function deleteNote(Note $note, StateTransitionOption $option = null)
    {
        $link = $note->getLink(Rel::NOTE);
        if ($link == null){
            $link = $note->getLink(Rel::SELF);
        }
        if ($link == null || $link->getHref() == null ){
            throw new GedcomxApplicationException("Note cannot be deleted: missing link.");
        }

        $request = $this->createAuthenticatedGedcomxRequest(Request::DELETE, $link->getHref());
        return $this->stateFactory->createState(
            'PersonState',
            $this->client,
            $request,
            $this->passOptionsTo('invoke', array($request), func_get_args()),
            $this->accessToken
        );
    }

    /**
     * Get a RelationshipState associated with the relationship object.
     *
     * @param Relationship          $relationship
     * @param StateTransitionOption $option
     *
     * @return RelationshipState|null
     */
    public function readRelationship(Relationship $relationship, StateTransitionOption $option = null)
    {
        $link = $relationship->getLink(Rel::RELATIONSHIP);
        $link = $link == null ? $relationship->getLink(Rel::SELF) : $link;
        if ($link == null || $link->getHref() == null) {
            return null;
        }

        $request = $this->createAuthenticatedGedcomxRequest(Request::GET, $link->getHref());

        return $this->stateFactory->createState(
            'RelationshipState',
            $this->client,
            $request,
            $this->passOptionsTo('invoke', array($request), func_get_args()),
            $this->accessToken
        );
    }


    /**
     * Read the PersonParentsState for this person. (A Family Search extension.)
     * todo: finish implementing PersonParentState
     *
     * @param \Gedcomx\Rs\Client\Options\StateTransitionOption $option,...
     *
     * @return PersonParentsState
     */
    public function readParents(StateTransitionOption $option = null)
    {
        $link = $this->getLink(Rel::PARENTS);
        if ($link == null || $link->getHref() == null) {
            return null;
        }

        $request = $this->createAuthenticatedGedcomxRequest(Request::GET, $link->getHref());

        return $this->stateFactory->createState(
            "PersonParentsState",
            $this->client,
            $request,
            $this->passOptionsTo('invoke',array($request), func_get_args()),
            $this->accessToken
        );
    }

    /**
     * Read the PersonChildrenState for this person. (A Family Search extension.)
     * todo: finish implementing PersonChildrenState
     *
     * @param \Gedcomx\Rs\Client\Options\StateTransitionOption $option,...
     *
     * @return PersonChildrenState
     */
    public function readChildren(StateTransitionOption $option = null)
    {
        $link = $this->getLink(Rel::CHILDREN);
        if ($link == null || $link->getHref() == null) {
            return null;
        }

        $request = $this->createAuthenticatedGedcomxRequest(Request::GET, $link->getHref());

        return $this->stateFactory->createState(
            "PersonChildrenState",
            $this->client,
            $request,
            $this->passOptionsTo('invoke',array($request), func_get_args()),
            $this->accessToken
        );
    }

    /**
     * @param \Gedcomx\Conclusion\Person $person
     *
     * @return \Gedcomx\Rs\Client\PersonState
     */
    public function addParent(Person $person, StateTransitionOption $option = null)
    {
        throw new RuntimeException("function currently not implemented."); //todo: implement
    }

    /**
     * @param \Gedcomx\Conclusion\Person                       $person
     * @param \Gedcomx\Rs\Client\Options\StateTransitionOption $option,...
     *
     * @return \Gedcomx\Rs\Client\PersonState
     */
    public function addChild(Person $person, StateTransitionOption $option = null)
    {
        throw new RuntimeException("function currently not implemented."); //todo: implement
    }

    /**
     * Read the PersonSpousesState for this person based on the index in the relationship array.
     * (A Family Search extension.)
     * todo: finish implementing PersonSpousesState
     *
     * @param int                   $index
     * @param StateTransitionOption $option,...
     *
     * @return PersonSpousesState|null
     */
    public function readSpouseFromIndex($index, StateTransitionOption $option = null)
    {
        $spouseRelationships = $this->getSpouseRelationships();
        if (count($spouseRelationships) <= $index) {
            return null;
        }
        return $this->passOptionsTo(
            'readSpouseFromRelationship',
            array($spouseRelationships[$index]),
            func_get_args()
        );
    }

    /**
     * Read the spouse based on a Relationship object
     *
     * @param Relationship          $relationship
     * @param StateTransitionOption $option
     *
     * @return mixed
     */
    public function readSpouseFromRelationship(Relationship $relationship, StateTransitionOption $option = null)
    {
        //todo: readRelative?
        return $this->passOptionsTo('readRelative', array($relationship), func_get_args());
    }

    /**
     * Read the spouses for this person
     * todo: finish implementing PersonSpousesState
     *
     * @param \Gedcomx\Rs\Client\Options\StateTransitionOption $option,...
     *
     * @return PersonSpousesState
     */
    public function readSpouses(StateTransitionOption $option = null)
    {
        $link = $this->getLink(Rel::SPOUSES);
        if ($link == null || $link->getHref() == null) {
            return null;
        }

        $request = $this->createAuthenticatedGedcomxRequest(Request::GET, $link->getHref());
        return $this->stateFactory->createState(
            'PersonSpousesState',
            $this->client,
            $request,
            $this->passOptionsTo('invoke', array($request), func_get_args()),
            $this->accessToken
        );
    }

    /**
     * Add a PersonState to this person as a spouse.
     *
     * @param \Gedcomx\Rs\Client\PersonState                   $person
     * @param \Gedcomx\Rs\Client\Options\StateTransitionOption $option,...
     *
     * @throws Exception\GedcomxApplicationException
     * @return \Gedcomx\Rs\Client\PersonState
     */
    public function addSpouse(PersonState $person, StateTransitionOption $option = null)
    {
        $collection = $this->readCollection();
        if ($collection == null || $collection->hasError()) {
            throw new GedcomxApplicationException("Unable to add relationship: collection unavailable.");
        }

        return $this->passOptionsTo('addSpouseRelationship', array($this,$person), func_get_args(), $collection);
    }

    /**
     * @param mixed $data The file
     * @param SourceDescription $description
     * @param \Gedcomx\Rs\Client\Options\StateTransitionOption $option,...
     */
    public function addArtifact($data, SourceDescription $description = null, StateTransitionOption $option = null)
    {
        throw new RuntimeException("function currently not implemented."); //todo: implement
    }

    /*
     * Create a Person object with this state's URI
     *
     * @return \Gedcomx\Conclusion\Person
     */
    protected function createEmptySelf() {
        $person = new Person();
        $person->setId($this->getLocalSelfId());
        return $person;
    }

    /**
     * Get the id of this Person.
     *
     * @return null|string
     */
    protected function getLocalSelfId() {
        $me = $this->getPerson();
        return $me == null ? null : $me->getId();
    }

    /**
     * Load a list of embedded resources for this person.
     *
     * @param string[]                                         $rels
     * @param \Gedcomx\Rs\Client\Options\StateTransitionOption $option,...
     *
     * @return \Gedcomx\Rs\Client\PersonState
     */
    public function loadEmbeddedResources( array $rels, StateTransitionOption $option = null ) {
        foreach ( $rels as $rel) {
            $link = $this->getLink($rel);
            if ($this->entity != null && $link != null && $link->getHref() != null) {
                $this->passOptionsTo('embed', array($link), func_get_args());
            }
        }

        return $this;
    }

    /**
     * Remove a conclusion from this person.
     *
     * @param Conclusion            $conclusion
     * @param StateTransitionOption $option
     *
     * @return PersonState
     * @throws Exception\GedcomxApplicationException
     */
    protected function deleteConclusion( Conclusion $conclusion, StateTransitionOption $option = null){
        $link = $conclusion->getLink(Rel::CONCLUSION);
        if($link == null){
            $conclusion->getLink(Rel::SELF);
        }
        if ($link == null || $link->getHref() == null) {
            throw new GedcomxApplicationException("Conclusion cannot be deleted: missing link.");
        }

        $request = $this->createAuthenticatedGedcomxRequest(Request::DELETE, $link->getHref());
        return $this->stateFactory->createState(
            'PersonState',
            $this->client,
            $request,
            $this->passOptionsTo('invoke', array($request), func_get_args()),
            $this->accessToken
        );
    }
}