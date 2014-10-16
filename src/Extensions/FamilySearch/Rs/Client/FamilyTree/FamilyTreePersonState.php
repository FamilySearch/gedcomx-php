<?php

    namespace Gedcomx\Extensions\FamilySearch\Rs\Client\FamilyTree;

	use Gedcomx\Conclusion\Person;
    use Gedcomx\Extensions\FamilySearch\Rs\Client\DiscussionState;
    use Gedcomx\Extensions\FamilySearch\Rs\Client\PersonMergeState;
    use Gedcomx\Extensions\FamilySearch\Rs\Client\PersonNonMatchesState;
    use Gedcomx\Extensions\FamilySearch\Rs\FamilyTree\ChangeHistoryState;
    use Gedcomx\Extensions\FamilySearch\Rs\FamilyTree\ChildAndParentsRelationshipState;
    use Gedcomx\Extensions\FamilySearch\Tree\ChildAndParentsRelationship;
    use Gedcomx\Extensions\FamilySearch\Tree\DiscussionReference;
    use Gedcomx\Gedcomx;
    use Gedcomx\Rs\Client\Exception\GedcomxApplicationException;
    use Gedcomx\Rs\Client\Options\StateTransitionOption;
    use Gedcomx\Rs\Client\PersonState;
    use Gedcomx\Rs\Client\Rel;
    use Gedcomx\Extensions\FamilySearch\FamilySearchPlatform;
	use Gedcomx\Extensions\FamilySearch\Rs\Client\Helpers\FamilySearchRequest;
	use Gedcomx\Extensions\FamilySearch\Rs\Client\Rel as ExtRel;
	use Gedcomx\Extensions\FamilySearch\Rs\FamilyTree\FamilyTreeStateFactory;
    use Gedcomx\Rs\Client\SourceDescriptionsState;
    use Gedcomx\Util\HttpStatus;
    use Guzzle\Http\Client;
    use Guzzle\Http\Message\Request;
    use Guzzle\Http\Message\Response;

    class FamilyTreePersonState extends PersonState
    {
        public function __construct(Client $client, Request $request, Response $response, $accessToken, FamilyTreeStateFactory $stateFactory)
        {
            parent::__construct($client, $request, $response, $accessToken, $stateFactory);
        }

        protected function  reconstruct(Request $request, Response $response)
        {
            /** @var \Gedcomx\Rs\Client\StateFactory $stateFactory */
            return new FamilyTreePersonState($this->client, $request, $response, $this->accessToken, $this->stateFactory);
        }

        protected function loadEntityConditionally(Response $response)
        {
            if ($response->getInfo('request_header') == Request::GET
                && ($response->getStatusCode() == HttpStatus::OK || $response->getStatusCode() == HttpStatus::GONE)
                || $response->getStatusCode() == HttpStatus::PRECONDITION_FAILED
            ) {
                return $this->loadEntity();
            } else {
                return null;
            }
        }

        protected function loadEntity()
        {
            $json = json_decode($this->response->getBody(), true);

            return new FamilySearchPlatform($json);
        }

        public function getPersons()
        {
            return $this->getEntity() == null ? null : $this->getEntity()->getPersons();
        }

        /**
         * @return ChildAndParentsRelationship[]|null
         */
        public function getChildAndParentsRelationships()
        {
            return $this->getEntity() == null ? null : $this->getEntity()->getChildAndParentsRelationships();
        }

        /**
         * @return ChildAndParentsRelationship[]|null
         */
        public function getChildAndParentsRelationshipsToChildren()
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
         * @return ChildAndParentsRelationship[]|null
         */
        public function getChildAndParentsRelationshipsToParents()
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
         * @param $rel
         *
         * @return Request
         */
        protected function createRequestForEmbeddedResource($rel)
        {
            $link = $this->getLink($rel);
            $request = $this->createAuthenticatedGedcomxRequest(Request::GET, $link->getHref());
            if ($rel == Rel::DISCUSSION_REFERENCES) {
                FamilySearchRequest::applyFamilySearchMediaType($request);
            }

            return $request;
        }

        public function loadDiscussionReferences(StateTransitionOption $option = null)
        {
            return $this->passOptionsTo('loadEmbeddedResources', array(Rel::DISCUSSION_REFERENCES), func_get_args());
        }

        /**
         * @param StateTransitionOption $option,...
         *
         * @return SourceDescriptionsState
         */
        public function readPortraits(StateTransitionOption $option = null)
        {
            $link = $this->getLink(ExtRel::PORTRAITS);
            if ($link == null || $link->getHref() == null) {
                return null;
            }

            $request = $this->createAuthenticatedGedcomxRequest(Request::GET, $link->getHref());

            return $this->stateFactory->createState(
                'SourceDescriptionsState',
                $this->client,
                $request,
                $this->passOptionsTo('invoke', array($request), func_get_args()),
                $this->accessToken
            );
        }

        /**
         * @param StateTransitionOption $option
         *
         * @return Response
         */
        public function readPortrait(StateTransitionOption $option = null)
        {
            $link = $this->getLink(ExtRel::PORTRAIT);
            if ($link == null || $link->getHref() == null) {
                return null;
            }

            $request = $this->createAuthenticatedGedcomxRequest(Request::GET, $link->getHref());

            return $this->passOptionsTo('invoke', array($request), func_get_args());
        }

        /**
         * @param DiscussionState       $discussion
         * @param StateTransitionOption $option
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
         * @param DiscussionReference   $reference
         * @param StateTransitionOption $option
         *
         * @return FamilyTreePersonState
         */
        public function addDiscussionReference(DiscussionReference $reference, StateTransitionOption $option = null)
        {
            return $this->passOptionsTo('addDiscussionReferences', array(array($reference)), func_get_args());
        }

        /**
         * @param DiscussionReference[] $refs
         * @param StateTransitionOption $option
         *
         * @return FamilyTreePersonState
         */
        public function addDiscussionReferences(array $refs, StateTransitionOption $option = null)
        {
            return $this->passOptionsTo('updateDiscussionReference', array($refs), func_get_args());
        }

        /**
         * @param DiscussionReference   $reference
         * @param StateTransitionOption $option
         *
         * @return FamilyTreePersonState
         */
        public function updateDiscussionReference(DiscussionReference $reference, StateTransitionOption $option = null)
        {
            return $this->passOptionsTo('updateDiscussionReferences', array(array($reference)), func_get_args());
        }

        /**
         * @param DiscussionReference[] $refs
         * @param StateTransitionOption $option
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
         * @param Person                $person
         * @param StateTransitionOption $option
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
            $request = $this->createAuthenticatedRequest(Request::POST, $target);
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
         * @param DiscussionReference   $reference
         * @param StateTransitionOption $option
         *
         * @throws GedcomxApplicationException
         * @return FamilyTreePersonState
         */
        public function  deleteDiscussionReference(DiscussionReference $reference, StateTransitionOption $option = null)
        {
            $link = $reference->getLink(Rel::DISCUSSION_REFERENCE);
            if ($link == null) {
                $link = $reference->getLink(Rel::SELF);
            }
            if ($link == null || $link->getHref() == null) {
                throw new GedcomxApplicationException("Discussion reference cannot be deleted: missing link.");
            }

            $request = $this->createAuthenticatedRequest(Request::DELETE, $link->getHref());
            FamilySearchRequest::applyFamilySearchMediaType($request);

            return $this->stateFactory->createState(
                'PersonState',
                $this->client,
                $request,
                $this->passOptionsTo('invoke', array($request), func_get_args()),
                $this->accessToken);
        }

        /**
         * @param ChildAndParentsRelationship $relationship
         * @param StateTransitionOption       $option,...
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

            $request = $this->createAuthenticatedRequest(Request::GET, $link->getHref());
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
         * @param StateTransitionOption $option
         *
         * @return ChangeHistoryState
         */
        public function  readChangeHistory(StateTransitionOption $option = null)
        {
            $link = $this->getLink(ExtRel::CHANGE_HISTORY);
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
         * @return \Gedcomx\Extensions\FamilySearch\Rs\Client\PersonMatchResultsState
         */
        public function readMatches(StateTransitionOption $option = null)
        {
            $link = $this->getLink(Rel::MATCHES);
            if ($link == null || $link->getHref() == null) {
                return null;
            }

            $request = $this->createAuthenticatedFeedRequest(Request::GET, $link->getHref());

            return $this->stateFactory->createState(
                'PersonMatchResultsState',
                $this->client,
                $request,
                $this->passOptionsTo('invoke', array($request), func_get_args()),
                $this->accessToken
            );
        }

        /**
         * @param StateTransitionOption $option,...
         *
         * @return FamilyTreePersonState
         */
        public function restore(StateTransitionOption $option = null)
        {
            $link = $this->getLink(ExtRel::RESTORE);
            if ($link == null || $link->getHref() == null) {
                return null;
            }

            $request = $this->createAuthenticatedRequest(Request::POST, $link->getHref());
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
         * @param FamilyTreePersonState $candidate
         * @param StateTransitionOption $option
         *
         * @return PersonMergeState
         */
        public function readMergeOptions(FamilyTreePersonState $candidate, StateTransitionOption $option = null)
        {
            return $this->passOptionsTo('transitionToPersonMerge', array(Request::OPTIONS, $candidate), func_get_args());
        }

        /**
         * @param FamilyTreePersonState $candidate
         * @param StateTransitionOption $option,...
         *
         * @return PersonMergeState
         */
        public function readMergeAnalysis(FamilyTreePersonState $candidate, StateTransitionOption $option = null)
        {
            return $this->passOptionsTo('transitionToPersonMerge', array(Request::GET, $candidate), func_get_args());
        }

		/**
		 * @param string                $method
		 * @param FamilyTreePersonState $candidate
		 * @param StateTransitionOption $option
		 *
		 * @throws \InvalidArgumentException
		 * @return PersonMergeState
		 */
        protected function transitionToPersonMerge($method, FamilyTreePersonState $candidate, StateTransitionOption $option = null)
        {
            $link = $this->getLink(ExtRel::MERGE);
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
                    "pid"  => $personId,
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
         * @param FamilyTreePersonState $person
         * @param StateTransitionOption $option ,..
         *
         * @return PersonNonMatchesState
         */
        public function addNonMatchState(FamilyTreePersonState $person, StateTransitionOption $option = null)
        {
            return $this->passOptionsTo('addNonMatchPerson', array($person->getPerson()), func_get_args());
        }

        /**
         * @param Person                $person
         * @param StateTransitionOption $option
         *
         * @return PersonNonMatchesState|null
         */
        public function addNonMatchPerson(Person $person, StateTransitionOption $option = null)
        {
            $link = $this->getLink(ExtRel::NOT_A_MATCHES);
            if ($link == null || $link->getHref() == null) {
                return null;
            }

            $entity = new Gedcomx();
            $entity->addPerson($person);
            $request = $this->createAuthenticatedRequest(Request::POST, $link->getHref());
            FamilySearchRequest::applyFamilySearchMediaType($request);

            return $this->stateFactory->createState(
                'PersonNonMatchesState',
                $this->client,
                $request,
                $this->passOptionsTo('invoke', array($request), func_get_args()),
                $this->accessToken
            );
        }
    }