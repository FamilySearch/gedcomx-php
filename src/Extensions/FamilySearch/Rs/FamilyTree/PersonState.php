<?php

	namespace Gedcomx\Extensions\FamilySearch\Rs\Client\FamilyTree;

    use Gedcomx\Rs\Client\Rel as Rel;
    use Gedcomx\Extensions\FamilySearch\Rs\Client\Rel as ExtRel;
	use Gedcomx\Extensions\FamilySearch\FamilySearchPlatform;
	use Gedcomx\Extensions\FamilySearch\Rs\Client\Helpers\FamilySearchRequest;
	use Gedcomx\Util\HttpStatus;
	use Guzzle\Http\Client;
	use Guzzle\Http\Message\Request;
	use Guzzle\Http\Message\Response;

	class PersonState extends \Gedcomx\Rs\Client\PersonState
	{
		use FamilySearchRequest;

		public function __construct(Client $client, Request $request, Response $response, $accessToken, FamilyTreeStateFactory $stateFactory)
		{
			parent::__construct($client, $request, $response, $accessToken, $stateFactory);
		}

		protected function  reconstruct(Request $request, Response $response)
		{
			return new PersonState($this->client, $request, $response, $this->accessToken, $this->stateFactory);
		}

		protected function loadEntityConditionally(Response $response)
		{
			if ($response . getMethod() == Request::GET
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

		public function getChildAndParentsRelationships()
		{
			return $this->getEntity() == null ? null : $this->getEntity()->getChildAndParentsRelationships();
		}

		public function getChildAndParentsRelationshipsToChildren()
		{
			$relationships = $this->getChildAndParentsRelationships();
			if ($relationships == null) {
				$relationships = array();
			}
			if (! empty($relationships)) {
				foreach ($relationships as $idx => $r) {
					if ($this->refersToMe($r->getChild())) {
						unset($relationships[$idx]);
					}
				}
			}

			return $relationships;
		}

		public function getChildAndParentsRelationshipsToParents()
		{
			$relationships = $this->getChildAndParentsRelationships();
			if ($relationships == null) {
				$relationships = array();
			}
			if (! empty($relationships)) {
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
		 * @return \Gedcomx\Extensions\FamilySearch\Rs\Client\Helpers\Request|Request
		 */
		protected function createRequestForEmbeddedResource($rel)
		{
			$link = $this->getLink($rel);

			if ($rel == Rel::DISCUSSION_REFERENCES) {
				return $this->createAuthenticatedFamilySearchRequest(Request::GET, $link->getHref());
			} else {
				return $this->createAuthenticatedGedcomxRequest(Request::GET, $link->getHref());
			}
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
            $request = $this->createAuthenticatedFamilySearchRequest(Request::POST, $target);

            return $this->stateFactory->createState(
                'PersonState',
                $this->client,
                $request,
                $this->passOptionsTo('invoke', array($request), func_get_args()),
                $this->accessToken
            );
        }

  public FamilyTreePersonState deleteDiscussionReference(DiscussionReference reference, StateTransitionOption... options) {
		Link $link = reference.getLink(Rel.DISCUSSION_REFERENCE);
    $link = $link == null ? reference.getLink(org.gedcomx.rs.Rel.SELF) : $link;
    if ($link == null || $link->getHref() == null) {
		throw new GedcomxApplicationException("Discussion reference cannot be deleted: missing $link.");
	}

    ClientRequest request = RequestUtil.applyFamilySearchConneg(createAuthenticatedGedcomxRequest()).build($link->getHref().toURI(), HttpMethod.DELETE);
    return ((FamilyTreeStateFactory)this.stateFactory).newPersonState(request, invoke(request, options), this.accessToken);
  }


  public ChildAndParentsRelationshipState readChildAndParentsRelationship(ChildAndParentsRelationship relationship, StateTransitionOption... options) {
		Link $link = relationship.getLink(org.gedcomx.rs.Rel.RELATIONSHIP);
    $link = $link == null ? relationship.getLink(org.gedcomx.rs.Rel.SELF) : $link;
    if ($link == null || $link->getHref() == null) {
		return null;
	}

    ClientRequest request = RequestUtil.applyFamilySearchConneg(createAuthenticatedRequest()).build($link->getHref().toURI(), HttpMethod.GET);
    return ((FamilyTreeStateFactory)this.stateFactory).newChildAndParentsRelationshipState(request, invoke(request, options), this.accessToken);
  }

  public ChangeHistoryState readChangeHistory(StateTransitionOption... options) {
		Link $link = getLink(Rel.CHANGE_HISTORY);
    if ($link == null || $link->getHref() == null) {
		return null;
	}

    ClientRequest request = createAuthenticatedFeedRequest().build($link->getHref().toURI(), HttpMethod.GET);
    return ((FamilyTreeStateFactory)this.stateFactory).newChangeHistoryState(request, invoke(request, options), this.accessToken);
  }

  public PersonMatchResultsState readMatches(StateTransitionOption... options) {
		Link $link = getLink(Rel.MATCHES);
    if ($link == null || $link->getHref() == null) {
		return null;
	}

    ClientRequest request = createAuthenticatedFeedRequest().build($link->getHref().toURI(), HttpMethod.GET);
    return ((FamilyTreeStateFactory)this.stateFactory).newPersonMatchResultsState(request, invoke(request, options), this.accessToken);
  }

  public FamilyTreePersonState restore(StateTransitionOption... options) {
		Link $link = getLink(Rel.RESTORE);
    if ($link == null || $link->getHref() == null) {
		return null;
	}

    ClientRequest request = RequestUtil.applyFamilySearchConneg(createAuthenticatedRequest()).build($link->getHref().toURI(), HttpMethod.POST);
    return ((FamilyTreeStateFactory)this.stateFactory).newPersonState(request, invoke(request, options), this.accessToken);
  }

  public PersonMergeState readMergeOptions(FamilyTreePersonState candidate, StateTransitionOption... options) {
		return transitionToPersonMerge(HttpMethod.OPTIONS, candidate, options);
	}

  public PersonMergeState readMergeAnalysis(FamilyTreePersonState candidate, StateTransitionOption... options) {
		return transitionToPersonMerge(HttpMethod.GET, candidate, options);
	}

  protected PersonMergeState transitionToPersonMerge(String method, FamilyTreePersonState candidate, StateTransitionOption... options) {
		Link $link = getLink(Rel.MERGE);
    if ($link == null || $link.getTemplate() == null) {
		return null;
	}

    Person person = getPerson();
    if (person == null || person.getId() == null) {
		throw new IllegalArgumentException("Cannot read merge options: no person id available.");
	}
    String personId = person.getId();

    person = candidate.getPerson();
    if (person == null || person.getId() == null) {
		throw new IllegalArgumentException("Cannot read merge options: no person id provided on the candidate.");
	}
    String candidateId = person.getId();

    String template = $link.getTemplate();

    String uri;
    try {
		uri = UriTemplate.fromTemplate(template).set("pid", personId).set("dpid", candidateId).expand();
	}
	catch (VariableExpansionException e) {
			throw new GedcomxApplicationException(e);
		}
    catch (MalformedUriTemplateException e) {
			throw new GedcomxApplicationException(e);
		}

    ClientRequest request = RequestUtil.applyFamilySearchConneg(createAuthenticatedRequest()).build(URI.create(uri), method);
    return ((FamilyTreeStateFactory)this.stateFactory).newPersonMergeState(request, invoke(request, options), this.accessToken);
  }

  public PersonNonMatchesState addNonMatch(FamilyTreePersonState person, StateTransitionOption... options) {
		return addNonMatch(person.getPerson(), options);
	}

  public PersonNonMatchesState addNonMatch(Person person, StateTransitionOption... options) {
		Link $link = getLink(Rel.NOT_A_MATCHES);
    if ($link == null || $link->getHref() == null) {
		return null;
	}

    Gedcomx entity = new Gedcomx();
    entity.addPerson(person);
    ClientRequest request = RequestUtil.applyFamilySearchConneg(createAuthenticatedRequest()).entity(entity).build($link->getHref().toURI(), HttpMethod.POST);
    return ((FamilyTreeStateFactory)this.stateFactory).newPersonNonMatchesState(request, invoke(request, options), this.accessToken);
  }
	}