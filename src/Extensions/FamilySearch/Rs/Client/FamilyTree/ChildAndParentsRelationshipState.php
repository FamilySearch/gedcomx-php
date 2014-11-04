<?php

namespace Gedcomx\Extensions\FamilySearch\Rs\Client\FamilyTree;

use Gedcomx\Common\EvidenceReference;
use Gedcomx\Common\Note;
use Gedcomx\Common\ResourceReference;
use Gedcomx\Conclusion\Conclusion;
use Gedcomx\Conclusion\Fact;
use Gedcomx\Extensions\FamilySearch\FamilySearchPlatform;
use Gedcomx\Extensions\FamilySearch\Platform\Tree\ChildAndParentsRelationship;
use Gedcomx\Extensions\FamilySearch\Rs\Client\FamilySearchCollectionState;
use Gedcomx\Extensions\FamilySearch\Rs\Client\Helpers\FamilySearchRequest;
use Gedcomx\Extensions\FamilySearch\Rs\Client\Rel;
use Gedcomx\Links\Link;
use Gedcomx\Rs\Client\Exception\GedcomxApplicationException;
use Gedcomx\Rs\Client\Options\StateTransitionOption;
use Gedcomx\Rs\Client\PersonState;
use Gedcomx\Rs\Client\SourceDescriptionState;
use Gedcomx\Source\SourceReference;
use Guzzle\Http\Message\EntityEnclosingRequest;
use Guzzle\Http\Message\Request;
use Guzzle\Http\Message\Response;

class ChildAndParentsRelationshipState extends FamilySearchCollectionState implements PreferredRelationshipState
{

    protected function reconstruct(Request $request, Response $response)
    {
        return new ChildAndParentsRelationshipState($this->client, $request, $response, $this->accessToken, $this->stateFactory);
    }

    protected function getScope()
    {
        return $this->getRelationship();
    }

    /**
     * @return ChildAndParentsRelationship|null
     */
    public function getRelationship()
    {
        if ($this->getEntity() != null) {
            $relationships = $this->getEntity()->getChildAndParentsRelationships();
            if ($relationships != null && !empty($relationships)) {
                return $relationships[0];
            }
        }
        return null;
    }

    /**
     * @return Conclusion
     */
    public function getConclusion()
    {
        return $this->getFatherFact() != null ? $this->getFatherFact()
            : $this->getMotherFact() != null ? $this->getMotherFact()
                : null;
    }

    /**
     * @return Fact|string
     */
    public function getFatherFact()
    {
        $relationship = $this->getRelationship();
        if ($relationship != null) {
            $facts = $relationship->getFatherFacts();
            if ($facts != null && !empty($facts)) {
                return $facts[0];
            }
        }

        return null;
    }

    /**
     * @return Fact|null
     */
    public function getMotherFact()
    {
        $relationship = $this->getRelationship();
        if ($relationship != null) {
            $facts = $relationship->getMotherFacts();
            if ($facts != null && !empty($facts)) {
                return $facts[0];
            }
        }

        return null;
    }

    /**
     * @return Note|null
     */
    public function getNote()
    {
        $relationship = $this->getRelationship();
        if ($relationship != null) {
            $notes = $relationship->getNotes();
            if ($notes != null && !empty($notes)) {
                return $notes[0];
            }
        }

        return null;
    }

    /**
     * @return SourceReference|null
     */
    public function getSourceReference()
    {
        $relationship = $this->getRelationship();
        if ($relationship != null) {
            $sources = $relationship->getSources();
            if ($sources != null && !empty($sources)) {
                return $sources[0];
            }
        }

        return null;
    }

    /**
     * @return EvidenceReference|null
     */
    public function getEvidenceReference()
    {
        $relationship = $this->getRelationship();
        if ($relationship != null) {
            $evidence = $relationship->getEvidence();
            if ($evidence != null && !empty($evidence)) {
                return $evidence[0];
            }
        }

        return null;
    }

    /**
     * @return SourceReference|null
     */
    public function getMediaReference()
    {
        $relationship = $this->getRelationship();
        if ($relationship != null) {
            $media = $relationship->getMedia();
            if ($media != null && !empty($media)) {
                return $media[0];
            }
        }

        return null;
    }

    /**
     * @param StateTransitionOption $option,...
     *
     * @return FamilyTreeCollectionState|null
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
            $this->accessToken);
    }

    /**
     * @param string $method
     * @param Link   $link
     *
     * @return Request
     */
    protected function createRequestForEmbeddedResource($method, Link $link)
    {
        $request = $this->createAuthenticatedGedcomxRequest($method, $link->getHref());
        FamilySearchRequest::applyFamilySearchMediaType($request);

        return $request;
    }

    /**
     * @param StateTransitionOption $option,...
     *
     * @return $this
     */
    public function loadEmbeddedResource(StateTransitionOption $option = null)
    {
        $this->passOptionsTo('includeEmbeddedResources', array($this->entity), func_get_args());
        return $this;
    }

    /**
     * @param array                 $rels
     * @param StateTransitionOption $option,...
     *
     * @return ChildAndParentsRelationshipState|null
     */
    public function loadEmbeddedResources(array $rels, StateTransitionOption $option = null)
    {
        foreach ($rels as $rel) {
            $link = $this->getLink($rel);
            if ($this->entity != null && $link != null && $link->getHref() != null) {
                $this->passOptionsTo('embed', array($link), func_get_args());
            }
        }
        return $this;
    }

    /**
     * @param StateTransitionOption $option,...
     *
     * @return ChildAndParentsRelationshipState
     */
    public function loadConclusions(StateTransitionOption $option = null)
    {
        return $this->passOptionsTo('loadEmbeddedResources', array(array(Rel::CONCLUSIONS)), func_get_args());
    }

    /**
     * @param StateTransitionOption $option,...
     *
     * @return ChildAndParentsRelationshipState
     */
    public function loadSourceReferences(StateTransitionOption $option = null)
    {
        return $this->passOptionsTo('loadEmbeddedResources', array(array(Rel::SOURCE_REFERENCES)), func_get_args());
    }

    /**
     * @param StateTransitionOption $option,...
     *
     * @return ChildAndParentsRelationshipState
     */
    public function loadMediaReferences(StateTransitionOption $option = null)
    {
        return $this->passOptionsTo('loadEmbeddedResources', array(array(Rel::MEDIA_REFERENCES)), func_get_args());
    }

    /**
     * @param StateTransitionOption $option,...
     *
     * @return ChildAndParentsRelationshipState
     */
    public function loadEvidenceReferences(StateTransitionOption $option = null)
    {
        return $this->passOptionsTo('loadEmbeddedResources', array(array(Rel::EVIDENCE_REFERENCES)), func_get_args());
    }

    /**
     * @param StateTransitionOption $option,...
     *
     * @return ChildAndParentsRelationshipState
     */
    public function loadNotes(StateTransitionOption $option = null)
    {
        return $this->passOptionsTo('loadEmbeddedResources', array(array(Rel::NOTES)), func_get_args());
    }

    /**
     * @return ChildAndParentsRelationship
     */
    protected function createEmptySelf()
    {
        $relationship = new ChildAndParentsRelationship();
        $relationship->setId($this->getLocalSelfId());
        return $relationship;
    }

    /**
     * @return null|string
     */
    protected function getLocalSelfId()
    {
        $me = $this->getRelationship();
        return $me == null ? null : $me->getId();
    }

    /**
     * @param Fact                  $fact
     * @param StateTransitionOption $option,...
     *
     * @return ChildAndParentsRelationshipState
     */
    public function addFatherFact(Fact $fact, StateTransitionOption $option = null)
    {
        return $this->passOptionsTo('addFatherFacts', array(array($fact)), func_get_args());
    }

    /**
     * @param array                                            $facts
     * @param \Gedcomx\Rs\Client\Options\StateTransitionOption $option
     *
     * @return ChildAndParentsRelationshipState
     */
    public function addFatherFacts(array $facts, StateTransitionOption $option = null)
    {
        $relationship = $this->createEmptySelf();
        $relationship->setFatherFacts($facts);
        return $this->passOptionsTo('updateRelationship', array($relationship, Rel::CONCLUSIONS), func_get_args());
    }

    /**
     * @param \Gedcomx\Conclusion\Fact                         $fact
     * @param \Gedcomx\Rs\Client\Options\StateTransitionOption $option
     *
     * @return ChildAndParentsRelationshipState
     */
    public function updateFatherFact(Fact $fact, StateTransitionOption $option = null)
    {
        return $this->passOptionsTo('updateFatherFacts', array(array($fact)), func_get_args());
    }

    /**
     * @param array                 $facts
     * @param StateTransitionOption $option,...
     *
     * @return ChildAndParentsRelationshipState
     */
    public function updateFatherFacts(array $facts, StateTransitionOption $option = null)
    {
        $relationship = $this->createEmptySelf();
        $relationship->setFatherFacts($facts);
        return $this->passOptionsTo('updateRelationship', array($relationship, Rel::CONCLUSIONS), func_get_args());
    }

    /**
     * @param Fact                  $fact
     * @param StateTransitionOption $option,...
     *
     * @return ChildAndParentsRelationshipState
     */
    public function addMotherFact(Fact $fact, StateTransitionOption $option = null)
    {
        return $this->passOptionsTo('addMotherFacts', array(array($fact)), func_get_args());
    }

    /**
     * @param array                 $facts
     * @param StateTransitionOption $option,...
     *
     * @return ChildAndParentsRelationshipState
     */
    public function addMotherFacts(array $facts, StateTransitionOption $option = null)
    {
        $relationship = $this->createEmptySelf();
        $relationship->setMotherFacts($facts);
        return $this->passOptionsTo('updateRelationship', array($relationship, Rel::CONCLUSIONS), func_get_args());
    }

    /**
     * @param Fact                  $fact
     * @param StateTransitionOption $option,...
     *
     * @return ChildAndParentsRelationshipState
     */
    public function updateMotherFact(Fact $fact, StateTransitionOption $option = null)
    {
        return $this->passOptionsTo('updateMotherFacts', array(array($fact)), func_get_args());
    }

    /**
     * @param array                 $facts
     * @param StateTransitionOption $option,...
     *
     * @return ChildAndParentsRelationshipState
     */
    public function updateMotherFacts(array $facts, StateTransitionOption $option = null)
    {
        $relationship = $this->createEmptySelf();
        $relationship->setMotherFacts($facts);
        return $this->passOptionsTo('updateRelationship', array($relationship, Rel::CONCLUSIONS), func_get_args());
    }

    /**
     * @param Fact                  $fact
     * @param StateTransitionOption $option,...
     *
     * @return mixed
     * @throws GedcomxApplicationException
     */
    public function deleteFact(Fact $fact, StateTransitionOption $option = null)
    {
        $link = $fact->getLink(Rel::CONCLUSION);
        $link = $link == null ? $fact->getLink(Rel::SELF) : $link;
        if ($link == null || $link->getHref() == null) {
            throw new GedcomxApplicationException("Conclusion cannot be deleted: missing link.");
        }

        $request = $this->createAuthenticatedRequest(Request::DELETE, $link->getHref());
        FamilySearchRequest::applyFamilySearchMediaType($request);
        return $this->stateFactory->createState(
            'ChildAndParentsRelationshipState',
            $this->client,
            $request,
            $this->passOptionsTo('invoke', array($request), func_get_args()),
            $this->accessToken);
    }

    /**
     * @param SourceDescriptionState $source
     * @param StateTransitionOption  $option,...
     *
     * @return ChildAndParentsRelationshipState
     */
    public function addSourceReferenceState(SourceDescriptionState $source, StateTransitionOption $option = null)
    {
        $reference = new SourceReference();
        $reference->setDescriptionRef($source->getSelfUri());
        return $this->passOptionsTo('addSourceReference', array($reference), func_get_args());
    }

    /**
     * @param SourceReference       $reference
     * @param StateTransitionOption $option,...
     *
     * @return ChildAndParentsRelationshipState
     */
    public function addSourceReference(SourceReference $reference, StateTransitionOption $option = null)
    {
        return $this->passOptionsTo('addSourceReferences', array(array($reference)), func_get_args());
    }

    /**
     * @param array                 $refs
     * @param StateTransitionOption $option,...
     *
     * @return ChildAndParentsRelationshipState
     */
    public function addSourceReferences(array $refs, StateTransitionOption $option = null)
    {
        $relationship = $this->createEmptySelf();
        $relationship->setSources($refs);
        return $this->passOptionsTo('updateRelationship', array($relationship, Rel::SOURCE_REFERENCES), func_get_args());
    }

    /**
     * @param SourceReference       $reference
     * @param StateTransitionOption $option,...
     *
     * @return ChildAndParentsRelationshipState
     */
    public function updateSourceReference(SourceReference $reference, StateTransitionOption $option = null)
    {
        return $this->passOptionsTo('updateSourceReferences', array(array($reference)), func_get_args());
    }

    /**
     * @param array                 $refs
     * @param StateTransitionOption $option,...
     *
     * @return ChildAndParentsRelationshipState
     */
    public function updateSourceReferences(array $refs, StateTransitionOption $option = null)
    {
        $relationship = $this->createEmptySelf();
        $relationship->setSources($refs);
        return $this->passOptionsTo('updateRelationship', array($relationship, Rel::SOURCE_REFERENCES), func_get_args());
    }

    /**
     * @param SourceReference       $reference
     * @param StateTransitionOption $option,...
     *
     * @return ChildAndParentsRelationshipState
     * @throws GedcomxApplicationException
     */
    public function deleteSourceReference(SourceReference $reference, StateTransitionOption $option = null)
    {
        $link = $reference->getLink(Rel::SOURCE_REFERENCE);
        $link = $link == null ? $reference->getLink(Rel::SELF) : $link;
        if ($link == null || $link->getHref() == null) {
            throw new GedcomxApplicationException("Source reference cannot be deleted: missing link.");
        }

        $request = $this->createAuthenticatedRequest(Request::DELETE, $link->getHref());
        FamilySearchRequest::applyFamilySearchMediaType($request);
        return $this->stateFactory->createState(
            'ChildAndParentsRelationshipState',
            $this->client,
            $request,
            $this->passOptionsTo('invoke', array($request), func_get_args()),
            $this->accessToken
        );
    }

    /**
     * @param SourceReference       $reference
     * @param StateTransitionOption $option,...
     *
     * @return ChildAndParentsRelationshipState
     */
    public function addMediaReference(SourceReference $reference, StateTransitionOption $option = null)
    {
        return $this->passOptionsTo('addMediaReferences', array(array($reference)), func_get_args());
    }

    /**
     * @param array                 $refs
     * @param StateTransitionOption $option,...
     *
     * @return ChildAndParentsRelationshipState
     */
    public function addMediaReferences(array $refs, StateTransitionOption $option = null)
    {
        $relationship = $this->createEmptySelf();
        $relationship->setMedia($refs);
        return $this->passOptionsTo('updateMediaReferences', array($relationship), func_get_args());
    }

    /**
     * @param SourceReference       $reference
     * @param StateTransitionOption $option,...
     *
     * @return ChildAndParentsRelationshipState
     */
    public function updateMediaReference(SourceReference $reference, StateTransitionOption $option = null)
    {
        return $this->passOptionsTo('updateMediaReferences', array(array($reference)), func_get_args());
    }

    /**
     * @param array                 $refs
     * @param StateTransitionOption $option,...
     *
     * @return ChildAndParentsRelationshipState
     */
    public function updateMediaReferences(array $refs, StateTransitionOption $option = null)
    {
        $relationship = $this->createEmptySelf();
        $relationship->setMedia($refs);
        return $this->passOptionsTo('updateRelationship', array($relationship, Rel::MEDIA_REFERENCES), func_get_args());
    }

    /**
     * @param SourceReference       $reference
     * @param StateTransitionOption $option,...
     *
     * @return ChildAndParentsRelationshipState
     * @throws GedcomxApplicationException
     */
    public function deleteMediaReference(SourceReference $reference, StateTransitionOption $option = null)
    {
        $link = $reference->getLink(Rel::MEDIA_REFERENCE);
        $link = $link == null ? $reference->getLink(Rel::SELF) : $link;
        if ($link == null || $link->getHref() == null) {
            throw new GedcomxApplicationException("Media reference cannot be deleted: missing link.");
        }

        $request = $this->createAuthenticatedRequest(Request::DELETE, $link->getHref());
        FamilySearchRequest::applyFamilySearchMediaType($request);
        return $this->stateFactory->createState(
            'ChildAndParentsRelationshipState',
            $this->client,
            $request,
            $this->passOptionsTo('invoke', array($request), func_get_args()),
            $this->accessToken
        );
    }

    /**
     * @param EvidenceReference     $reference
     * @param StateTransitionOption $option,...
     *
     * @return ChildAndParentsRelationshipState
     */
    public function addEvidenceReference(EvidenceReference $reference, StateTransitionOption $option = null)
    {
        return $this->passOptionsTo('addEvidenceReferences', array(array($reference)), func_get_args());
    }

    /**
     * @param array                 $refs
     * @param StateTransitionOption $option,...
     *
     * @return ChildAndParentsRelationshipState
     */
    public function addEvidenceReferences(array $refs, StateTransitionOption $option = null)
    {
        $relationship = $this->createEmptySelf();
        $relationship->setEvidence($refs);
        return $this->passOptionsTo('updateRelationship', array($relationship, Rel::EVIDENCE_REFERENCES), func_get_args());
    }

    /**
     * @param EvidenceReference     $reference
     * @param StateTransitionOption $option,...
     *
     * @return ChildAndParentsRelationshipState
     */
    public function updateEvidenceReference(EvidenceReference $reference, StateTransitionOption $option = null)
    {
        return $this->passOptionsTo('updateEvidenceReferences', array($reference), func_get_args());
    }

    /**
     * @param array                 $refs
     * @param StateTransitionOption $option,...
     *
     * @return ChildAndParentsRelationshipState
     */
    public function updateEvidenceReferences(array $refs, StateTransitionOption $option = null)
    {
        $relationship = $this->createEmptySelf();
        $relationship->setEvidence($refs);
        return $this->passOptionsTo('updateRelationship', array($relationship, Rel::EVIDENCE_REFERENCES), func_get_args());
    }

    /**
     * @param EvidenceReference     $reference
     * @param StateTransitionOption $option,...
     *
     * @return ChildAndParentsRelationshipState
     * @throws GedcomxApplicationException
     */
    public function deleteEvidenceReference(EvidenceReference $reference, StateTransitionOption $option = null)
    {
        $link = $reference->getLink(Rel::EVIDENCE_REFERENCE);
        $link = $link == null ? $reference->getLink(Rel::SELF) : $link;
        if ($link == null || $link->getHref() == null) {
            throw new GedcomxApplicationException("Evidence reference cannot be deleted: missing link.");
        }

        $request = $this->createAuthenticatedRequest(Request::DELETE, $link->getHref());
        FamilySearchRequest::applyFamilySearchMediaType($request);
        return $this->stateFactory->createState(
            'ChildAndParentsRelationshipState',
            $this->client,
            $request,
            $this->passOptionsTo('invoke', array($request), func_get_args()),
            $this->accessToken
        );
    }

    /**
     * @param Note                  $note
     * @param StateTransitionOption $option,...
     *
     * @return ChildAndParentsRelationshipState
     * @throws GedcomxApplicationException
     */
    public function readNote(Note $note, StateTransitionOption $option = null)
    {
        $link = $note->getLink(Rel::NOTE);
        $link = $link == null ? $note->getLink(Rel::SELF) : $link;
        if ($link == null || $link->getHref() == null) {
            throw new GedcomxApplicationException("Note cannot be read: missing link.");
        }

        $request = $this->createAuthenticatedRequest(Request::GET, $link->getHref());
        return $this->stateFactory->createState(
            'ChildAndParentsRelationshipState',
            $this->client,
            $request,
            $this->passOptionsTo('invoke', array($request), func_get_args()),
            $this->accessToken);
    }

    /**
     * @param Note                  $note
     * @param StateTransitionOption $option,...
     *
     * @return ChildAndParentsRelationshipState
     */
    public function addNote(Note $note, StateTransitionOption $option = null)
    {
        return $this->passOptionsTo('addNotes', array(array($note)), func_get_args());
    }

    /**
     * @param array                 $notes
     * @param StateTransitionOption $option,...
     *
     * @return ChildAndParentsRelationshipState
     */
    public function addNotes(array $notes, StateTransitionOption $option = null)
    {
        $relationship = $this->createEmptySelf();
        $relationship->setNotes($notes);
        return $this->passOptionsTo('updateRelationship', array($relationship, Rel::NOTES), func_get_args());
    }

    /**
     * @param Note                  $note
     * @param StateTransitionOption $option,...
     *
     * @return ChildAndParentsRelationshipState
     */
    public function updateNote(Note $note, StateTransitionOption $option = null)
    {
        return $this->passOptionsTo('updateNotes', array(array($note)), func_get_args());
    }

    /**
     * @param array                 $notes
     * @param StateTransitionOption $option,...
     *
     * @return ChildAndParentsRelationshipState
     */
    public function updateNotes(array $notes, StateTransitionOption $option = null)
    {
        $relationship = $this->createEmptySelf();
        $relationship->setNotes($notes);
        return $this->passOptionsTo('updateRelationship', array($relationship, Rel::NOTES), func_get_args());
    }

    /**
     * @param Note                  $note
     * @param StateTransitionOption $option,...
     *
     * @return ChildAndParentsRelationshipState
     * @throws GedcomxApplicationException
     */
    public function deleteNote(Note $note, StateTransitionOption $option = null)
    {
        $link = $note->getLink(Rel::NOTE);
        $link = $link == null ? $note->getLink(Rel::SELF) : $link;
        if ($link == null || $link->getHref() == null) {
            throw new GedcomxApplicationException("Note cannot be deleted: missing link.");
        }

        $request = $this->createAuthenticatedRequest(Request::DELETE, $link->getHref());
        return $this->stateFactory->createState(
            'ChildAndParentsRelationshipState',
            $this->client,
            $request,
            $this->passOptionsTo('invoke', array($request), func_get_args()),
            $this->accessToken
        );
    }

    /**
     * @param StateTransitionOption $option,...
     *
     * @return ChangeHistoryState|null
     */
    public function readChangeHistory(StateTransitionOption $option = null)
    {
        $link = $this->getLink(Rel::CHANGE_HISTORY);
        if ($link == null || $link->getHref() == null) {
            return null;
        }

        $request = $this->createAuthenticatedFeedRequest(Request::GET, $link->getHref());
        return $this->stateFactory->createState(
            'ChangeHistoryState',
            $this->client,
            $request,
            $this->passOptionsTo('invoke', array($request), func_get_args()),
            $this->accessToken
        );
    }

    /**
     * @param StateTransitionOption $option,...
     *
     * @return \Gedcomx\Rs\Client\PersonState|null
     */
    public function readChild(StateTransitionOption $option = null)
    {
        $relationship = $this->getRelationship();
        if ($relationship == null) {
            return null;
        }

        $child = $relationship->getChild();
        if ($child == null || $child->getResource() == null) {
            return null;
        }

        $request = $this->createAuthenticatedRequest(Request::GET, $child->getResource());
        return $this->stateFactory->createState(
            'PersonState',
            $this->client,
            $request,
            $this->passOptionsTo('invoke', array($request), func_get_args()),
            $this->accessToken
        );
    }

    /**
     * @param StateTransitionOption $option,...
     *
     * @return PersonState|null
     */
    public function readFather(StateTransitionOption $option = null)
    {
        $relationship = $this->getRelationship();
        if ($relationship == null) {
            return null;
        }

        $father = $relationship->getFather();
        if ($father == null || $father->getResource() == null) {
            return null;
        }

        $request = $this->createAuthenticatedRequest(Request::GET, $father->getResource());
        return $this->stateFactory->createState(
            'PersonState',
            $this->client,
            $request,
            $this->passOptionsTo('invoke', array($request), func_get_args()),
            $this->accessToken
        );
    }

    /**
     * @param \Gedcomx\Rs\Client\PersonState                   $father
     * @param \Gedcomx\Rs\Client\Options\StateTransitionOption $option
     *
     * @return ChildAndParentsRelationshipState
     */
    public function updateFatherWithPersonState(PersonState $father, StateTransitionOption $option = null)
    {
        return $this->passOptionsTo('updateFather', array($father->getResourceReference()), func_get_args());
    }

    /**
     * @param \Gedcomx\Common\ResourceReference                $father
     * @param \Gedcomx\Rs\Client\Options\StateTransitionOption $option
     *
     * @return \Gedcomx\Extensions\FamilySearch\Rs\Client\FamilyTree\ChildAndParentsRelationshipState
     */
    public function updateFather(ResourceReference $father, StateTransitionOption $option = null)
    {
        $relationship = $this->createEmptySelf();
        $relationship->setFather($father);
        $fsp = new FamilySearchPlatform();
        $fsp->addChildAndParentsRelationship($relationship);
        $request = $this->createAuthenticatedRequest(Request::POST, $this->getSelfUri());
        /** @var EntityEnclosingRequest $request */
        $request->setBody($fsp->toJson());
        return $this->stateFactory->createState(
            'ChildAndParentsRelationshipState',
            $this->client,
            $request,
            $this->passOptionsTo('invoke', array($request), func_get_args()),
            $this->accessToken
        );
    }

    public function deleteFather(StateTransitionOption $option = null)
    {
        $link = $this->getLink(Rel::FATHER_ROLE);
        if ($link == null || $link->getHref() == null) {
            return null;
        }

        $request = $this->createAuthenticatedRequest(Request::DELETE, $link->getHref());
        return $this->stateFactory->createState(
            'ChildAndParentsRelationshipState',
            $this->client,
            $request,
            $this->passOptionsTo('invoke', array($request), func_get_args()),
            $this->accessToken
        );
    }

    public function readMother(StateTransitionOption $option = null)
    {
        $relationship = $this->getRelationship();
        if ($relationship == null) {
            return null;
        }

        $mother = $relationship->getMother();
        if ($mother == null || $mother->getResource() == null) {
            return null;
        }

        $request = $this->createAuthenticatedRequest(Request::GET, $mother->getResource());
        return $this->stateFactory->createState(
            'PersonState',
            $this->client,
            $request,
            $this->passOptionsTo('invoke', array($request), func_get_args()),
            $this->accessToken
        );
    }

    /**
     * @param \Gedcomx\Rs\Client\PersonState                   $mother
     * @param \Gedcomx\Rs\Client\Options\StateTransitionOption $option,...
     *
     * @return ChildAndParentsRelationshipState
     */
    public function updateMotherWithPersonState(PersonState $mother, StateTransitionOption $option = null)
    {
        return $this->passOptionsTo('updateMother', array($mother->getResourceReference()), func_get_args());
    }

    /**
     * @param \Gedcomx\Common\ResourceReference                $mother
     * @param \Gedcomx\Rs\Client\Options\StateTransitionOption $option,...
     *
     * @return \Gedcomx\Extensions\FamilySearch\Rs\Client\FamilyTree\ChildAndParentsRelationshipState
     */
    public function updateMother(ResourceReference $mother, StateTransitionOption $option = null)
    {
        $relationship = $this->createEmptySelf();
        $relationship->setMother($mother);
        $fsp = new FamilySearchPlatform();
        $fsp->addChildAndParentsRelationship($relationship);
        $request = $this->createAuthenticatedRequest(Request::POST, $this->getSelfUri());
        /** @var EntityEnclosingRequest $request */
        $request->setBody($fsp->toJson());
        FamilySearchRequest::applyFamilySearchMediaType($request);
        return $this->stateFactory->createState(
            'ChildAndParentsRelationshipState',
            $this->client,
            $request,
            $this->passOptionsTo('invoke', array($request), func_get_args()),
            $this->accessToken
        );
    }

    /**
     * @param \Gedcomx\Rs\Client\Options\StateTransitionOption $option,...
     *
     * @return ChildAndParentsRelationshipState|null
     */
    public function deleteMother(StateTransitionOption $option = null)
    {
        $link = $this->getLink(Rel::MOTHER_ROLE);
        if ($link == null || $link->getHref() == null) {
            return null;
        }

        $request = $this->createAuthenticatedRequest(Request::DELETE, $link->getHref());
        return $this->stateFactory->createState(
            'ChildAndParentsRelationshipState',
            $this->client,
            $request,
            $this->passOptionsTo('invoke', array($request), func_get_args()),
            $this->accessToken);
    }

    /**
     * @param \Gedcomx\Rs\Client\Options\StateTransitionOption $option,...
     *
     * @return ChildAndParentsRelationshipState|null
     */
    public function restore(StateTransitionOption $option = null)
    {
        $link = $this->getLink(Rel::RESTORE);
        if ($link == null || $link->getHref() == null) {
            return null;
        }

        $request = $this->createAuthenticatedRequest(Request::POST, $link->getHref());
        return $this->stateFactory->createState(
            'ChildAndParentsRelationshipState',
            $this->client,
            $request,
            $this->passOptionsTo('invoke', array($request), func_get_args()),
            $this->accessToken
        );
    }

    /**
     * Update a relationship passing in a Rel link to specify what needs to be updated.
     *
     * @param \Gedcomx\Extensions\FamilySearch\Platform\Tree\ChildAndParentsRelationship $relationship
     * @param string                                                                     $rel
     * @param \Gedcomx\Rs\Client\Options\StateTransitionOption                           $option
     *
     * @return ChildAndParentsRelationshipState
     */
    protected function updateRelationship(ChildAndParentsRelationship $relationship, $rel, StateTransitionOption $option = null)
    {
        $target = $this->getSelfUri();
        $link = $this->getLink($rel);
        if ($link != null && $link->getHref() != null) {
            $target = $link->getHref();
        }

        $gx = new FamilySearchPlatform();
        $gx->setChildAndParentsRelationships(array($relationship));
        $request = $this->createAuthenticatedRequest(Request::POST, $target);
        FamilySearchRequest::applyFamilySearchMediaType($request);
        /** @var EntityEnclosingRequest $request */
        $request->setBody($gx->toJson());

        return $this->stateFactory->createState(
            'ChildAndParentsRelationshipState',
            $this->client,
            $request,
            $this->passOptionsTo('invoke', array($request), func_get_args()),
            $this->accessToken
        );
    }

}