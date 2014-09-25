<?php


namespace Gedcomx\Rs\Client;

use Gedcomx\Common\EvidenceReference;
use Gedcomx\Common\Note;
use Gedcomx\Conclusion\Fact;
use Gedcomx\Conclusion\Relationship;
use Gedcomx\Gedcomx;
use Gedcomx\Source\SourceReference;
use RuntimeException;

class RelationshipState extends GedcomxApplicationState
{

    function __construct($client, $request, $response, $accessToken, $stateFactory)
    {
        parent::__construct($client, $request, $response, $accessToken, $stateFactory);
    }

    protected function reconstruct($request, $response)
    {
        return new RelationshipState($this->client, $request, $response, $this->accessToken, $this->stateFactory);
    }

    protected function loadEntity()
    {
        $json = json_decode($this->response->getBody(), true);
        return new Gedcomx($json);
    }

    protected function getScope()
    {
        return $this->getRelationship();
    }

    /**
     * @return Relationship
     */
    public function getRelationship()
    {
        if ($this->entity) {
            $relationships = $this->entity->getRelationships();
            if (count($relationships) > 0) {
                return $relationships[0];
            }
        }

        return null;
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
     * @param SourceReference $sourceReference
     * @return PersonState
     */
    public function addSourceReference($sourceReference)
    {
        throw new RuntimeException("function currently not implemented."); //todo: implement
    }

    /**
     * @param SourceReference[] $sourceReferences
     * @return PersonState
     */
    public function addSourceReferences($sourceReferences)
    {
        throw new RuntimeException("function currently not implemented."); //todo: implement
    }

    /**
     * @param SourceReference $sourceReference
     * @return PersonState
     */
    public function updateSourceReference($sourceReference)
    {
        throw new RuntimeException("function currently not implemented."); //todo: implement
    }

    /**
     * @param SourceReference[] $sourceReferences
     * @return PersonState
     */
    public function updateSourceReferences($sourceReferences)
    {
        throw new RuntimeException("function currently not implemented."); //todo: implement
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

}