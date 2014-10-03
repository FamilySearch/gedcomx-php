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
use Gedcomx\Source\SourceDescription;
use Gedcomx\Source\SourceReference;
use Guzzle\Http\Message\Request;
use RuntimeException;

class PersonState extends GedcomxApplicationState
{


    function __construct($client, $request, $response, $accessToken, $stateFactory)
    {
        parent::__construct($client, $request, $response, $accessToken, $stateFactory);
    }

    protected function reconstruct($request, $response)
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
     * @param \Gedcomx\Rs\Client\StateTransitionOption $option,... zero or more StateTransitionOption objects
     *                                                             or an array of StateTransitionOption objects
     *
     * @return CollectionState|null
     */
    public function readCollection()
    {
        throw new RuntimeException("function currently not implemented."); //todo: implement
    }

    /**
     * @param \Gedcomx\Rs\Client\StateTransitionOption $option,... zero or more StateTransitionOption objects
     *                                                             or an array of StateTransitionOption objects
     * @return AncestryResultsState|null
     */
    public function readAncestry( $option = null )
    {
        $link = $this->getLink(Rel::ANCESTRY);
        if (!$link||!$link->getHref()) {
            return null;
        }

        $transitionOptions = $this->getTransitionOptions(func_get_args());
        $request = $this->createAuthenticatedGedcomxRequest("GET");
        $request->setUrl($link->getHref());
        return $this->stateFactory->createState(
            "AncestryResultsState",
            $this->client,
            $request,
            $this->invoke($request,$transitionOptions),
            $this->accessToken
        );
        
    }

    /**
     * @param \Gedcomx\Rs\Client\StateTransitionOption $option,... zero or more StateTransitionOption objects
     *                                                             or an array of StateTransitionOption objects
     *
     * @return DescendancyResultsState|null
     */
    public function readDescendancy()
    {
        $transitionOptions = $this->getTransitionOptions(func_get_args());
        throw new RuntimeException("function currently not implemented."); //todo: implement
    }

    /**
     * @param \Gedcomx\Rs\Client\StateTransitionOption $option,... zero or more StateTransitionOption objects
     *                                                             or an array of StateTransitionOption objects
     *
     * @return PersonState $this
     */
    public function loadAllEmbeddedResources()
    {
        $transitionOptions = $this->getTransitionOptions(func_get_args());
        throw new RuntimeException("function currently not implemented."); //todo: implement
    }

    /**
     * @param \Gedcomx\Rs\Client\StateTransitionOption $option,... zero or more StateTransitionOption objects
     *                                                             or an array of StateTransitionOption objects
     *
     * @return PersonState $this
     */
    public function loadConclusions()
    {
        $transitionOptions = $this->getTransitionOptions(func_get_args());
        throw new RuntimeException("function currently not implemented."); //todo: implement
    }

    /**
     * @param \Gedcomx\Rs\Client\StateTransitionOption $option,... zero or more StateTransitionOption objects
     *                                                             or an array of StateTransitionOption objects
     *
     * @return PersonState $this
     */
    public function loadSourceReferences( $option = null )
    {
        $transitionOptions = $this->getTransitionOptions( func_get_args() );
        return $this->loadEmbeddedResources( array(Rel::SOURCE_REFERENCES), $transitionOptions);
    }

    /**
     * @param \Gedcomx\Rs\Client\StateTransitionOption $option,... zero or more StateTransitionOption objects
     *                                                             or an array of StateTransitionOption objects
     *
     * @return PersonState $this
     */
    public function loadMediaReferences()
    {
        $transitionOptions = $this->getTransitionOptions(func_get_args());
        throw new RuntimeException("function currently not implemented."); //todo: implement
    }

    /**
     * @param \Gedcomx\Rs\Client\StateTransitionOption $option,... zero or more StateTransitionOption objects
     *                                                             or an array of StateTransitionOption objects
     *
     * @return PersonState $this
     */
    public function loadEvidenceReferences()
    {
        $transitionOptions = $this->getTransitionOptions(func_get_args());
        throw new RuntimeException("function currently not implemented."); //todo: implement
    }

    /**
     * @param \Gedcomx\Rs\Client\StateTransitionOption $option,... zero or more StateTransitionOption objects
     *                                                             or an array of StateTransitionOption objects
     *
     * @return PersonState $this
     */
    public function loadNotes()
    {
        $transitionOptions = $this->getTransitionOptions(func_get_args());
        throw new RuntimeException("function currently not implemented."); //todo: implement
    }

    /**
     * @param \Gedcomx\Rs\Client\StateTransitionOption $option,... zero or more StateTransitionOption objects
     *                                                             or an array of StateTransitionOption objects
     *
     * @return PersonState $this
     */
    public function loadParentRelationships()
    {
        $transitionOptions = $this->getTransitionOptions(func_get_args());
        throw new RuntimeException("function currently not implemented."); //todo: implement
    }

    /**
     * @param \Gedcomx\Rs\Client\StateTransitionOption $option,... zero or more StateTransitionOption objects
     *                                                             or an array of StateTransitionOption objects
     *
     * @return PersonState $this
     */
    public function loadSpouseRelationships()
    {
        $transitionOptions = $this->getTransitionOptions(func_get_args());
        throw new RuntimeException("function currently not implemented."); //todo: implement
    }

    /**
     * @param \Gedcomx\Rs\Client\StateTransitionOption $option,... zero or more StateTransitionOption objects
     *                                                             or an array of StateTransitionOption objects
     *
     * @return PersonState $this
     */
    public function loadChildRelationships()
    {
        $transitionOptions = $this->getTransitionOptions(func_get_args());
        throw new RuntimeException("function currently not implemented."); //todo: implement
    }

    /**
     * @param \Gedcomx\Conclusion\Person               $person
     * @param \Gedcomx\Rs\Client\StateTransitionOption $option,... zero or more StateTransitionOption objects
     *                                                             or an array of StateTransitionOption objects
     *
     * @return PersonState
     */
    public function update(Person $person, $option = null)
    {
        $transitionOptions = $this->getTransitionOptions(func_get_args());
        if ($this->getLink(Rel::CONCLUSIONS) != null && ($person->getNames() != null || $person->getFacts() != null || $person->getGender() != null)) {
            $this->updateConclusions($person, $transitionOptions);
        }

        if ($this->getLink(Rel::EVIDENCE_REFERENCES) != null && $person->getEvidence() != null) {
            $this->updateEvidenceReferences($person, $transitionOptions);
        }

        if ($this->getLink(Rel::MEDIA_REFERENCES) != null && $person->getMedia() != null) {
            $this->updateMediaReferences($person, $transitionOptions);
        }

        if ($this->getLink(Rel::SOURCE_REFERENCES) != null && $person->getSources() != null) {
            $this->updateSourceReferences($person, $transitionOptions);
        }

        if ($this->getLink(Rel::NOTES) != null && $person->getNotes() != null) {
            $this->updateNotes($person, $transitionOptions);
        }

        $gx = new Gedcomx();
        $gx->addPerson($person);
        $request = $this->createAuthenticatedGedcomxRequest(Request::POST, $this->getSelfUri() );
        return $this->stateFactory->createState(
            "PersonState",
            $this->client,
            $request,
            $this->invoke($request, $transitionOptions),
            $this->accessToken
        );
    }

    /**
     * @param \Gedcomx\Conclusion\Gender               $gender
     * @param \Gedcomx\Rs\Client\StateTransitionOption $option,... zero or more StateTransitionOption objects
     *                                                             or an array of StateTransitionOption objects
     *
     * @return PersonState
     */
    public function addGender(Gender $gender, $option = null)
    {
        $transitionOptions = $this->getTransitionOptions(func_get_args());
        $this->updateGender($gender, $transitionOptions);
    }

    /**
     * @param \Gedcomx\Conclusion\Gender               $gender
     * @param \Gedcomx\Rs\Client\StateTransitionOption $option,... zero or more StateTransitionOption objects
     *                                                             or an array of StateTransitionOption objects
     *
     * @return PersonState
     */
    public function updateGender(Gender $gender, $option = null)
    {
        $transitionOptions = $this->getTransitionOptions(func_get_args());
        $person = $this->createEmptySelf();
        $person->setGender($gender);
        return $this->updateConclusions($person, $transitionOptions);
    }

    /**
     * @param \Gedcomx\Conclusion\Name                 $name
     * @param \Gedcomx\Rs\Client\StateTransitionOption $option,... zero or more StateTransitionOption objects
     *                                                             or an array of StateTransitionOption objects
     *
     * @return PersonState
     */
    public function addName(Name $name, $option = null)
    {
        $transitionOptions = $this->getTransitionOptions(func_get_args());
        return $this->addNames(array($name),$transitionOptions);
    }

    /**
     * @param \Gedcomx\Conclusion\Name[]               $names
     * @param \Gedcomx\Rs\Client\StateTransitionOption $option,... zero or more StateTransitionOption objects
     *                                                             or an array of StateTransitionOption objects
     *
     * @return PersonState
     */
    public function addNames($names, $option = null)
    {
        $transitionOptions = $this->getTransitionOptions(func_get_args());
        $person = $this->createEmptySelf();
        $person->setNames($names);
        return $this->updateConclusions($person, $transitionOptions);
    }

    /**
     * @param \Gedcomx\Conclusion\Name $name
     * @param \Gedcomx\Rs\Client\StateTransitionOption $option,... zero or more StateTransitionOption objects
     *                                                             or an array of StateTransitionOption objects
     *
     * @return PersonState
     */
    public function updateName(Name $name, $option = null)
    {
        $transitionOptions = $this->getTransitionOptions(func_get_args());
        return updateNames(arra($name),$transitionOptions);
    }

    /**
     * @param \Gedcomx\Conclusion\Name[]               $names
     * @param \Gedcomx\Rs\Client\StateTransitionOption $option,... zero or more StateTransitionOption objects
     *                                                             or an array of StateTransitionOption objects
     *
     * @return PersonState
     */
    public function updateNames($names, $option = null )
    {
        $transitionOptions = $this->getTransitionOptions(func_get_args());
        $person = $this->createEmptySelf();
        $person->setNames($names);
        return $this->updateConclusions($person, $transitionOptions);
    }

    /**
     * @param Name $name
     * @param \Gedcomx\Rs\Client\StateTransitionOption $option,... zero or more StateTransitionOption objects
     *                                                             or an array of StateTransitionOption objects
     * @return PersonState
     */
    public function deleteName($name)
    {
        $transitionOptions = $this->getTransitionOptions(func_get_args());
        throw new RuntimeException("function currently not implemented."); //todo: implement
    }

    /**
     * @param \Gedcomx\Conclusion\Fact                 $fact
     * @param \Gedcomx\Rs\Client\StateTransitionOption $option,... zero or more StateTransitionOption objects
     *                                                             or an array of StateTransitionOption objects
     *
     * @return PersonState
     */
    public function addFact(Fact $fact, $option = null)
    {
        $transitionOptions = $this->getTransitionOptions(func_get_args());
        return $this->updateFacts(array($fact), $transitionOptions);
    }

    /**
     * @param \Gedcomx\Conclusion\Fact[]               $facts
     * @param \Gedcomx\Rs\Client\StateTransitionOption $option,... zero or more StateTransitionOption objects
     *                                                             or an array of StateTransitionOption objects
     *
     * @return PersonState
     */
    public function addFacts($facts, $option = null)
    {
        $transitionOptions = $this->getTransitionOptions(func_get_args());
        return $this->updateFacts($facts, $transitionOptions);
    }

    /**
     * @param \Gedcomx\Conclusion\Fact                 $fact
     * @param \Gedcomx\Rs\Client\StateTransitionOption $option,... zero or more StateTransitionOption objects
     *                                                             or an array of StateTransitionOption objects
     *
     * @return PersonState
     */
    public function updateFact($fact, $option = null)
    {
        $transitionOptions = $this->getTransitionOptions(func_get_args());
        return $this->updateFacts(array($fact), $transitionOptions);
    }

    /**
     * @param \Gedcomx\Conclusion\Fact[]               $facts
     * @param \Gedcomx\Rs\Client\StateTransitionOption $option,... zero or more StateTransitionOption objects
     *                                                             or an array of StateTransitionOption objects
     *
     * @return PersonState
     */
    public function updateFacts($facts, $option = null)
    {
        $transitionOptions = $this->getTransitionOptions(func_get_args());
        $person = $this->createEmptySelf();
        $person->setNames($facts);
        return $this->updateConclusions($person, $transitionOptions);
    }

    /**
     * @param Fact $fact
     * @param \Gedcomx\Rs\Client\StateTransitionOption $option,... zero or more StateTransitionOption objects
     *                                                             or an array of StateTransitionOption objects
     * @return PersonState
     */
    public function deleteFact($fact)
    {
        $transitionOptions = $this->getTransitionOptions(func_get_args());
        throw new RuntimeException("function currently not implemented."); //todo: implement
    }

    /**
     * @param \Gedcomx\Gedcomx|\Gedcomx\Conclusion\Person $obj
     * @param \Gedcomx\Rs\Client\StateTransitionOption    $option,... zero or more StateTransitionOption objects
     *                                                                or an array of StateTransitionOption objects
     *
     * @throws Exception\GedcomxApplicationException
     * @return PersonState
     */
    public function updateConclusions($obj, $option = null) {
        if( $obj instanceof Person ){
            $gx = new Gedcomx();
            $gx->addPerson($obj);
        } else if( $obj instanceof Gedcomx ){
            $gx = $obj;
        } else {
            throw new GedcomxApplicationException( "Unsupported object type: ".get_class($obj) );
        }

        $target = $this->getSelfUri();
        $transitionOptions = $this->getTransitionOptions(func_get_args());
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
            $this->invoke($request, $transitionOptions),
            $this->accessToken
        );
  }

    /**
     * @param SourceDescriptionState|RecordState|SourceReference $obj
     * @param StateTransitionOption                              $option,...
     *                                                             or an array of StateTransitionOption objects
     *
     * @throws Exception\GedcomxApplicationException
     * @return PersonState|null
     */
    public function addSourceReference($obj, $option = null)
    {
        $transitionOptions = $this->getTransitionOptions(func_get_args());
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

        return $this->addSourceReferences( array($reference), $transitionOptions );
    }

    /**
     * @param SourceReference[]     $refs
     * @param StateTransitionOption $option,...
     *
     * @return PersonState|null
     */
    public function addSourceReferences($refs, $option = null) {
        $person = createEmptySelf();
        $person->setSources($refs);

        $transitionOptions = $this->getTransitionOptions( func_get_args() );
        return $this->updateSourceReferences($person, $transitionOptions);
    }

    /**
     * @param SourceReference       $sourceReference
     * @param StateTransitionOption $option,...
     *
     * @return PersonState
     */
    public function updateSourceReference($sourceReference, $option = null)
    {
        $transitionOptions = $this->getTransitionOptions( func_get_args() );
        return $this->updateSourceReferences( array($sourceReference,$transitionOptions) );
    }

    /**
     * @param Person|SourceReference[] $source
     * @param StateTransitionOption    $option,...
     *
     * @return PersonState
     */
    public function updateSourceReferences($source, $option = null)
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
     * @param SourceReference $sourceReference
     * @param \Gedcomx\Rs\Client\StateTransitionOption $option,... zero or more StateTransitionOption objects
     *
     * @return PersonState
     */
    public function deleteSourceReference($sourceReference)
    {
        $transitionOptions = $this->getTransitionOptions(func_get_args());
        throw new RuntimeException("function currently not implemented."); //todo: implement
    }

    /**
     * @param SourceReference $mediaReference
     * @param \Gedcomx\Rs\Client\StateTransitionOption $option,... zero or more StateTransitionOption objects
     *
     * @return PersonState
     */
    public function addMediaReference($mediaReference)
    {
        $transitionOptions = $this->getTransitionOptions(func_get_args());
        throw new RuntimeException("function currently not implemented."); //todo: implement
    }

    /**
     * @param SourceReference[] $mediaReferences
     * @param \Gedcomx\Rs\Client\StateTransitionOption $option,... zero or more StateTransitionOption objects
     *
     * @return PersonState
     */
    public function addMediaReferences($mediaReferences)
    {
        $transitionOptions = $this->getTransitionOptions(func_get_args());
        throw new RuntimeException("function currently not implemented."); //todo: implement
    }

    /**
     * @param SourceReference $mediaReference
     * @param \Gedcomx\Rs\Client\StateTransitionOption $option,... zero or more StateTransitionOption objects
     *
     * @return PersonState
     */
    public function updateMediaReference($mediaReference)
    {
        $transitionOptions = $this->getTransitionOptions(func_get_args());
        throw new RuntimeException("function currently not implemented."); //todo: implement
    }

    /**
     * @param SourceReference[] $mediaReferences
     * @param \Gedcomx\Rs\Client\StateTransitionOption $option,... zero or more StateTransitionOption objects
     * @return PersonState
     */
    public function updateMediaReferences($mediaReferences)
    {
        $transitionOptions = $this->getTransitionOptions(func_get_args());
        throw new RuntimeException("function currently not implemented."); //todo: implement
    }

    /**
     * @param SourceReference $mediaReference
     * @param \Gedcomx\Rs\Client\StateTransitionOption $option,... zero or more StateTransitionOption objects
     * @return PersonState
     */
    public function deleteMediaReference($mediaReference)
    {
        $transitionOptions = $this->getTransitionOptions(func_get_args());
        throw new RuntimeException("function currently not implemented."); //todo: implement
    }

    /**
     * @param EvidenceReference $evidenceReference
     * @param \Gedcomx\Rs\Client\StateTransitionOption $option,... zero or more StateTransitionOption objects
     * @return PersonState
     */
    public function addEvidenceReference($evidenceReference)
    {
        $transitionOptions = $this->getTransitionOptions(func_get_args());
        throw new RuntimeException("function currently not implemented."); //todo: implement
    }

    /**
     * @param EvidenceReference[] $evidenceReferences
     * @param \Gedcomx\Rs\Client\StateTransitionOption $option,... zero or more StateTransitionOption objects
     * @return PersonState
     */
    public function addEvidenceReferences($evidenceReferences)
    {
        $transitionOptions = $this->getTransitionOptions(func_get_args());
        throw new RuntimeException("function currently not implemented."); //todo: implement
    }

    /**
     * @param EvidenceReference $evidenceReference
     * @param \Gedcomx\Rs\Client\StateTransitionOption $option,... zero or more StateTransitionOption objects
     * @return PersonState
     */
    public function updateEvidenceReference($evidenceReference)
    {
        $transitionOptions = $this->getTransitionOptions(func_get_args());
        throw new RuntimeException("function currently not implemented."); //todo: implement
    }

    /**
     * @param EvidenceReference[] $evidenceReferences
     * @param \Gedcomx\Rs\Client\StateTransitionOption $option,... zero or more StateTransitionOption objects
     * @return PersonState
     */
    public function updateEvidenceReferences($evidenceReferences)
    {
        $transitionOptions = $this->getTransitionOptions(func_get_args());
        throw new RuntimeException("function currently not implemented."); //todo: implement
    }

    /**
     * @param EvidenceReference $evidenceReference
     * @param \Gedcomx\Rs\Client\StateTransitionOption $option,... zero or more StateTransitionOption objects
     * @return PersonState
     */
    public function deleteEvidenceReference($evidenceReference)
    {
        $transitionOptions = $this->getTransitionOptions(func_get_args());
        throw new RuntimeException("function currently not implemented."); //todo: implement
    }

    /**
     * @param Note $note
     * @param \Gedcomx\Rs\Client\StateTransitionOption $option,... zero or more StateTransitionOption objects
     * @return PersonState
     */
    public function addNote($note)
    {
        $transitionOptions = $this->getTransitionOptions(func_get_args());
        throw new RuntimeException("function currently not implemented."); //todo: implement
    }

    /**
     * @param Note[] $notes
     * @param \Gedcomx\Rs\Client\StateTransitionOption $option,... zero or more StateTransitionOption objects
     * @return PersonState
     */
    public function addNotes($notes)
    {
        $transitionOptions = $this->getTransitionOptions(func_get_args());
        throw new RuntimeException("function currently not implemented."); //todo: implement
    }

    /**
     * @param Note $note
     * @param \Gedcomx\Rs\Client\StateTransitionOption $option,... zero or more StateTransitionOption objects
     * @return PersonState
     */
    public function updateNote($note)
    {
        $transitionOptions = $this->getTransitionOptions(func_get_args());
        throw new RuntimeException("function currently not implemented."); //todo: implement
    }

    /**
     * @param Note[] $notes
     * @param \Gedcomx\Rs\Client\StateTransitionOption $option,... zero or more StateTransitionOption objects
     * @return PersonState
     */
    public function updateNotes($notes)
    {
        $transitionOptions = $this->getTransitionOptions(func_get_args());
        throw new RuntimeException("function currently not implemented."); //todo: implement
    }

    /**
     * @param Note $note
     * @param \Gedcomx\Rs\Client\StateTransitionOption $option,... zero or more StateTransitionOption objects
     * @return PersonState
     */
    public function deleteNote($note)
    {
        $transitionOptions = $this->getTransitionOptions(func_get_args());
        throw new RuntimeException("function currently not implemented."); //todo: implement
    }

    /**
     * @param \Gedcomx\Rs\Client\StateTransitionOption $option,... zero or more StateTransitionOption objects
     * @return PersonParentsState
     */
    public function readParents()
    {
        $transitionOptions = $this->getTransitionOptions(func_get_args());
        throw new RuntimeException("function currently not implemented."); //todo: implement
    }

    /**
     * @param \Gedcomx\Rs\Client\StateTransitionOption $option,... zero or more StateTransitionOption objects
     * @return PersonChildrenState
     */
    public function readChildren()
    {
        $transitionOptions = $this->getTransitionOptions(func_get_args());
        throw new RuntimeException("function currently not implemented."); //todo: implement
    }

    /**
     * @param Person $person
     * @return PersonState
     */
    public function addParent($person)
    {
        $transitionOptions = $this->getTransitionOptions(func_get_args());
        throw new RuntimeException("function currently not implemented."); //todo: implement
    }

    /**
     * @param Person $person
     * @param \Gedcomx\Rs\Client\StateTransitionOption $option,... zero or more StateTransitionOption objects
     * @return PersonState
     */
    public function addChild($person)
    {
        $transitionOptions = $this->getTransitionOptions(func_get_args());
        throw new RuntimeException("function currently not implemented."); //todo: implement
    }

    /**
     * @param \Gedcomx\Rs\Client\StateTransitionOption $option,... zero or more StateTransitionOption objects
     * @return PersonSpousesState
     */
    public function readSpouses()
    {
        $transitionOptions = $this->getTransitionOptions(func_get_args());
        throw new RuntimeException("function currently not implemented."); //todo: implement
    }

    /**
     * @param Person $person
     * @param \Gedcomx\Rs\Client\StateTransitionOption $option,... zero or more StateTransitionOption objects
     * @return PersonState
     */
    public function addSpouse($person)
    {
        $transitionOptions = $this->getTransitionOptions(func_get_args());
        throw new RuntimeException("function currently not implemented."); //todo: implement
    }

    /**
     * @param mixed $data The file
     * @param SourceDescription $description
     * @param \Gedcomx\Rs\Client\StateTransitionOption $option,... zero or more StateTransitionOption objects
     */
    public function addArtifact($data, $description = null)
    {
        $transitionOptions = $this->getTransitionOptions(func_get_args());
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
     * @param StateTransitionOption $option,...
     *
     * @return PersonState
     */
    public function loadEmbeddedResources( $rels, $option = null ) {
        $transitionOptions = $this->getTransitionOptions(func_get_args());
        foreach ( $rels as $rel) {
            $link = $this->getLink($rel);
            if ($this->entity != null && $link != null && $link->getHref() != null) {
                $this->embed($link, $this->entity, $transitionOptions);
            }
        }

        return $this;
    }

}