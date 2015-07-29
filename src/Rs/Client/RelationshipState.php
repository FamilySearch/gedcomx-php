<?php


namespace Gedcomx\Rs\Client;

use Gedcomx\Common\EvidenceReference;
use Gedcomx\Common\Note;
use Gedcomx\Conclusion\Fact;
use Gedcomx\Conclusion\Relationship;
use Gedcomx\Gedcomx;
use Gedcomx\Rs\Client\Exception\GedcomxApplicationException;
use Gedcomx\Rs\Client\Options\StateTransitionOption;
use Gedcomx\Source\SourceReference;
use GuzzleHttp\Client;
use Guzzle\Http\Message\EntityEnclosingRequest;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;

/**
 * The RelationshipState exposes management functions for a relationship.
 */
class RelationshipState extends GedcomxApplicationState
{
    /**
     * Constructs a relationship state using the specified client, request, response, access token, and state factory.
     *
     * @param \GuzzleHttp\Client             $client
     * @param \GuzzleHttp\Psr7\Request    $request
     * @param \GuzzleHttp\Psr7\Response   $response
     * @param string                          $accessToken
     * @param \Gedcomx\Rs\Client\StateFactory $stateFactory
     */
    function __construct(Client $client, Request $request, Response $response, $accessToken, StateFactory $stateFactory)
    {
        parent::__construct($client, $request, $response, $accessToken, $stateFactory);
    }

    /**
     * Clones the current state instance.
     *
     * @param \GuzzleHttp\Psr7\Request  $request
     * @param \GuzzleHttp\Psr7\Response $response
     *
     * @return \Gedcomx\Rs\Client\RelationshipState
     */
    protected function reconstruct(Request $request, Response $response)
    {
        return new RelationshipState($this->client, $request, $response, $this->accessToken, $this->stateFactory);
    }

    /**
     * Instantiates a new relationship and only sets the relationship ID to the current relationship's ID.
     * @return \Gedcomx\Conclusion\Relationship
     */
    protected function createEmptySelf()
    {
        $relationship = new Relationship();
        $relationship->setId($this->getLocalSelfId());
        return $relationship;
    }

    /**
     * Gets the current relationship ID.
     * @return null|string
     */
    protected function getLocalSelfId()
    {
        $me = $this->getRelationship();
        return $me == null ? null : $me->getId();
    }

    /**
     * Gets the entity represented by this state (if applicable). Not all responses produce entities.
     *
     * @return \Gedcomx\Gedcomx
     */
    protected function loadEntity()
    {
        $json = json_decode($this->response->getBody(), true);

        return new Gedcomx($json);
    }

    /**
     * Gets the main data element represented by this state instance.
     *
     * @return \Gedcomx\Conclusion\Relationship
     */
    protected function getScope()
    {
        return $this->getRelationship();
    }

    /**
     * Gets the first relationship of the current relationships.
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
     * Adds a fact to the current relationship.
     *
     * @param Fact                          $fact
     * @param Options\StateTransitionOption $option,...
     *
     * @return \Gedcomx\Rs\Client\RelationshipState
     */
    public function addFact(Fact $fact, StateTransitionOption $option = null)
    {
        return $this->passOptionsTo('addFacts', array(array($fact)), func_get_args());
    }

    /**
     * Adds facts to the current relationship.
     *
     * @param Fact[]                        $facts
     * @param Options\StateTransitionOption $option,...
     *
     * @return \Gedcomx\Rs\Client\RelationshipState
     */
    public function addFacts(array $facts, StateTransitionOption $option = null)
    {
        $relationship = $this->createEmptySelf();
        $relationship->setFacts($facts);
        return $this->passOptionsTo('updateRelationship', array($relationship, REL::CONCLUSIONS), func_get_args());
    }

    /**
     * Updates the fact of the current relationship.
     *
     * @param Fact                          $fact
     * @param Options\StateTransitionOption $option,...
     *
     * @return \Gedcomx\Rs\Client\RelationshipState
     */
    public function updateFact(Fact $fact, StateTransitionOption $option = null)
    {
        return $this->passOptionsTo('updateFacts', array(array($fact)), func_get_args());
    }

    /**
     * Updates the facts of the current relationship.
     *
     * @param Fact[]                        $facts
     * @param Options\StateTransitionOption $option,...
     *
     * @return \Gedcomx\Rs\Client\RelationshipState
     */
    public function updateFacts(array $facts, StateTransitionOption $option = null)
    {
        $relationship = $this->createEmptySelf();
        $relationship->setFacts($facts);
        return $this->passOptionsTo('updateRelationship', array($relationship, REL::CONCLUSIONS), func_get_args());
    }

    /**
     * Deletes the fact of the current relationship.
     *
     * @param Fact                          $fact
     * @param Options\StateTransitionOption $option,...
     *
     * @throws GedcomxApplicationException
     * @return \Gedcomx\Rs\Client\RelationshipState
     */
    public function deleteFact(Fact $fact, StateTransitionOption $option = null)
    {
        $link = $fact->getLink(Rel::CONCLUSION);
        $link = $link == null ? $fact->getLink(Rel::SELF) : $link;
        if ($link == null || $link->getHref() == null) {
            throw new GedcomxApplicationException("Conclusion cannot be deleted: missing link.");
        }

        $request = $this->createAuthenticatedGedcomxRequest('DELETE', $link->getHref());
        return $this->stateFactory->createState(
            'RelationshipState',
            $this->client,
            $request,
            $this->passOptionsTo('invoke', array($request), func_get_args()),
            $this->accessToken
        );
    }

    /**
     * Adds the specified source reference (in the SourceDescriptionState) to the current relationship.
     *
     * @param \Gedcomx\Rs\Client\SourceDescriptionState $source
     * @param Options\StateTransitionOption             $option,...
     *
     * @return \Gedcomx\Rs\Client\RelationshipState
     */
    public function addSourceDescriptionState(SourceDescriptionState $source, StateTransitionOption $option = null)
    {
        $reference = new SourceReference();
        $reference->setDescriptionRef($source->getSelfUri());
        return $this->passOptionsTo('addSourceReferences', array(array($reference)), func_get_args());
    }

    /**
     * Adds the specified source reference to the current relationship.
     *
     * @param \Gedcomx\Source\SourceReference $reference
     * @param Options\StateTransitionOption   $option,...
     *
     * @return \Gedcomx\Rs\Client\RelationshipState
     */
    public function addSourceReference(SourceReference $reference, StateTransitionOption $option = null)
    {
        return $this->passOptionsTo('addSourceReferences', array(array($reference)), func_get_args());
    }

    /**
     * Adds the specified source references to the current relationship.
     *
     * @param \Gedcomx\Source\SourceReference[] $refs
     * @param Options\StateTransitionOption     $option,...
     *
     * @return \Gedcomx\Rs\Client\RelationshipState
     */
    public function addSourceReferences(array $refs, StateTransitionOption $option = null)
    {
        $relationship = $this->createEmptySelf();
        $relationship->setSources($refs);
        return $this->passOptionsTo('updateRelationship', array($relationship, Rel::SOURCE_REFERENCES), func_get_args());

    }

    /**
     * Updates the specified source reference for the current relationship.
     *
     * @param \Gedcomx\Source\SourceReference $reference
     * @param Options\StateTransitionOption   $option,...
     *
     * @internal param \Gedcomx\Source\SourceReference $sourceReference
     * @return \Gedcomx\Rs\Client\RelationshipState
     */
    public function updateSourceReference(SourceReference $reference, StateTransitionOption $option = null)
    {
        return $this->passOptionsTo('updateSourceReferences', array(array($reference)), func_get_args());
    }

    /**
     * Updates the specified source references for the current relationship.
     *
     * @param array                         $refs
     * @param Options\StateTransitionOption $option,...
     *
     * @return \Gedcomx\Rs\Client\RelationshipState
     */
    public function updateSourceReferences(array $refs, StateTransitionOption $option = null)
    {
        $relationship = $this->createEmptySelf();
        $relationship->setSources($refs);
        return $this->passOptionsTo('updateRelationship', array($relationship, Rel::SOURCE_REFERENCES), func_get_args());

    }

    /**
     * Deletes the specified source reference from the current relationship.
     *
     * @param \Gedcomx\Source\SourceReference $reference
     * @param Options\StateTransitionOption   $option,...
     *
     * @throws Exception\GedcomxApplicationException
     * @return \Gedcomx\Rs\Client\RelationshipState
     */
    public function deleteSourceReference(SourceReference $reference, StateTransitionOption $option = null)
    {
        $link = $reference->getLink(Rel::SOURCE_REFERENCE);
        $link = $link == null ? $reference->getLink(Rel::SELF) : $link;
        if ($link == null || $link->getHref() == null) {
            throw new GedcomxApplicationException("Source reference cannot be deleted: missing link.");
        }

        $request = $this->createAuthenticatedGedcomxRequest('DELETE', $link->getHref());
        return $this->stateFactory->createState(
            'RelationshipState',
            $this->client,
            $request,
            $this->passOptionsTo('invoke', array($request), func_get_args()),
            $this->accessToken
        );
    }

    /**
     * Adds a media reference to the current relationship.
     *
     * @param SourceDescriptionState $description
     * @param StateTransitionOption  $option,...
     *
     * @return \Gedcomx\Rs\Client\RelationshipState
     */
    public function addMediaReferenceState(SourceDescriptionState $description, StateTransitionOption $option = null)
    {
        $reference = new SourceReference();
        $reference->setDescriptionRef($description->getSelfUri());
        return $this->passOptionsTo('addMediaReference', array($reference), func_get_args());
    }

    /**
     * Adds a media reference to the current relationship.
     *
     * @param SourceReference       $reference
     * @param StateTransitionOption $option,...
     *
     * @return \Gedcomx\Rs\Client\RelationshipState
     */
    public function addMediaReference(SourceReference $reference, StateTransitionOption $option = null)
    {
        return $this->passOptionsTo('addMediaReferences', array(array($reference)), func_get_args());
    }

    /**
     * Adds media references to the current relationship.
     *
     * @param array                 $refs
     * @param StateTransitionOption $option,...
     *
     * @return \Gedcomx\Rs\Client\RelationshipState
     */
    public function addMediaReferences(array $refs, StateTransitionOption $option = null)
    {
        $relationship = $this->createEmptySelf();
        $relationship->setMedia($refs);
        return $this->passOptionsTo('updateRelationship', array($relationship, Rel::MEDIA_REFERENCES), func_get_args());
    }

    /**
     * Updates the media reference for the current relationship.
     *
     * @param SourceReference       $reference
     * @param StateTransitionOption $option,...
     *
     * @return \Gedcomx\Rs\Client\RelationshipState
     */
    public function updateMediaReference(SourceReference $reference, StateTransitionOption $option = null)
    {
        return $this->passOptionsTo('updateMediaReferences', array(array($reference)), func_get_args());
    }

    /**
     * Updates the media references for the current relationship.
     *
     * @param array                 $refs
     * @param StateTransitionOption $option,...
     *
     * @return \Gedcomx\Rs\Client\RelationshipState
     */
    public function updateMediaReferences(array $refs, StateTransitionOption $option = null)
    {
        $relationship = $this->createEmptySelf();
        $relationship->setMedia($refs);
        return $this->passOptionsTo('updateRelationship', array($relationship, Rel::MEDIA_REFERENCES), func_get_args());
    }

    /**
     * Deletes the specified media reference from the current relationship.
     *
     * @param SourceReference       $reference
     * @param StateTransitionOption $option,...
     *
     * @return \Gedcomx\Rs\Client\RelationshipState
     * @throws Exception\GedcomxApplicationException
     */
    public function deleteMediaReference(SourceReference $reference, StateTransitionOption $option = null)
    {
        $link = $reference->getLink(Rel::MEDIA_REFERENCE);
        $link = $link == null ? $reference->getLink(Rel::SELF) : $link;
        if ($link == null || $link->getHref() == null) {
            throw new GedcomxApplicationException("Media reference cannot be deleted: missing link.");
        }

        $request = $this->createAuthenticatedGedcomxRequest('DELETE', $link->getHref());
        return $this->stateFactory->createState(
            'RelationshipState',
            $this->client,
            $request,
            $this->passOptionsTo('invoke', array($request), func_get_args()),
            $this->accessToken
        );
    }


    /**
     * Adds an evidence reference to the current relationship.
     *
     * @param RelationshipState     $evidence
     * @param StateTransitionOption $option,...
     *
     * @return \Gedcomx\Rs\Client\RelationshipState
     */
    public function addEvidenceReferenceState(RelationshipState $evidence, StateTransitionOption $option = null)
    {
        $reference = new EvidenceReference();
        $reference->setResource($evidence->getSelfUri());
        return $this->passOptionsTo('addEvidenceReference', array($reference), func_get_args());
    }

    /**
     * Adds an evidence reference to the current relationship.
     *
     * @param EvidenceReference     $reference
     * @param StateTransitionOption $option,...
     *
     * @return \Gedcomx\Rs\Client\RelationshipState
     */
    public function addEvidenceReference(EvidenceReference $reference, StateTransitionOption $option = null)
    {
        return $this->passOptionsTo('addEvidenceReferences', array(array($reference)), func_get_args());
    }

    /**
     * Adds the evidence references to the current relationship.
     *
     * @param array                 $refs
     * @param StateTransitionOption $option,...
     *
     * @return \Gedcomx\Rs\Client\RelationshipState
     */
    public function addEvidenceReferences(array $refs, StateTransitionOption $option = null)
    {
        $relationship = $this->createEmptySelf();
        $relationship->setEvidence($refs);
        return $this->passOptionsTo('updateRelationship', array($relationship, Rel::EVIDENCE_REFERENCES), func_get_args());
    }

    /**
     * Update the evidence reference for the current relationship.
     *
     * @param EvidenceReference     $reference
     * @param StateTransitionOption $option,...
     *
     * @return \Gedcomx\Rs\Client\RelationshipState
     */
    public function updateEvidenceReference(EvidenceReference $reference, StateTransitionOption $option = null)
    {
        return $this->passOptionsTo('updateEvidenceReferences', array($reference), func_get_args());
    }

    /**
     * Updates the evidence references for the current relationship.
     *
     * @param array                 $refs
     * @param StateTransitionOption $option,...
     *
     * @return \Gedcomx\Rs\Client\RelationshipState
     */
    public function updateEvidenceReferences(array $refs, StateTransitionOption $option = null)
    {
        $relationship = $this->createEmptySelf();
        $relationship->setEvidence($refs);
        return $this->passOptionsTo('updateRelationship', array($relationship, Rel::EVIDENCE_REFERENCES), func_get_args());
    }

    /**
     * Deletes the evidence reference from the current relationship.
     *
     * @param EvidenceReference     $reference
     * @param StateTransitionOption $option,...
     *
     * @return \Gedcomx\Rs\Client\RelationshipState
     * @throws Exception\GedcomxApplicationException
     */
    public function deleteEvidenceReference(EvidenceReference $reference, StateTransitionOption $option = null)
    {
        $link = $reference->getLink(Rel::EVIDENCE_REFERENCE);
        $link = $link == null ? $reference->getLink(Rel::SELF) : $link;
        if ($link == null || $link->getHref() == null) {
            throw new GedcomxApplicationException("Evidence reference cannot be deleted: missing link.");
        }

        $request = $this->createAuthenticatedGedcomxRequest('DELETE', $link->getHref());
        return $this->stateFactory->createState(
            'RelationshipState',
            $this->client,
            $request,
            $this->passOptionsTo('invoke', array($request), func_get_args()),
            $this->accessToken
        );
    }

    /**
     * Gets the first note from the current relationship notes.
     * @return \Gedcomx\Common\Note|null
     */
    public function getNote()
    {
        $relationship = $this->getRelationship();
        if ($relationship != null) {
            if ($relationship->getNotes() != null && count($relationship->getNotes()) > 0) {
                $notes = $relationship->getNotes();

                return $notes[0];
            }
        }

        return null;
    }

    /**
     * Gets the first source reference from the current relationship sources.
     * @return \Gedcomx\Source\SourceReference|null
     */
    public function getSourceReference()
    {
        $relationship = $this->getRelationship();
        if ($relationship != null) {
            if ($relationship->getSources() != null && count($relationship->getSources()) > 0) {
                $sources = $relationship->getSources();

                return $sources[0];
            }
        }

        return null;
    }

    /**
     * Reads the specified note.
     *
     * @param Note                  $note
     * @param StateTransitionOption $options
     *
     * @return \Gedcomx\Rs\Client\RelationshipState
     * @throws Exception\GedcomxApplicationException
     */
    public function readNote(Note $note, StateTransitionOption $options = null)
    {
        $link = $note->getLink(Rel::NOTE);
        $link = $link == null ? $note->getLink(Rel::SELF) : $link;
        if ($link == null || $link->getHref() == null) {
            throw new GedcomxApplicationException("Note cannot be read: missing link.");
        }

        $request = $this->createAuthenticatedGedcomxRequest('GET', $link->getHref());
        return $this->stateFactory->createState(
            'RelationshipState',
            $this->client,
            $request,
            $this->passOptionsTo('invoke', array($request), func_get_args()),
            $this->accessToken
        );
    }


    /**
     * Adds a note to the current relationship.
     *
     * @param \Gedcomx\Common\Note                             $note
     * @param \Gedcomx\Rs\Client\Options\StateTransitionOption $option,...
     *
     * @return \Gedcomx\Rs\Client\RelationshipState
     */
    public function addNote(Note $note, StateTransitionOption $option = null)
    {
        return $this->passOptionsTo('addNotes', array(array($note)), func_get_args());
    }

    /**
     * Add the notes to the current relationship.
     *
     * @param \Gedcomx\Common\Note[]                           $notes
     * @param \Gedcomx\Rs\Client\Options\StateTransitionOption $option,...
     *
     * @return \Gedcomx\Rs\Client\RelationshipState
     */
    public function addNotes(array $notes, StateTransitionOption $option = null)
    {
        $relationship = $this->createEmptySelf();
        $relationship->setNotes($notes);
        return $this->passOptionsTo('updateRelationshipNotes', array($relationship), func_get_args());
    }

    /**
     * Updates the specified note for the current relationship.
     *
     * @param \Gedcomx\Common\Note                             $note
     * @param \Gedcomx\Rs\Client\Options\StateTransitionOption $option,...
     *
     * @return \Gedcomx\Rs\Client\RelationshipState
     */
    public function updateNote(Note $note, StateTransitionOption $option = null)
    {
        return $this->passOptionsTo('updateNotes', array(array($note)), func_get_args());
    }

    /**
     * Update the specified notes for the current relationship.
     *
     * @param \Gedcomx\Common\Note[]                           $notes
     * @param \Gedcomx\Rs\Client\Options\StateTransitionOption $option,...
     *
     * @return \Gedcomx\Rs\Client\RelationshipState
     */
    public function updateNotes(array $notes, StateTransitionOption $option = null)
    {
        $relationship = $this->createEmptySelf();
        $relationship->setNotes($notes);
        return $this->passOptionsTo('updateRelationshipNotes', array($relationship), func_get_args());
    }

    /**
     * Update the notes on the specified relationship.
     *
     * @param \Gedcomx\Conclusion\Relationship                 $relationship
     * @param \Gedcomx\Rs\Client\Options\StateTransitionOption $option,...
     *
     * @return \Gedcomx\Rs\Client\RelationshipState
     */
    public function updateRelationshipNotes(Relationship $relationship, StateTransitionOption $option = null)
    {
        $target = $this->getSelfUri();
        $link = $this->getLink(Rel::NOTES);
        if ($link != null && $link->getHref() != null) {
            $target = $link->getHref();
        }

        $gx = new Gedcomx();
        $gx->setRelationships(array($relationship));
        $request = $this->createAuthenticatedGedcomxRequest('POST', $target);
        /** @var EntityEnclosingRequest $request */
        $request->setBody($gx->toJson());
        return $this->stateFactory->createState(
            'RelationshipState',
            $this->client,
            $request,
            $this->passOptionsTo('invoke', array($request), func_get_args()),
            $this->accessToken
        );
    }

    /**
     * Delete the specified note from the current relationship.
     *
     * @param \Gedcomx\Common\Note                             $note
     * @param \Gedcomx\Rs\Client\Options\StateTransitionOption $option,...
     *
     * @throws GedcomxApplicationException
     * @return \Gedcomx\Rs\Client\RelationshipState
     */
    public function deleteNote(Note $note, StateTransitionOption $option = null)
    {
        $link = $note->getLink(Rel::NOTE);
        if ($link == null || $link->getHref() == null) {
            throw new GedcomxApplicationException("Note cannot be deleted: missing link.");
        }

        $request = $this->createAuthenticatedGedcomxRequest('DELETE', $link->getHref());
        return $this->stateFactory->createState(
            'RelationshipState',
            $this->client,
            $request,
            $this->passOptionsTo('invoke', array($request), func_get_args()),
            $this->accessToken
        );
    }

    /**
     * Updates the specified relationship.
     *
     * @param \Gedcomx\Conclusion\Relationship $relationship
     *
     * @return mixed
     */
    public function updateSelf(Relationship $relationship)
    {
        return $this->passOptionsTo('updateRelationship', array($relationship, Rel::SELF), func_get_args());
    }

    /**
     * Updates the specified relationship.
     *
     * @param \Gedcomx\Conclusion\Relationship                 $relationship
     * @param                                                  $rel
     * @param \Gedcomx\Rs\Client\Options\StateTransitionOption $option,...
     *
     * @return mixed
     */
    protected function updateRelationship(Relationship $relationship, $rel, StateTransitionOption $option = null)
    {
        $target = $this->getSelfUri();
        $link = $this->getLink($rel);
        if ($link != null && $link->getHref() != null) {
            $target = $link->getHref();
        }

        $gx = new Gedcomx();
        $gx->setRelationships(array($relationship));

        /** @var $request EntityEnclosingRequest */
        $request = $this->createAuthenticatedGedcomxRequest('POST', $target);
        $request->setBody($gx->toJson());

        return $this->stateFactory->createState(
            'RelationshipState',
            $this->client,
            $request,
            $this->passOptionsTo('invoke', array($request), func_get_args()),
            $this->accessToken
        );
    }

    /**
     * Loads conclusions for the current relationship.
     *
     * @param \Gedcomx\Rs\Client\Options\StateTransitionOption $option,...
     *
     * @return mixed
     */
    public function loadConclusions(StateTransitionOption $option = null)
    {
        return $this->passOptionsTo('loadEmbeddedResources', array(array(Rel::CONCLUSIONS)), func_get_args());
    }

    /**
     * Loads source references for the current relationship.
     *
     * @param \Gedcomx\Rs\Client\Options\StateTransitionOption $option,...
     *
     * @return mixed
     */
    public function loadSourceReferences(StateTransitionOption $option = null)
    {
        return $this->passOptionsTo('loadEmbeddedResources', array(array(Rel::SOURCE_REFERENCES)), func_get_args());
    }

    /**
     * Loads media references for the current relationship.
     *
     * @param \Gedcomx\Rs\Client\Options\StateTransitionOption $option,...
     *
     * @return mixed
     */
    public function loadMediaReferences(StateTransitionOption $option = null)
    {
        return $this->passOptionsTo('loadEmbeddedResources', array(array(Rel::MEDIA_REFERENCES)), func_get_args());
    }

    /**
     * Loads evidence references for the current relationship.
     *
     * @param \Gedcomx\Rs\Client\Options\StateTransitionOption $option,...
     *
     * @return mixed
     */
    public function loadEvidenceReferences(StateTransitionOption $option = null)
    {
        return $this->passOptionsTo('loadEmbeddedResources', array(array(Rel::EVIDENCE_REFERENCES)), func_get_args());
    }

    /**
     * Loads notes for the current relationship.
     *
     * @param \Gedcomx\Rs\Client\Options\StateTransitionOption $option,...
     *
     * @return mixed
     */
    public function loadNotes(StateTransitionOption $option = null)
    {
        return $this->passOptionsTo('loadEmbeddedResources', array(array(Rel::NOTES)), func_get_args());
    }

    /**
     * Loads the embedded resources for the specified links.
     *
     * @param array                                            $rels
     * @param \Gedcomx\Rs\Client\Options\StateTransitionOption $option,...
     *
     * @return $this
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
     * Gets the rel name for the current state instance.
     * @return string
     */
    public function getSelfRel()
    {
        return Rel::RELATIONSHIP;
    }
}