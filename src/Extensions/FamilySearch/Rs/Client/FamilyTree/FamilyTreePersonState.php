<?php

namespace Gedcomx\Extensions\FamilySearch\Rs\Client\FamilyTree;

use Gedcomx\Conclusion\Person;
use Gedcomx\Extensions\FamilySearch\Rs\Client\DiscussionState;
use Gedcomx\Extensions\FamilySearch\Rs\Client\PersonMergeState;
use Gedcomx\Extensions\FamilySearch\Rs\Client\PersonNonMatchesState;
use Gedcomx\Extensions\FamilySearch\Platform\Tree\ChildAndParentsRelationship;
use Gedcomx\Extensions\FamilySearch\Platform\Tree\DiscussionReference;
use Gedcomx\Gedcomx;
use Gedcomx\Links\Link;
use Gedcomx\Rs\Client\Exception\GedcomxApplicationException;
use Gedcomx\Rs\Client\Options\StateTransitionOption;
use Gedcomx\Rs\Client\PersonState;
use Gedcomx\Extensions\FamilySearch\FamilySearchPlatform;
use Gedcomx\Extensions\FamilySearch\Rs\Client\Helpers\FamilySearchRequest;
use Gedcomx\Extensions\FamilySearch\Rs\Client\Rel;
use Gedcomx\Rs\Client\SourceDescriptionsState;
use Gedcomx\Rs\Client\Util\HttpStatus;
use GuzzleHttp\Client;
use Guzzle\Http\Message\EntityEnclosingRequest;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;

class FamilyTreePersonState extends PersonState
{
    /**
     * Create new instance of FamilyTreePersonState
     *
     * @param \GuzzleHttp\Client                                                          $client
     * @param \GuzzleHttp\Psr7\Request                                                 $request
     * @param \GuzzleHttp\Psr7\Response                                                $response
     * @param string                                                                       $accessToken
     * @param \Gedcomx\Extensions\FamilySearch\Rs\Client\FamilyTree\FamilyTreeStateFactory $stateFactory
     */
    public function __construct(Client $client, Request $request, Response $response, $accessToken, FamilyTreeStateFactory $stateFactory)
    {
        parent::__construct($client, $request, $response, $accessToken, $stateFactory);
    }

    /**
     * Clone this instance of FamilyTreePersonState
     *
     * @param \GuzzleHttp\Psr7\Request  $request
     * @param \GuzzleHttp\Psr7\Response $response
     *
     * @return \Gedcomx\Extensions\FamilySearch\Rs\Client\FamilyTree\FamilyTreePersonState
     */
    protected function  reconstruct(Request $request, Response $response)
    {
        /** @var \Gedcomx\Rs\Client\StateFactory $stateFactory */
        return new FamilyTreePersonState($this->client, $request, $response, $this->accessToken, $this->stateFactory);
    }

    /**
     * Parse the response body if we have an HTTP status that indicates one should be present.
     *
     * @return \Gedcomx\Extensions\FamilySearch\FamilySearchPlatform|null
     */
    protected function loadEntityConditionally()
    {
        if ($this->request->getMethod() =='GET'
            && ($this->response->getStatusCode() == HttpStatus::OK || $this->response->getStatusCode() == HttpStatus::GONE)
            || $this->response->getStatusCode() == HttpStatus::PRECONDITION_FAILED
        ) {
            return $this->loadEntity();
        } else {
            return null;
        }
    }

    /**
     * Parse the response body.
     *
     * @return \Gedcomx\Extensions\FamilySearch\FamilySearchPlatform
     */
    protected function loadEntity()
    {
        $json = json_decode($this->response->getBody(), true);

        return new FamilySearchPlatform($json);
    }

    /**
     * Return the Person conclusion objects on this state
     *
     * @return Person[]|null
     */
    public function getPersons()
    {
        return $this->getEntity() == null ? null : $this->getEntity()->getPersons();
    }

    /**
     * Return the ChildAndParentsRelationship objects on this state
     *
     * @return ChildAndParentsRelationship[]|null
     */
    public function getChildAndParentsRelationships()
    {
        return $this->getEntity() == null ? null : $this->getEntity()->getChildAndParentsRelationships();
    }

    /**
     * Return the ChildAndParentsRelationship objects where this person is the child
     *
     * @return ChildAndParentsRelationship[]|null
     */
    public function getRelationshipsToChildren()
    {
        $relationships = $this->getChildAndParentsRelationships();
        if ($relationships == null) {
            $relationships = array();
        }
        if (!empty($relationships)) {
            foreach ($relationships as $idx => $r) {
                if ($this->refersToMe($r->getChild())) {
                    unset($relationships[$idx]);
                }
            }
        }

        return $relationships;
    }

    /*
     * Return the ChildAndParentsRelationship objects where this person is the parent
     *
     * @return ChildAndParentsRelationship[]|null
     */
    public function getRelationshipsToParents()
    {
        $relationships = $this->getChildAndParentsRelationships();
        if ($relationships == null) {
            $relationships = array();
        }
        if (!empty($relationships)) {
            foreach ($relationships as $idx => $r) {
                if ($this->refersToMe($r->getFather()) || $this->refersToMe($r->getMother())) {
                    unset($relationships[$idx]);
                }
            }
        }

        return $relationships;
    }

    /**
     * Create a request object to pull in resource data
     *
     * @param string              $method
     * @param \Gedcomx\Links\Link $link
     *
     * @return Request
     */
    protected function createRequestForEmbeddedResource($method, Link $link)
    {
        $request = $this->createAuthenticatedGedcomxRequest($method, $link->getHref());
        if ($link->getRel() == Rel::DISCUSSION_REFERENCES) {
            FamilySearchRequest::applyFamilySearchMediaType($request);
        }

        return $request;
    }

    /**
     * Load discussion references for this person
     *
     * @param \Gedcomx\Rs\Client\Options\StateTransitionOption $option,...
     *
     * @return mixed
     */
    public function loadDiscussionReferences(StateTransitionOption $option = null)
    {
        return $this->passOptionsTo('loadEmbeddedResources', array(array(Rel::DISCUSSION_REFERENCES)), func_get_args());
    }

    /**
     * Load portraits associated with this person
     *
     * @param \Gedcomx\Rs\Client\Options\StateTransitionOption $option,...
     *
     * @return \Gedcomx\Rs\Client\SourceDescriptionsState
     */
    public function readPortraits(StateTransitionOption $option = null)
    {
        $link = $this->getLink(Rel::PORTRAITS);
        if ($link == null || $link->getHref() == null) {
            return null;
        }

        $request = $this->createAuthenticatedGedcomxRequest('GET', $link->getHref());

        return $this->stateFactory->createState(
            'SourceDescriptionsState',
            $this->client,
            $request,
            $this->passOptionsTo('invoke', array($request), func_get_args()),
            $this->accessToken
        );
    }

    /**
     * Read the portrait for this person
     *
     * @param \Gedcomx\Rs\Client\Options\StateTransitionOption $option,...
     *
     * @return \GuzzleHttp\Psr7\Response
     */
    public function readPortrait(StateTransitionOption $option = null)
    {
        $link = $this->getLink(Rel::PORTRAIT);
        if ($link == null || $link->getHref() == null) {
            return null;
        }

        $request = $this->createAuthenticatedGedcomxRequest('GET', $link->getHref());

        return $this->passOptionsTo('invoke', array($request), func_get_args());
    }

    /**
     * Add a discussion reference to this person using a state object
     *
     * @param \Gedcomx\Extensions\FamilySearch\Rs\Client\DiscussionState $discussion
     * @param \Gedcomx\Rs\Client\Options\StateTransitionOption $option,...
     *
     * @return FamilyTreePersonState
     */
    public function addDiscussionState(DiscussionState $discussion, StateTransitionOption $option = null)
    {
        $reference = new DiscussionReference();
        $reference->setResource($discussion->getSelfUri());

        return $this->passOptionsTo('addDiscussionReference', array($reference), func_get_args());
    }

    /**
     * Add a discussion reference to this person using a DiscussionReference object
     *
     * @param \Gedcomx\Extensions\FamilySearch\Platform\Tree\DiscussionReference $reference
     * @param \Gedcomx\Rs\Client\Options\StateTransitionOption                   $option,...
     *
     * @return FamilyTreePersonState
     */
    public function addDiscussionReference(DiscussionReference $reference, StateTransitionOption $option = null)
    {
        return $this->passOptionsTo('addDiscussionReferences', array(array($reference)), func_get_args());
    }

    /**
     * Add a list of discussion references to this person
     *
     * @param \Gedcomx\Extensions\FamilySearch\Platform\Tree\DiscussionReference[] $refs
     * @param \Gedcomx\Rs\Client\Options\StateTransitionOption                     $option,...
     *
     * @return FamilyTreePersonState
     */
    public function addDiscussionReferences(array $refs, StateTransitionOption $option = null)
    {
        return $this->passOptionsTo('updateDiscussionReferences', array($refs), func_get_args());
    }

    /**
     * Update a discussion reference on this person
     *
     * @param \Gedcomx\Extensions\FamilySearch\Platform\Tree\DiscussionReference $reference
     * @param \Gedcomx\Rs\Client\Options\StateTransitionOption                   $option,...
     *
     * @return FamilyTreePersonState
     */
    public function updateDiscussionReference(DiscussionReference $reference, StateTransitionOption $option = null)
    {
        return $this->passOptionsTo('updateDiscussionReferences', array(array($reference)), func_get_args());
    }

    /**
     * Update a list of discussion references on this person
     *
     * @param \Gedcomx\Extensions\FamilySearch\Platform\Tree\DiscussionReference[] $refs
     * @param \Gedcomx\Rs\Client\Options\StateTransitionOption                     $option,...
     *
     * @return FamilyTreePersonState
     */
    public function updateDiscussionReferences(array $refs, StateTransitionOption $option = null)
    {
        $person = $this->createEmptySelf();
        foreach ($refs as $ref) {
            $person->addExtensionElement($ref);
        }

        return $this->passOptionsTo('updatePersonDiscussionReferences', array($person), func_get_args());
    }

    /**
     * Update the discussion references on a Person object
     *
     * @param \Gedcomx\Conclusion\Person                       $person
     * @param \Gedcomx\Rs\Client\Options\StateTransitionOption $option,...
     *
     * @return FamilyTreePersonState
     */
    public function updatePersonDiscussionReferences(Person $person, StateTransitionOption $option = null)
    {
        $target = $this->getSelfUri();
        $link = $this->getLink(Rel::DISCUSSION_REFERENCES);
        if ($link != null && $link->getHref() != null) {
            $target = $link->getHref();
        }

        $gx = new Gedcomx();
        $gx->setPersons(array($person));

        $request = $this->createAuthenticatedRequest('POST', $target);
        $request->setBody($gx->toJson());
        FamilySearchRequest::applyFamilySearchMediaType($request);

        return $this->stateFactory->createState(
            'PersonState',
            $this->client,
            $request,
            $this->passOptionsTo('invoke', array($request), func_get_args()),
            $this->accessToken
        );
    }

    /**
     * Delete a discussion reference
     *
     * @param \Gedcomx\Extensions\FamilySearch\Platform\Tree\DiscussionReference $reference
     * @param \Gedcomx\Rs\Client\Options\StateTransitionOption $option,...
     *
     * @throws \Gedcomx\Rs\Client\Exception\GedcomxApplicationException
     * @return FamilyTreePersonState
     */
    public function  deleteDiscussionReference(DiscussionReference $reference, StateTransitionOption $option = null)
    {
        $link = null;
        if ($reference->getLinks() != null) {
            $link = $reference->getLink(Rel::DISCUSSION_REFERENCE);
            if ($link == null) {
                $link = $reference->getLink(Rel::SELF);
            }
        }
        if ($link == null) {
            $link = new Link();
            $link->setHref($reference->getResource());
        }
        if ($link == null || $link->getHref() == null) {
            throw new GedcomxApplicationException("Discussion reference cannot be deleted: missing link.");
        }

        $request = $this->createAuthenticatedRequest('DELETE', $link->getHref());
        FamilySearchRequest::applyFamilySearchMediaType($request);

        return $this->stateFactory->createState(
            'PersonState',
            $this->client,
            $request,
            $this->passOptionsTo('invoke', array($request), func_get_args()),
            $this->accessToken);
    }

    /**
     * Read the child and parent relationships for this person
     *
     * @param ChildAndParentsRelationship $relationship
     * @param \Gedcomx\Rs\Client\Options\StateTransitionOption $option,...
     *
     * @return ChildAndParentsRelationshipState
     */
    public function readChildAndParentsRelationship(ChildAndParentsRelationship $relationship, StateTransitionOption $option = null)
    {
        $link = $relationship->getLink(Rel::RELATIONSHIP);
        if ($link == null) {
            $link = $relationship->getLink(Rel::SELF);
        }
        if ($link == null || $link->getHref() == null) {
            return null;
        }

        $request = $this->createAuthenticatedRequest('GET', $link->getHref());
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
     * Read the change history for this person
     *
     * @param \Gedcomx\Rs\Client\Options\StateTransitionOption $option,...
     *
     * @return ChangeHistoryState
     */
    public function  readChangeHistory(StateTransitionOption $option = null)
    {
        $link = $this->getLink(Rel::CHANGE_HISTORY);
        if ($link == null || $link->getHref() == null) {
            return null;
        }

        $request = $this->createAuthenticatedFeedRequest('GET', $link->getHref());

        return $this->stateFactory->createState(
            'ChangeHistoryState',
            $this->client,
            $request,
            $this->passOptionsTo('invoke', array($request), func_get_args()),
            $this->accessToken
        );
    }

    /**
     * Get the person records flagged as possible matches to this record
     *
     * @param \Gedcomx\Rs\Client\Options\StateTransitionOption $option,...
     *
     * @return \Gedcomx\Extensions\FamilySearch\Rs\Client\PersonMatchResultsState
     */
    public function readMatches(StateTransitionOption $option = null)
    {
        $link = $this->getLink(Rel::MATCHES);
        if ($link == null || $link->getHref() == null) {
            return null;
        }

        $request = $this->createAuthenticatedFeedRequest('GET', $link->getHref());

        return $this->stateFactory->createState(
            'PersonMatchResultsState',
            $this->client,
            $request,
            $this->passOptionsTo('invoke', array($request), func_get_args()),
            $this->accessToken
        );
    }

    /**
     * Read persons that have been marked as not a match for this person
     *
     * @param \Gedcomx\Rs\Client\Options\StateTransitionOption $options
     *
     * @return \Gedcomx\Extensions\FamilySearch\Rs\Client\PersonNonMatchesState
     */
    public function readNonMatches(StateTransitionOption $options = null)
    {
        $link = $this->getLink(Rel::NOT_A_MATCHES);
        if ($link == null || $link->getHref() == null) {
            return null;
        }

        $request = $this->createAuthenticatedRequest('GET', $link->getHref());
        FamilySearchRequest::applyFamilySearchMediaType($request);

        return $this->stateFactory->createState(
            'PersonNonMatchesState',
            $this->client,
            $request,
            $this->passOptionsTo('invoke', array($request), func_get_args()),
            $this->accessToken
        );
    }

    /**
     * Restore this person if it has been deleted
     *
     * @param \Gedcomx\Rs\Client\Options\StateTransitionOption $option,...
     *
     * @return FamilyTreePersonState
     */
    public function restore(StateTransitionOption $option = null)
    {
        $link = $this->getLink(Rel::RESTORE);
        if ($link == null || $link->getHref() == null) {
            return null;
        }

        $request = $this->createAuthenticatedRequest('POST', $link->getHref());
        FamilySearchRequest::applyFamilySearchMediaType($request);

        return $this->stateFactory->createState(
            'PersonState',
            $this->client,
            $request,
            $this->passOptionsTo('invoke', array($request), func_get_args()),
            $this->accessToken
        );
    }

    /**
     * Reads merge options for merging the candidate with the current person
     *
     * @param FamilyTreePersonState                            $candidate
     * @param \Gedcomx\Rs\Client\Options\StateTransitionOption $option,...
     *
     * @return \Gedcomx\Extensions\FamilySearch\Rs\Client\PersonMergeState
     */
    public function readMergeOptions(FamilyTreePersonState $candidate, StateTransitionOption $option = null)
    {
        return $this->passOptionsTo('transitionToPersonMerge', array('OPTIONS', $candidate), func_get_args());
    }

    /**
     * Reads the merge analysis resulting from comparing the current person with the candidate
     *
     * @param FamilyTreePersonState                            $candidate
     * @param \Gedcomx\Rs\Client\Options\StateTransitionOption $option,...
     *
     * @return \Gedcomx\Extensions\FamilySearch\Rs\Client\PersonMergeState
     */
    public function readMergeAnalysis(FamilyTreePersonState $candidate, StateTransitionOption $option = null)
    {
        return $this->passOptionsTo('transitionToPersonMerge', array('GET', $candidate), func_get_args());
    }

    /**
     * Processes the request for merge analysis
     *
     * @param string                                           $method
     * @param FamilyTreePersonState                            $candidate
     * @param \Gedcomx\Rs\Client\Options\StateTransitionOption $option,...
     *
     * @throws \InvalidArgumentException
     * @return \Gedcomx\Extensions\FamilySearch\Rs\Client\PersonMergeState
     */
    protected function transitionToPersonMerge($method, FamilyTreePersonState $candidate, StateTransitionOption $option = null)
    {
        $link = $this->getLink(Rel::MERGE);
        if ($link == null || $link->getTemplate() == null) {
            return null;
        }

        $person = $this->getPerson();
        if ($person == null || $person->getId() == null) {
            throw new \InvalidArgumentException ("Cannot read merge options: no person id available.");
        }
        $personId = $person->getId();

        $person = $candidate->getPerson();
        if ($person == null || $person->getId() == null) {
            throw new \InvalidArgumentException ("Cannot read merge options: no person id provided on the candidate.");
        }
        $candidateId = $person->getId();

        $uri = array(
            $link->getTemplate(),
            array(
                "pid" => $personId,
                "dpid" => $candidateId
            )
        );

        $request = $this->createAuthenticatedRequest($method, $uri);
        FamilySearchRequest::applyFamilySearchMediaType($request);

        return $this->stateFactory->createState(
            'PersonMergeState',
            $this->client,
            $request,
            $this->passOptionsTo('invoke', array($request), func_get_args()),
            $this->accessToken
        );
    }

    /**
     * Flag the Person passed as not a match to the current person using a state object
     *
     * @param FamilyTreePersonState                            $person
     * @param \Gedcomx\Rs\Client\Options\StateTransitionOption $option ,..
     *
     * @return \Gedcomx\Extensions\FamilySearch\Rs\Client\PersonNonMatchesState
     */
    public function addNonMatchState(FamilyTreePersonState $person, StateTransitionOption $option = null)
    {
        return $this->passOptionsTo('addNonMatchPerson', array($person->getPerson()), func_get_args());
    }

    /**
     * Flag the Person passed as not a match to the current person using a Person conclusion object
     *
     * @param \Gedcomx\Conclusion\Person                       $person
     * @param \Gedcomx\Rs\Client\Options\StateTransitionOption $option,...
     *
     * @return \Gedcomx\Extensions\FamilySearch\Rs\Client\PersonNonMatchesState|null
     */
    public function addNonMatchPerson(Person $person, StateTransitionOption $option = null)
    {
        $link = $this->getLink(Rel::NOT_A_MATCHES);
        if ($link == null || $link->getHref() == null) {
            return null;
        }

        $entity = new Gedcomx();
        $entity->addPerson($person);
        $request = $this->createAuthenticatedRequest('POST', $link->getHref());
        FamilySearchRequest::applyFamilySearchMediaType($request);
        /** @var EntityEnclosingRequest $request */
        $json = $entity->toJson();
        $request->setBody($entity->toJson());

        return $this->stateFactory->createState(
            'PersonNonMatchesState',
            $this->client,
            $request,
            $this->passOptionsTo('invoke', array($request), func_get_args()),
            $this->accessToken
        );
    }
}