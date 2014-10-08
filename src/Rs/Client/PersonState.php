<?php


namespace Gedcomx\Rs\Client;

use Gedcomx\Common\EvidenceReference;
use Gedcomx\Common\Note;
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
use Gedcomx\Tests\SerializationTest;
use Guzzle\Http\Client;
use Guzzle\Http\Message\Request;
use Guzzle\Http\Message\Response;
use RuntimeException;

class PersonState extends GedcomxApplicationState
{


    function __construct(Client $client, Request $request, Response $response, $accessToken, StateFactory $stateFactory)
    {
        parent::__construct($client, $request, $response, $accessToken, $stateFactory);
    }

    protected function reconstruct(Request $request, Response $response)
    {
        return new PersonState($this->client, $request, $response, $this->accessToken, $this->stateFactory);
    }

    protected function loadEntity()
    {
        $json = json_decode($this->response->getBody(), true);
        return new Gedcomx($json);
    }

    protected function getScope()
    {
        return $this->getPerson();
    }

    /**
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
     * @param \Gedcomx\Rs\Client\Options\StateTransitionOption $option,...
     *
     * @return CollectionState|null
     */
    public function readCollection(StateTransitionOption $option = null)
    {
        throw new RuntimeException("function currently not implemented."); //todo: implement
    }

    /**
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

        $request = $this->createAuthenticatedGedcomxRequest("GET");
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
        throw new RuntimeException("function currently not implemented."); //todo: implement
    }

    /**
     * @param \Gedcomx\Rs\Client\Options\StateTransitionOption $option,...
     *
     * @return \Gedcomx\Rs\Client\PersonState $this
     */
    public function loadAllEmbeddedResources(StateTransitionOption $option = null)
    {
        throw new RuntimeException("function currently not implemented."); //todo: implement
    }

    /**
     * @param \Gedcomx\Rs\Client\Options\StateTransitionOption $option,...
     *
     * @return \Gedcomx\Rs\Client\PersonState $this
     */
    public function loadConclusions(StateTransitionOption $option = null)
    {
        throw new RuntimeException("function currently not implemented."); //todo: implement
    }

    /**
     * @param \Gedcomx\Rs\Client\Options\StateTransitionOption $option,...
     *
     * @return \Gedcomx\Rs\Client\PersonState $this
     */
    public function loadSourceReferences(StateTransitionOption $option = null)
    {
        return $this->passOptionsTo('loadEmbeddedResources', array(array(Rel::SOURCE_REFERENCES)), func_get_args());
    }

    /**
     * @param \Gedcomx\Rs\Client\Options\StateTransitionOption $option,...
     *
     * @return \Gedcomx\Rs\Client\PersonState $this
     */
    public function loadMediaReferences(StateTransitionOption $option = null)
    {
        throw new RuntimeException("function currently not implemented."); //todo: implement
    }

    /**
     * @param \Gedcomx\Rs\Client\Options\StateTransitionOption $option,...
     *
     * @return \Gedcomx\Rs\Client\PersonState $this
     */
    public function loadEvidenceReferences(StateTransitionOption $option = null)
    {
        throw new RuntimeException("function currently not implemented."); //todo: implement
    }

    /**
     * @param \Gedcomx\Rs\Client\Options\StateTransitionOption $option,...
     *
     * @return \Gedcomx\Rs\Client\PersonState $this
     */
    public function loadNotes(StateTransitionOption $option = null)
    {
        $args = array(
            array(Rel::NOTES),
        );
        return $this->passOptionsTo('loadEmbeddedResources', $args, func_get_args());
    }

    /**
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
     * @param \Gedcomx\Conclusion\Person               $person
     * @param \Gedcomx\Rs\Client\Options\StateTransitionOption $option,...
     *                                                             or an array of StateTransitionOption objects
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
        $request = $this->createAuthenticatedGedcomxRequest(Request::POST, $this->getSelfUri() );
        return $this->stateFactory->createState(
            "PersonState",
            $this->client,
            $request,
            $this->passOptionsTo('invoke', array($request), func_get_args()),
            $this->accessToken
        );
    }

    /**
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
     * @param \Gedcomx\Conclusion\Name $name
     * @param \Gedcomx\Rs\Client\Options\StateTransitionOption $option,...
     *
     * @return \Gedcomx\Rs\Client\PersonState
     */
    public function deleteName(Name $name, StateTransitionOption $option = null)
    {
        throw new RuntimeException("function currently not implemented."); //todo: implement
    }

    /**
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
     * @param \Gedcomx\Conclusion\Fact                         $fact
     * @param \Gedcomx\Rs\Client\Options\StateTransitionOption $option,...
     * 
     * @return \Gedcomx\Rs\Client\PersonState
     */
    public function deleteFact(Fact $fact, StateTransitionOption $option = null)
    {
        throw new RuntimeException("function currently not implemented."); //todo: implement
    }

    /**
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
            $target = $conclusionsLink.getHref();
        }

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
     * @param SourceDescriptionState|RecordState|SourceReference $obj
     * @param \Gedcomx\Rs\Client\Options\StateTransitionOption   $option,...
     *
     * @throws Exception\GedcomxApplicationException
     * @return \Gedcomx\Rs\Client\PersonState|null
     */
    public function addSourceReference($obj, StateTransitionOption $option = null)
    {
        $class = get_class($obj);
        switch ($class) {
            case "SourceReference":
                $reference = $obj;
                break;

            case "SourceDescriptionState":
            case "RecordState":
                $reference = new SourceReference();
                $reference->setDescriptionRef($obj->getSelfUri());
                break;

            default:
                throw new GedcomxApplicationException("Unrecognized object type $class in PersonState->addSourceReference()");
        }

        return $this->passOptionsTo('addSourceReferences',array(array($reference)), func_get_args());
    }

    /**
     * @param \Gedcomx\Source\SourceReference[]                $refs
     * @param \Gedcomx\Rs\Client\Options\StateTransitionOption $option,...
     *
     * @return \Gedcomx\Rs\Client\PersonState|null
     */
    public function addSourceReferences(array $refs, StateTransitionOption $option = null) {
        $person = createEmptySelf();
        $person->setSources($refs);

        return $this->passOptionsTo('updateSourceReferences', array($person), func_get_args());
    }

    /**
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
            $person = createEmptySelf();
            $person->setSources($source);
        }
        $target = $this->getSelfUri();
        $link = $this->getLink(Rel::SOURCE_REFERENCES);
        if( $link === null || $link.getHref() === null ){
            $target = $link->getHref();
        }

        $gedcom = new Gedcomx();
        $gedcom->setPersons(array($person));
        $request = $this->createAuthenticatedGedcomxRequest("POST", $target);
        $request->setBody( $gedcom->toJson() );
        $transitionOptions = $this->getTransitionOptions( func_get_args() );

        return $this->stateFactory->createState(
            "PersonState",
            $this->client,
            $this->request,
            $this->invoke($request, $transitionOptions),
            $this->accessToken
        );
    }

    /**
     * @param \Gedcomx\Source\SourceReference                  $sourceReference
     * @param \Gedcomx\Rs\Client\Options\StateTransitionOption $option,...
     *
     * @return \Gedcomx\Rs\Client\PersonState
     */
    public function deleteSourceReference(SourceReference $sourceReference, StateTransitionOption $option = null)
    {
        throw new RuntimeException("function currently not implemented."); //todo: implement
    }

    /**
     * @param \Gedcomx\Source\SourceReference                  $mediaReference
     * @param \Gedcomx\Rs\Client\Options\StateTransitionOption $option,...
     *
     * @return \Gedcomx\Rs\Client\PersonState
     */
    public function addMediaReference(SourceReference $mediaReference, StateTransitionOption $option = null)
    {
        throw new RuntimeException("function currently not implemented."); //todo: implement
    }

    /**
     * @param \Gedcomx\Source\SourceReference[]                $mediaReferences
     * @param \Gedcomx\Rs\Client\Options\StateTransitionOption $option,...
     *
     * @return \Gedcomx\Rs\Client\PersonState
     */
    public function addMediaReferences(array $mediaReferences, StateTransitionOption $option = null)
    {
        throw new RuntimeException("function currently not implemented."); //todo: implement
    }

    /**
     * @param \Gedcomx\Source\SourceReference                  $mediaReference
     * @param \Gedcomx\Rs\Client\Options\StateTransitionOption $option,...
     *
     * @return \Gedcomx\Rs\Client\PersonState
     */
    public function updateMediaReference(SourceReference $mediaReference, StateTransitionOption $option = null)
    {
        throw new RuntimeException("function currently not implemented."); //todo: implement
    }

    /**
     * @param \Gedcomx\Source\SourceReference[]                $mediaReferences
     * @param \Gedcomx\Rs\Client\Options\StateTransitionOption $option,...
     *
     * @return \Gedcomx\Rs\Client\PersonState
     */
    public function updateMediaReferences(array $mediaReferences, StateTransitionOption $option = null)
    {
        throw new RuntimeException("function currently not implemented."); //todo: implement
    }

    /**
     * @param \Gedcomx\Source\SourceReference                  $mediaReference
     * @param \Gedcomx\Rs\Client\Options\StateTransitionOption $option,...
     *
     * @return \Gedcomx\Rs\Client\PersonState
     */
    public function deleteMediaReference(SourceReference $mediaReference, StateTransitionOption $option = null)
    {
        throw new RuntimeException("function currently not implemented."); //todo: implement
    }

    /**
     * @param \Gedcomx\Common\EvidenceReference                $evidenceReference
     * @param \Gedcomx\Rs\Client\Options\StateTransitionOption $option,...
     *
     * @return \Gedcomx\Rs\Client\PersonState
     */
    public function addEvidenceReference(EvidenceReference $evidenceReference, StateTransitionOption $option = null)
    {
        throw new RuntimeException("function currently not implemented."); //todo: implement
    }

    /**
     * @param \Gedcomx\Common\EvidenceReference[]              $evidenceReferences
     * @param \Gedcomx\Rs\Client\Options\StateTransitionOption $option,...
     *
     * @return \Gedcomx\Rs\Client\PersonState
     */
    public function addEvidenceReferences(array $evidenceReferences, StateTransitionOption $option = null)
    {
        throw new RuntimeException("function currently not implemented."); //todo: implement
    }

    /**
     * @param \Gedcomx\Common\EvidenceReference                $evidenceReference
     * @param \Gedcomx\Rs\Client\Options\StateTransitionOption $option,...
     *
     * @return \Gedcomx\Rs\Client\PersonState
     */
    public function updateEvidenceReference(EvidenceReference $evidenceReference, StateTransitionOption $option = null)
    {
        throw new RuntimeException("function currently not implemented."); //todo: implement
    }

    /**
     * @param \Gedcomx\Common\EvidenceReference[]              $evidenceReferences
     * @param \Gedcomx\Rs\Client\Options\StateTransitionOption $option,...
     *
     * @return \Gedcomx\Rs\Client\PersonState
     */
    public function updateEvidenceReferences(array $evidenceReferences, StateTransitionOption $option = null)
    {
        throw new RuntimeException("function currently not implemented."); //todo: implement
    }

    /**
     * @param \Gedcomx\Common\EvidenceReference                $evidenceReference
     * @param \Gedcomx\Rs\Client\Options\StateTransitionOption $option,...
     *
     * @return \Gedcomx\Rs\Client\PersonState
     */
    public function deleteEvidenceReference(EvidenceReference $evidenceReference, StateTransitionOption $option = null)
    {
        throw new RuntimeException("function currently not implemented."); //todo: implement
    }

    /**
     * @param \Gedcomx\Common\Note $note
     * @param \Gedcomx\Rs\Client\Options\StateTransitionOption $option,...
     *
     * @return \Gedcomx\Rs\Client\PersonState
     */
    public function addNote(Note $note, StateTransitionOption $option = null)
    {
        $transitionOptions = $this->getTransitionOptions(func_get_args());
        throw new RuntimeException("function currently not implemented."); //todo: implement
    }

    /**
     * @param \Gedcomx\Common\Note[]                           $notes
     * @param \Gedcomx\Rs\Client\Options\StateTransitionOption $option,...
     *
     * @return \Gedcomx\Rs\Client\PersonState
     */
    public function addNotes(array $notes, StateTransitionOption $option = null)
    {
        throw new RuntimeException("function currently not implemented."); //todo: implement
    }

    /**
     * @param \Gedcomx\Common\Note                             $note
     * @param \Gedcomx\Rs\Client\Options\StateTransitionOption $option,...
     *
     * @return \Gedcomx\Rs\Client\PersonState
     */
    public function updateNote(Note $note, StateTransitionOption $option = null)
    {
        throw new RuntimeException("function currently not implemented."); //todo: implement
    }

    /**
     * @param \Gedcomx\Common\Note[]                           $notes
     * @param \Gedcomx\Rs\Client\Options\StateTransitionOption $option,...
     *
     * @return \Gedcomx\Rs\Client\PersonState
     */
    public function updateNotes(array $notes, StateTransitionOption $option = null)
    {
        throw new RuntimeException("function currently not implemented."); //todo: implement
    }

    /**
     * @param \Gedcomx\Common\Note                             $note
     * @param \Gedcomx\Rs\Client\Options\StateTransitionOption $option,...
     *
     * @return \Gedcomx\Rs\Client\PersonState
     */
    public function deleteNote(Note $note, StateTransitionOption $option = null)
    {
        throw new RuntimeException("function currently not implemented."); //todo: implement
    }

    /**
     * @param \Gedcomx\Rs\Client\Options\StateTransitionOption $option,...
     * 
     * @return PersonParentsState
     */
    public function readParents(StateTransitionOption $option = null)
    {
        throw new RuntimeException("function currently not implemented."); //todo: implement
    }

    /**
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

        $request = createAuthenticatedGedcomxRequest(Request::GET, $link->getHref());

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
     * @param \Gedcomx\Rs\Client\Options\StateTransitionOption $option,...
     *
     * @return PersonSpousesState
     */
    public function readSpouses(StateTransitionOption $option = null)
    {
        throw new RuntimeException("function currently not implemented."); //todo: implement
    }

    /**
     * @param \Gedcomx\Conclusion\Person                       $person
     * @param \Gedcomx\Rs\Client\Options\StateTransitionOption $option,...
     *
     * @return \Gedcomx\Rs\Client\PersonState
     */
    public function addSpouse(Person $person, StateTransitionOption $option = null)
    {
        throw new RuntimeException("function currently not implemented."); //todo: implement
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
     * @return \Gedcomx\Conclusion\Person
     */
    protected function createEmptySelf() {
        $person = new Person();
        $person->setId($this->getLocalSelfId());
        return $person;
    }

    protected function getLocalSelfId() {
        $me = $this->getPerson();
        return $me == null ? null : $me->getId();
    }

    /**
     * @param string[]              $rels
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

}