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
use Guzzle\Http\Client;
use Guzzle\Http\Message\EntityEnclosingRequest;
use Guzzle\Http\Message\Request;
use Guzzle\Http\Message\Response;
use RuntimeException;

class RelationshipState extends GedcomxApplicationState
{

    function __construct(Client $client, Request $request, Response $response, $accessToken, StateFactory $stateFactory)
    {
        parent::__construct($client, $request, $response, $accessToken, $stateFactory);
    }

    protected function reconstruct(Request $request, Response $response)
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
     * @param Fact                          $fact
     * @param Options\StateTransitionOption $option
     *
     * @return \Gedcomx\Rs\Client\RelationshipState
     */
    public function addFact(Fact $fact, StateTransitionOption $option = null)
    {
        return $this->passOptionsTo('addFacts', array(array($fact)), func_get_args());
    }

    /**
     * @param Fact[]                        $facts
     * @param Options\StateTransitionOption $option
     *
     * @return \Gedcomx\Rs\Client\RelationshipState
     */
    public function addFacts(array $facts, StateTransitionOption $option = null)
    {
        $relationship = $this->createEmptySelf();
        $relationship->setFacts($facts);
        return $this->passOptionsTo('updateConclusions', array($relationship), func_get_args());
    }

    /**
     * @param Fact                          $fact
     * @param Options\StateTransitionOption $option
     *
     * @return \Gedcomx\Rs\Client\RelationshipState
     */
    public function updateFact(Fact $fact, StateTransitionOption $option = null)
    {
        return $this->passOptionsTo('updateFacts', array(array($fact)), func_get_args());
    }

    /**
     * @param Fact[]                        $facts
     * @param Options\StateTransitionOption $option
     *
     * @return \Gedcomx\Rs\Client\RelationshipState
     */
    public function updateFacts(array $facts, StateTransitionOption $option = null)
    {
        $relationship = $this->createEmptySelf();
        $relationship->setFacts($facts);
        return $this->passOptionsTo('updateConclusions', array($relationship), func_get_args());
    }

    /**
     * @param Fact                          $fact
     * @param Options\StateTransitionOption $option
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

        $request = $this->createAuthenticatedGedcomxRequest(Request::DELETE, $link->getHref());
        return $this->stateFactory->createState(
            'RelationshipState',
            $this->client,
            $request,
            $this->passOptionsTo('invoke', array($request), func_get_args()),
            $this->accessToken
        );
    }

    /**
     * @param \Gedcomx\Rs\Client\SourceDescriptionsState $source
     * @param Options\StateTransitionOption              $option,...
     *
     * @return \Gedcomx\Rs\Client\RelationshipState
     */
    public function addSourceDescriptionState(SourceDescriptionsState $source, StateTransitionOption $option = null)
    {
        $reference = new SourceReference();
        $reference->setDescriptionRef($source->getSelfUri());
        return $this->passOptionsTo('addSourceReferences', array(array($reference)), func_get_args());
    }

    /**
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
     * @param \Gedcomx\Source\SourceReference[] $refs
     * @param Options\StateTransitionOption     $option
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
     * @param \Gedcomx\Source\SourceReference $reference
     * @param Options\StateTransitionOption   $option
     *
     * @internal param \Gedcomx\Source\SourceReference $sourceReference
     *
     * @return \Gedcomx\Rs\Client\RelationshipState
     */
    public function updateSourceReference(SourceReference $reference, StateTransitionOption $option = null)
    {
        return $this->passOptionsTo('updateSourceReferences', array($reference), func_get_args());
    }

    /**
     * @param array                         $refs
     * @param Options\StateTransitionOption $option
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
     * @param \Gedcomx\Source\SourceReference $reference
     * @param Options\StateTransitionOption   $option
     *
     * @throws Exception\GedcomxApplicationException
     * @internal param \Gedcomx\Source\SourceReference $sourceReference
     *
     * @return \Gedcomx\Rs\Client\RelationshipState
     */
    public function deleteSourceReference(SourceReference $reference, StateTransitionOption $option = null)
    {
        $link = $reference->getLink(Rel::SOURCE_REFERENCE);
        $link = $link == null ? $reference->getLink(Rel::SELF) : $link;
        if ($link == null || $link->getHref() == null) {
            throw new GedcomxApplicationException("Source reference cannot be deleted: missing link.");
        }

        $request = $this->createAuthenticatedGedcomxRequest(Request::DELETE, $link->getHref());
        return $this->stateFactory->createState(
            'RelationshipState',
            $this->client,
            $request,
            $this->passOptionsTo('invoke', array($request), func_get_args()),
            $this->accessToken
        );

    }

    /**
     * @param SourceDescriptionState $description
     * @param StateTransitionOption  $option
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
     * @param SourceReference       $reference
     * @param StateTransitionOption $option
     *
     * @return \Gedcomx\Rs\Client\RelationshipState
     */
    public function addMediaReference(SourceReference $reference, StateTransitionOption $option = null)
    {
        return $this->passOptionsTo('addMediaReferences', array(array($reference)), func_get_args());
    }

    /**
     * @param array                 $refs
     * @param StateTransitionOption $option
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
     * @param SourceReference       $reference
     * @param StateTransitionOption $option
     *
     * @return \Gedcomx\Rs\Client\RelationshipState
     */
    public function updateMediaReference(SourceReference $reference, StateTransitionOption $option = null)
    {
        return $this->passOptionsTo('updateMediaReferences', array(array($reference)), func_get_args());
    }

    /**
     * @param array                 $refs
     * @param StateTransitionOption $option
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
     * @param SourceReference       $reference
     * @param StateTransitionOption $option
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

        $request = $this->createAuthenticatedGedcomxRequest(Request::DELETE, $link->getHref());
        return $this->stateFactory->createState(
            'RelationshipState',
            $this->client,
            $request,
            $this->passOptionsTo('invoke', array($request), func_get_args()),
            $this->accessToken
        );
    }


    /**
     * @param RelationshipState     $evidence
     * @param StateTransitionOption $option
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
     * @param EvidenceReference     $reference
     * @param StateTransitionOption $option
     *
     * @return \Gedcomx\Rs\Client\RelationshipState
     */
    public function addEvidenceReference(EvidenceReference $reference, StateTransitionOption $option = null)
    {
        return $this->passOptionsTo('addEvidenceReferences', array(array($reference)), func_get_args());
    }

    /**
     * @param array                 $refs
     * @param StateTransitionOption $option
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
     * @param EvidenceReference     $reference
     * @param StateTransitionOption $option
     *
     * @return \Gedcomx\Rs\Client\RelationshipState
     */
    public function updateEvidenceReference(EvidenceReference $reference, StateTransitionOption $option = null)
    {
        return $this->passOptionsTo('updateEvidenceReferences', array($reference), func_get_args());
    }

    /**
     * @param array                 $refs
     * @param StateTransitionOption $option
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
     * @param EvidenceReference     $reference
     * @param StateTransitionOption $option
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

        $request = $this->createAuthenticatedGedcomxRequest(Request::DELETE, $link->getHref());
        return $this->stateFactory->createState(
            'RelationshipState',
            $this->client,
            $request,
            $this->passOptionsTo('invoke', array($request), func_get_args()),
            $this->accessToken
        );
    }

    /**
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
        $request = $this->createAuthenticatedGedcomxRequest(Request::POST, $target);
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

        $request = $this->createAuthenticatedGedcomxRequest(Request::DELETE, $link->getHref());
        return $this->stateFactory->createState(
            'RelationshipState',
            $this->client,
            $request,
            $this->passOptionsTo('invoke', array($request), func_get_args()),
            $this->accessToken
        );
    }

    protected function updateRelationship(Relationship $relationship, $rel, StateTransitionOption $option = null)
    {
        $target = $this->getSelfUri();
        $link = $this->getLink($rel);
        if ($link != null && $link->getHref() != null) {
            $target = $link->getHref();
        }

        $gx = new Gedcomx();
        $gx->setRelationships(array($relationship));

        $request = $this->createAuthenticatedGedcomxRequest(Request::POST, $target);
        return $this->stateFactory->createState(
            'RelationshipState',
            $this->client,
            $request,
            $this->passOptionsTo('invoke', array($request), func_get_args()),
            $this->accessToken
        );
    }


    /*
     * @return \Gedcomx\Conclusion\Relationship
     */
    protected function createEmptySelf()
    {
        $relationship = new Relationship();
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


}