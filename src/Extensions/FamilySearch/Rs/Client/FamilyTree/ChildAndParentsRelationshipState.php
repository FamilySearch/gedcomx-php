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
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;

/**
 * Class ChildAndParentsRelationshipState
 *
 * @package Gedcomx\Extensions\FamilySearch\Rs\Client\FamilyTree
 *
 *          The ChildAndParentsRelationshipState exposes management and other FamilySearch specific functions for a children and parents.
 *
 */
class ChildAndParentsRelationshipState extends FamilySearchCollectionState implements PreferredRelationshipState
{

    /**
     * Clones the current instance of ChildAndParentsRelationshipState
     *
     * @param \GuzzleHttp\Psr7\Request  $request
     * @param \GuzzleHttp\Psr7\Response $response
     *
     * @return \Gedcomx\Extensions\FamilySearch\Rs\Client\FamilyTree\ChildAndParentsRelationshipState
     */
    protected function reconstruct(Request $request, Response $response)
    {
        return new ChildAndParentsRelationshipState($this->client, $request, $response, $this->accessToken, $this->stateFactory);
    }

    /**
     * Returns the relationship object represented by this state, if any
     *
     * @return \Gedcomx\Extensions\FamilySearch\Platform\Tree\ChildAndParentsRelationship|null
     */
    protected function getScope()
    {
        return $this->getRelationship();
    }

    /**
     * Define the rel for this state as a fallback if it cannot be determined from the state data
     *
     * @return string
     */
    public function getSelfRel()
    {
        return Rel::RELATIONSHIP;
    }

    /**
     * Returns the relationship object represented by this state, if any
     *
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
     * Gets the first conclusion for this relationship. FatherFact is returned first if it is not null; otherwise, MotherFact is returned.
     *
     * @return Conclusion
     */
    public function getConclusion()
    {
        return $this->getFatherFact() != null ? $this->getFatherFact()
            : $this->getMotherFact() != null ? $this->getMotherFact()
                : null;
    }

    /**
     * Return the first fact associated with the father
     *
     * @return \Gedcomx\Conclusion\Fact|null
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
     * Return the first fact associated with the mother
     *
     * @return \Gedcomx\Conclusion\Fact|null
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
     * Gets the first Note from the current Relationship.
     *
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
     * Return the first source reference for this relationship
     *
     * @return \Gedcomx\Source\SourceReference|null
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
     * Return the first evidence reference for this relationship
     *
     * @return \Gedcomx\Common\EvidenceReference|null
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
     * Return the first media reference for this relationship
     *
     * @return \Gedcomx\Source\SourceReference|null
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
     * Get a copy of this object's collection state
     *
     * @param \Gedcomx\Rs\Client\Options\StateTransitionOption $option,...
     *
     * @return \Gedcomx\Extensions\FamilySearch\Rs\Client\FamilyTree\FamilyTreeCollectionState|null
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
     * Create a request object to retrieve embedded resources
     *
     * @param string              $method
     * @param \Gedcomx\Links\Link $link
     *
     * @return \GuzzleHttp\Psr7\Request
     */
    protected function createRequestForEmbeddedResource($method, Link $link)
    {
        $request = $this->createAuthenticatedGedcomxRequest($method, $link->getHref());
        FamilySearchRequest::applyFamilySearchMediaType($request);

        return $request;
    }

    /**
     * Load an embedded resource for this relationship
     *
     * @param \Gedcomx\Rs\Client\Options\StateTransitionOption $option,...
     *
     * @return $this
     */
    public function loadEmbeddedResource(StateTransitionOption $option = null)
    {
        $this->passOptionsTo('includeEmbeddedResources', array($this->entity), func_get_args());
        return $this;
    }

    /**
     * Load the embedded resources for this relationship
     *
     * @param array                                            $rels
     * @param \Gedcomx\Rs\Client\Options\StateTransitionOption $option,...
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
     * Load conclusion data for this relationship
     *
     * @param \Gedcomx\Rs\Client\Options\StateTransitionOption $option,...
     *
     * @return ChildAndParentsRelationshipState
     */
    public function loadConclusions(StateTransitionOption $option = null)
    {
        return $this->passOptionsTo('loadEmbeddedResources', array(array(Rel::CONCLUSIONS)), func_get_args());
    }

    /**
     * Load source references for this relationship
     *
     * @param \Gedcomx\Rs\Client\Options\StateTransitionOption $option,...
     *
     * @return ChildAndParentsRelationshipState
     */
    public function loadSourceReferences(StateTransitionOption $option = null)
    {
        return $this->passOptionsTo('loadEmbeddedResources', array(array(Rel::SOURCE_REFERENCES)), func_get_args());
    }

    /**
     * Load media references for this relationship
     *
     * @param \Gedcomx\Rs\Client\Options\StateTransitionOption $option,...
     *
     * @return ChildAndParentsRelationshipState
     */
    public function loadMediaReferences(StateTransitionOption $option = null)
    {
        return $this->passOptionsTo('loadEmbeddedResources', array(array(Rel::MEDIA_REFERENCES)), func_get_args());
    }

    /**
     * Load evidence references for this relationship
     *
     * @param \Gedcomx\Rs\Client\Options\StateTransitionOption $option,...
     *
     * @return ChildAndParentsRelationshipState
     */
    public function loadEvidenceReferences(StateTransitionOption $option = null)
    {
        return $this->passOptionsTo('loadEmbeddedResources', array(array(Rel::EVIDENCE_REFERENCES)), func_get_args());
    }

    /**
     * Load the notes for this relationship
     *
     * @param \Gedcomx\Rs\Client\Options\StateTransitionOption $option,...
     *
     * @return ChildAndParentsRelationshipState
     */
    public function loadNotes(StateTransitionOption $option = null)
    {
        return $this->passOptionsTo('loadEmbeddedResources', array(array(Rel::NOTES)), func_get_args());
    }

    /**
     * Create an copy of this state with no data.
     *
     * @return ChildAndParentsRelationship
     */
    protected function createEmptySelf()
    {
        $relationship = new ChildAndParentsRelationship();
        $relationship->setId($this->getLocalSelfId());
        return $relationship;
    }

    /**
     * Get the ID of this relationship. Returns null if get() has not been called to hydrate the relationship.
     *
     * @return null|string
     */
    protected function getLocalSelfId()
    {
        $me = $this->getRelationship();
        return $me == null ? null : $me->getId();
    }

    /**
     * Add a fact to the father in this relationship
     *
     * @param \Gedcomx\Conclusion\Fact                         $fact
     * @param \Gedcomx\Rs\Client\Options\StateTransitionOption $option,...
     *
     * @return ChildAndParentsRelationshipState
     */
    public function addFatherFact(Fact $fact, StateTransitionOption $option = null)
    {
        return $this->passOptionsTo('addFatherFacts', array(array($fact)), func_get_args());
    }

    /**
     * Add a list of facts to the father in this relationship
     *
     * @param \Gedcomx\Conclusion\Fact[]                       $facts
     * @param \Gedcomx\Rs\Client\Options\StateTransitionOption $option,...
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
     * Update the specified father fact on the current relationship.
     *
     * @param \Gedcomx\Conclusion\Fact                         $fact
     * @param \Gedcomx\Rs\Client\Options\StateTransitionOption $option,...
     *
     * @return ChildAndParentsRelationshipState
     */
    public function updateFatherFact(Fact $fact, StateTransitionOption $option = null)
    {
        return $this->passOptionsTo('updateFatherFacts', array(array($fact)), func_get_args());
    }

    /**
     * Update a list of facts on the father.
     *
     * @param \Gedcomx\Conclusion\Fact[]                                            $facts
     * @param \Gedcomx\Rs\Client\Options\StateTransitionOption $option,...
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
     * Add a fact to the mother in this relationship
     *
     * @param \Gedcomx\Conclusion\Fact                         $fact
     * @param \Gedcomx\Rs\Client\Options\StateTransitionOption $option,...
     *
     * @return ChildAndParentsRelationshipState
     */
    public function addMotherFact(Fact $fact, StateTransitionOption $option = null)
    {
        return $this->passOptionsTo('addMotherFacts', array(array($fact)), func_get_args());
    }

    /**
     * Add a list of facts to the mother in this relationship
     *
     * @param \Gedcomx\Conclusion\Fact[]                                            $facts
     * @param \Gedcomx\Rs\Client\Options\StateTransitionOption $option,...
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
     * Update a fact on the mother in this relationship
     *
     * @param \Gedcomx\Conclusion\Fact                         $fact
     * @param \Gedcomx\Rs\Client\Options\StateTransitionOption $option,...
     *
     * @return ChildAndParentsRelationshipState
     */
    public function updateMotherFact(Fact $fact, StateTransitionOption $option = null)
    {
        return $this->passOptionsTo('updateMotherFacts', array(array($fact)), func_get_args());
    }

    /**
     * Update a list of facts on the mother in this relationship
     *
     * @param \Gedcomx\Conclusion\Fact[]                                            $facts
     * @param \Gedcomx\Rs\Client\Options\StateTransitionOption $option,...
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
     * Delete a fact from this relationship
     *
     * @param \Gedcomx\Conclusion\Fact                         $fact
     * @param \Gedcomx\Rs\Client\Options\StateTransitionOption $option,...
     *
     * @return ChildAndParentsRelationshipState
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
     * Add a source reference to this relationship using a state object
     *
     * @param \Gedcomx\Rs\Client\SourceDescriptionState        $source
     * @param \Gedcomx\Rs\Client\Options\StateTransitionOption $option,...
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
     * Add a source reference to this relationship using a SourceReference object
     *
     * @param \Gedcomx\Source\SourceReference                  $reference
     * @param \Gedcomx\Rs\Client\Options\StateTransitionOption $option,...
     *
     * @return ChildAndParentsRelationshipState
     */
    public function addSourceReference(SourceReference $reference, StateTransitionOption $option = null)
    {
        return $this->passOptionsTo('addSourceReferences', array(array($reference)), func_get_args());
    }

    /**
     * Add a list of source references to this relationship
     *
     * @param \Gedcomx\Source\SourceReference[]                $refs
     * @param \Gedcomx\Rs\Client\Options\StateTransitionOption $option,...
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
     * Update a source reference on this relationship
     *
     * @param \Gedcomx\Source\SourceReference                  $reference
     * @param \Gedcomx\Rs\Client\Options\StateTransitionOption $option,...
     *
     * @return ChildAndParentsRelationshipState
     */
    public function updateSourceReference(SourceReference $reference, StateTransitionOption $option = null)
    {
        return $this->passOptionsTo('updateSourceReferences', array(array($reference)), func_get_args());
    }

    /**
     * Update a list of source references on this relationship
     *
     * @param \Gedcomx\Source\SourceReference[]                $refs
     * @param \Gedcomx\Rs\Client\Options\StateTransitionOption $option,...
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
     * Delete a source reference from this relationship
     *
     * @param \Gedcomx\Source\SourceReference                  $reference
     * @param \Gedcomx\Rs\Client\Options\StateTransitionOption $option,...
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
     * Add a media reference to this relationship
     *
     * @param \Gedcomx\Source\SourceReference                  $reference
     * @param \Gedcomx\Rs\Client\Options\StateTransitionOption $option,...
     *
     * @return ChildAndParentsRelationshipState
     */
    public function addMediaReference(SourceReference $reference, StateTransitionOption $option = null)
    {
        return $this->passOptionsTo('addMediaReferences', array(array($reference)), func_get_args());
    }

    /**
     * Add a list of media references to this relationship
     *
     * @param \Gedcomx\Source\SourceReference[]                $refs
     * @param \Gedcomx\Rs\Client\Options\StateTransitionOption $option,...
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
     * Update a media reference on this relationship
     *
     * @param \Gedcomx\Source\SourceReference                  $reference
     * @param \Gedcomx\Rs\Client\Options\StateTransitionOption $option,...
     *
     * @return ChildAndParentsRelationshipState
     */
    public function updateMediaReference(SourceReference $reference, StateTransitionOption $option = null)
    {
        return $this->passOptionsTo('updateMediaReferences', array(array($reference)), func_get_args());
    }

    /**
     * Update a list of media references for this relationship
     *
     * @param \Gedcomx\Source\SourceReference[]                $refs
     * @param \Gedcomx\Rs\Client\Options\StateTransitionOption $option,...
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
     * Delete a media reference from this relationship
     *
     * @param \Gedcomx\Source\SourceReference                  $reference
     * @param \Gedcomx\Rs\Client\Options\StateTransitionOption $option,...
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
     * And an evidence reference to this relationship
     *
     * @param \Gedcomx\Common\EvidenceReference                $reference
     * @param \Gedcomx\Rs\Client\Options\StateTransitionOption $option,...
     *
     * @return ChildAndParentsRelationshipState
     */
    public function addEvidenceReference(EvidenceReference $reference, StateTransitionOption $option = null)
    {
        return $this->passOptionsTo('addEvidenceReferences', array(array($reference)), func_get_args());
    }

    /**
     * Add a list of evidence references to this relationship
     *
     * @param \Gedcomx\Common\EvidenceReference[]              $refs
     * @param \Gedcomx\Rs\Client\Options\StateTransitionOption $option,...
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
     * Update an evidence reference on this relationship
     *
     * @param \Gedcomx\Common\EvidenceReference                $reference
     * @param \Gedcomx\Rs\Client\Options\StateTransitionOption $option,...
     *
     * @return ChildAndParentsRelationshipState
     */
    public function updateEvidenceReference(EvidenceReference $reference, StateTransitionOption $option = null)
    {
        return $this->passOptionsTo('updateEvidenceReferences', array($reference), func_get_args());
    }

    /**
     * Update a list of evidence references on this relationship
     *
     * @param \Gedcomx\Common\EvidenceReference[]              $refs
     * @param \Gedcomx\Rs\Client\Options\StateTransitionOption $option,...
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
     * Delete an evidence reference from this relationship
     *
     * @param \Gedcomx\Common\EvidenceReference                $reference
     * @param \Gedcomx\Rs\Client\Options\StateTransitionOption $option,...
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
     * Read a specific note on this relationship
     *
     * @param \Gedcomx\Common\Note                             $note
     * @param \Gedcomx\Rs\Client\Options\StateTransitionOption $option,...
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
     * Add a note to this relationship
     *
     * @param \Gedcomx\Common\Note                             $note
     * @param \Gedcomx\Rs\Client\Options\StateTransitionOption $option,...
     *
     * @return ChildAndParentsRelationshipState
     */
    public function addNote(Note $note, StateTransitionOption $option = null)
    {
        return $this->passOptionsTo('addNotes', array(array($note)), func_get_args());
    }

    /**
     * Add a list of notes to this relationship
     *
     * @param \Gedcomx\Common\Note[]                           $notes
     * @param \Gedcomx\Rs\Client\Options\StateTransitionOption $option,...
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
     * Update a note on this relationship
     *
     * @param \Gedcomx\Common\Note                             $note
     * @param \Gedcomx\Rs\Client\Options\StateTransitionOption $option,...
     *
     * @return ChildAndParentsRelationshipState
     */
    public function updateNote(Note $note, StateTransitionOption $option = null)
    {
        return $this->passOptionsTo('updateNotes', array(array($note)), func_get_args());
    }

    /**
     * Update a list of notes on this relationship
     *
     * @param \Gedcomx\Common\Note[]                           $notes
     * @param \Gedcomx\Rs\Client\Options\StateTransitionOption $option,...
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
     * Delete a note from this relationship
     *
     * @param \Gedcomx\Common\Note                             $note
     * @param \Gedcomx\Rs\Client\Options\StateTransitionOption $option,...
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
     * Read the change history for this relationship
     *
     * @param \Gedcomx\Rs\Client\Options\StateTransitionOption $option,...
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
     * Read the child person data from this relationship
     *
     * @param \Gedcomx\Rs\Client\Options\StateTransitionOption $option,...
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
     * Read the father person data from this relationship
     *
     * @param \Gedcomx\Rs\Client\Options\StateTransitionOption $option,...
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
     * Update the father on this relationship using a PersonState
     *
     * @param \Gedcomx\Rs\Client\PersonState                   $father
     * @param \Gedcomx\Rs\Client\Options\StateTransitionOption $option,...
     *
     * @return ChildAndParentsRelationshipState
     */
    public function updateFatherWithPersonState(PersonState $father, StateTransitionOption $option = null)
    {
        return $this->passOptionsTo('updateFather', array($father->getResourceReference()), func_get_args());
    }

    /**
     * Update the father on this relationship with a ResourceReference
     *
     * @param \Gedcomx\Common\ResourceReference                $father
     * @param \Gedcomx\Rs\Client\Options\StateTransitionOption $option,...
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

    /**
     * Remove the father from this relationship
     *
     * @param \Gedcomx\Rs\Client\Options\StateTransitionOption $option,...
     *
     * @return ChildAndParentsRelationshipState|null
     */
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

    /**
     * Read the mother person data from this relationship
     *
     * @param \Gedcomx\Rs\Client\Options\StateTransitionOption $option,...
     *
     * @return PersonState|null
     */
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
     * Update the mother on this relationship with a PersonState
     *
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
     * Update the mother on this relationship with a ResourceReference
     *
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
     * Remove the mother from this relationship
     *
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
     * Restore a previously deleted relationship
     *
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
     * @param \Gedcomx\Rs\Client\Options\StateTransitionOption                           $option,...
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