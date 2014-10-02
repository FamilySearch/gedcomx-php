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
     * @return CollectionState|null
     */
    public function readCollection()
    {
        throw new RuntimeException("function currently not implemented."); //todo: implement
    }

    /**
     * @param \Gedcomx\Rs\Client\StateTransitionOption $options,... zero or more StateTransitionOption objects
     * @return AncestryResultsState|null
     */
    public function readAncestry( $options = null )
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
     * @return DescendancyResultsState|null
     */
    public function readDescendancy()
    {
        throw new RuntimeException("function currently not implemented."); //todo: implement
    }

    /**
     * @return PersonState $this
     */
    public function loadAllEmbeddedResources()
    {
        throw new RuntimeException("function currently not implemented."); //todo: implement
    }

    /**
     * @return PersonState $this
     */
    public function loadConclusions()
    {
        throw new RuntimeException("function currently not implemented."); //todo: implement
    }

    /**
     * @param StateTransitionOption $options,...
     * @return PersonState $this
     */
    public function loadSourceReferences( $options = null )
    {
        $transitionOptions = $this->getTransitionOptions( func_get_args() );
        return $this->loadEmbeddedResources( array(Rel::SOURCE_REFERENCES), $transitionOptions);
    }

    /**
     * @return PersonState $this
     */
    public function loadMediaReferences()
    {
        throw new RuntimeException("function currently not implemented."); //todo: implement
    }

    /**
     * @return PersonState $this
     */
    public function loadEvidenceReferences()
    {
        throw new RuntimeException("function currently not implemented."); //todo: implement
    }

    /**
     * @return PersonState $this
     */
    public function loadNotes()
    {
        throw new RuntimeException("function currently not implemented."); //todo: implement
    }

    /**
     * @return PersonState $this
     */
    public function loadParentRelationships()
    {
        throw new RuntimeException("function currently not implemented."); //todo: implement
    }

    /**
     * @return PersonState $this
     */
    public function loadSpouseRelationships()
    {
        throw new RuntimeException("function currently not implemented."); //todo: implement
    }

    /**
     * @return PersonState $this
     */
    public function loadChildRelationships()
    {
        throw new RuntimeException("function currently not implemented."); //todo: implement
    }

    /**
     * @param Person $person
     * @return PersonState
     */
    public function update($person)
    {
        throw new RuntimeException("function currently not implemented."); //todo: implement
    }

    /**
     * @param Gender $gender
     * @return PersonState
     */
    public function setGender($gender)
    {
        throw new RuntimeException("function currently not implemented."); //todo: implement
    }

    /**
     * @param Name $name
     * @return PersonState
     */
    public function addName($name)
    {
        throw new RuntimeException("function currently not implemented."); //todo: implement
    }

    /**
     * @param Name[] $names
     * @return PersonState
     */
    public function addNames($names)
    {
        throw new RuntimeException("function currently not implemented."); //todo: implement
    }

    /**
     * @param Name $name
     * @return PersonState
     */
    public function updateName($name)
    {
        throw new RuntimeException("function currently not implemented."); //todo: implement
    }

    /**
     * @param Name[] $names
     * @return PersonState
     */
    public function updateNames($names)
    {
        throw new RuntimeException("function currently not implemented."); //todo: implement
    }

    /**
     * @param Name $name
     * @return PersonState
     */
    public function deleteName($name)
    {
        throw new RuntimeException("function currently not implemented."); //todo: implement
    }

    /**
     * @param Fact $fact
     * @return PersonState
     */
    public function addFact($fact)
    {
        throw new RuntimeException("function currently not implemented."); //todo: implement
    }

    /**
     * @param Fact[] $facts
     * @return PersonState
     */
    public function addFacts($facts)
    {
        throw new RuntimeException("function currently not implemented."); //todo: implement
    }

    /**
     * @param Fact $fact
     * @return PersonState
     */
    public function updateFact($fact)
    {
        throw new RuntimeException("function currently not implemented."); //todo: implement
    }

    /**
     * @param Fact[] $facts
     * @return PersonState
     */
    public function updateFacts($facts)
    {
        throw new RuntimeException("function currently not implemented."); //todo: implement
    }

    /**
     * @param Fact $fact
     * @return PersonState
     */
    public function deleteFact($fact)
    {
        throw new RuntimeException("function currently not implemented."); //todo: implement
    }

    /**
     * @param SourceDescriptionState|RecordState|SourceReference $obj
     * @param StateTransitionOption                              $options,...
     *
     * @throws Exception\GedcomxApplicationException
     * @return PersonState|null
     */
    public function addSourceReference($obj, $options = null)
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
     * @param StateTransitionOption $options,...
     *
     * @return PersonState|null
     */
    public function addSourceReferences($refs, $options = null) {
        $person = createEmptySelf();
        $person->setSources($refs);

        $transitionOptions = $this->getTransitionOptions( func_get_args() );
        return $this->updateSourceReferences($person, $transitionOptions);
    }

    /**
     * @param SourceReference       $sourceReference
     * @param StateTransitionOption $options,...
     *
     * @return PersonState
     */
    public function updateSourceReference($sourceReference, $options = null)
    {
        $transitionOptions = $this->getTransitionOptions( func_get_args() );
        return $this->updateSourceReferences( array($sourceReference,$transitionOptions) );
    }

    /**
     * @param Person|SourceReference[] $source
     * @param StateTransitionOption    $options,...
     *
     * @return PersonState
     */
    public function updateSourceReferences($source, $options = null)
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
     * @return PersonState
     */
    public function deleteSourceReference($sourceReference)
    {
        throw new RuntimeException("function currently not implemented."); //todo: implement
    }

    /**
     * @param SourceReference $mediaReference
     * @return PersonState
     */
    public function addMediaReference($mediaReference)
    {
        throw new RuntimeException("function currently not implemented."); //todo: implement
    }

    /**
     * @param SourceReference[] $mediaReferences
     * @return PersonState
     */
    public function addMediaReferences($mediaReferences)
    {
        throw new RuntimeException("function currently not implemented."); //todo: implement
    }

    /**
     * @param SourceReference $mediaReference
     * @return PersonState
     */
    public function updateMediaReference($mediaReference)
    {
        throw new RuntimeException("function currently not implemented."); //todo: implement
    }

    /**
     * @param SourceReference[] $mediaReferences
     * @return PersonState
     */
    public function updateMediaReferences($mediaReferences)
    {
        throw new RuntimeException("function currently not implemented."); //todo: implement
    }

    /**
     * @param SourceReference $mediaReference
     * @return PersonState
     */
    public function deleteMediaReference($mediaReference)
    {
        throw new RuntimeException("function currently not implemented."); //todo: implement
    }

    /**
     * @param EvidenceReference $evidenceReference
     * @return PersonState
     */
    public function addEvidenceReference($evidenceReference)
    {
        throw new RuntimeException("function currently not implemented."); //todo: implement
    }

    /**
     * @param EvidenceReference[] $evidenceReferences
     * @return PersonState
     */
    public function addEvidenceReferences($evidenceReferences)
    {
        throw new RuntimeException("function currently not implemented."); //todo: implement
    }

    /**
     * @param EvidenceReference $evidenceReference
     * @return PersonState
     */
    public function updateEvidenceReference($evidenceReference)
    {
        throw new RuntimeException("function currently not implemented."); //todo: implement
    }

    /**
     * @param EvidenceReference[] $evidenceReferences
     * @return PersonState
     */
    public function updateEvidenceReferences($evidenceReferences)
    {
        throw new RuntimeException("function currently not implemented."); //todo: implement
    }

    /**
     * @param EvidenceReference $evidenceReference
     * @return PersonState
     */
    public function deleteEvidenceReference($evidenceReference)
    {
        throw new RuntimeException("function currently not implemented."); //todo: implement
    }

    /**
     * @param Note $note
     * @return PersonState
     */
    public function addNote($note)
    {
        throw new RuntimeException("function currently not implemented."); //todo: implement
    }

    /**
     * @param Note[] $notes
     * @return PersonState
     */
    public function addNotes($notes)
    {
        throw new RuntimeException("function currently not implemented."); //todo: implement
    }

    /**
     * @param Note $note
     * @return PersonState
     */
    public function updateNote($note)
    {
        throw new RuntimeException("function currently not implemented."); //todo: implement
    }

    /**
     * @param Note[] $notes
     * @return PersonState
     */
    public function updateNotes($notes)
    {
        throw new RuntimeException("function currently not implemented."); //todo: implement
    }

    /**
     * @param Note $note
     * @return PersonState
     */
    public function deleteNote($note)
    {
        throw new RuntimeException("function currently not implemented."); //todo: implement
    }

    /**
     * @return PersonParentsState
     */
    public function readParents()
    {
        throw new RuntimeException("function currently not implemented."); //todo: implement
    }

    /**
     * @return PersonChildrenState
     */
    public function readChildren()
    {
        throw new RuntimeException("function currently not implemented."); //todo: implement
    }

    /**
     * @param Person $person
     * @return PersonState
     */
    public function addParent($person)
    {
        throw new RuntimeException("function currently not implemented."); //todo: implement
    }

    /**
     * @param Person $person
     * @return PersonState
     */
    public function addChild($person)
    {
        throw new RuntimeException("function currently not implemented."); //todo: implement
    }

    /**
     * @return PersonSpousesState
     */
    public function readSpouses()
    {
        throw new RuntimeException("function currently not implemented."); //todo: implement
    }

    /**
     * @param Person $person
     * @return PersonState
     */
    public function addSpouse($person)
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

    /*
     * @return \Gedcomx\Conclusion\Person
     */
    protected function createEmptySelf() {
        $person = new Person();
        $person->setId(getLocalSelfId());
        return $person;
    }

    protected function getLocalSelfId() {
        $me = getPerson();
        return $me == null ? null : $me->getId();
    }

    /**
     * @param string[]              $rels
     * @param StateTransitionOption $options,...
     *
     * @return PersonState
     */
    public function loadEmbeddedResources( $rels, $options = null ) {
        foreach ( $rels as $rel) {
            $link = $this->getLink($rel);
            if ($this->entity != null && $link != null && $link->getHref() != null) {
                $this->embed($link, $this->entity, $options);
            }
        }

        return $this;
    }

}