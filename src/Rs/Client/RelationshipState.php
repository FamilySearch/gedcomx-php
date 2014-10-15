<?php


	namespace Gedcomx\Rs\Client;

	use Gedcomx\Common\EvidenceReference;
	use Gedcomx\Common\Note;
	use Gedcomx\Conclusion\Fact;
	use Gedcomx\Conclusion\Relationship;
	use Gedcomx\Gedcomx;
	use Gedcomx\Rs\Client\Options\StateTransitionOption;
	use Gedcomx\Source\SourceReference;
	use Guzzle\Http\Client;
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
		 * @param Fact $fact
		 *
		 * @return \Gedcomx\Rs\Client\PersonState
		 */
		public function addFact($fact)
		{
			throw new RuntimeException("function currently not implemented."); //todo: implement
		}

		/**
		 * @param Fact[] $facts
		 *
		 * @return \Gedcomx\Rs\Client\PersonState
		 */
		public function addFacts($facts)
		{
			throw new RuntimeException("function currently not implemented."); //todo: implement
		}

		/**
		 * @param Fact $fact
		 *
		 * @return \Gedcomx\Rs\Client\PersonState
		 */
		public function updateFact($fact)
		{
			throw new RuntimeException("function currently not implemented."); //todo: implement
		}

		/**
		 * @param Fact[] $facts
		 *
		 * @return \Gedcomx\Rs\Client\PersonState
		 */
		public function updateFacts($facts)
		{
			throw new RuntimeException("function currently not implemented."); //todo: implement
		}

		/**
		 * @param Fact $fact
		 *
		 * @return \Gedcomx\Rs\Client\PersonState
		 */
		public function deleteFact($fact)
		{
			throw new RuntimeException("function currently not implemented."); //todo: implement
		}

		/**
		 * @param SourceReference $sourceReference
		 *
		 * @return \Gedcomx\Rs\Client\PersonState
		 */
		public function addSourceReference($sourceReference)
		{
			throw new RuntimeException("function currently not implemented."); //todo: implement
		}

		/**
		 * @param SourceReference[] $sourceReferences
		 *
		 * @return \Gedcomx\Rs\Client\PersonState
		 */
		public function addSourceReferences($sourceReferences)
		{
			throw new RuntimeException("function currently not implemented."); //todo: implement
		}

		/**
		 * @param SourceReference $sourceReference
		 *
		 * @return \Gedcomx\Rs\Client\PersonState
		 */
		public function updateSourceReference($sourceReference)
		{
			throw new RuntimeException("function currently not implemented."); //todo: implement
		}

		/**
		 * @param SourceReference[] $sourceReferences
		 *
		 * @return \Gedcomx\Rs\Client\PersonState
		 */
		public function updateSourceReferences($sourceReferences)
		{
			throw new RuntimeException("function currently not implemented."); //todo: implement
		}

		/**
		 * @param SourceReference $sourceReference
		 *
		 * @return \Gedcomx\Rs\Client\PersonState
		 */
		public function deleteSourceReference($sourceReference)
		{
			throw new RuntimeException("function currently not implemented."); //todo: implement
		}

		/**
		 * @param SourceReference $mediaReference
		 *
		 * @return \Gedcomx\Rs\Client\PersonState
		 */
		public function addMediaReference($mediaReference)
		{
			throw new RuntimeException("function currently not implemented."); //todo: implement
		}

		/**
		 * @param SourceReference[] $mediaReferences
		 *
		 * @return \Gedcomx\Rs\Client\PersonState
		 */
		public function addMediaReferences($mediaReferences)
		{
			throw new RuntimeException("function currently not implemented."); //todo: implement
		}

		/**
		 * @param SourceReference $mediaReference
		 *
		 * @return \Gedcomx\Rs\Client\PersonState
		 */
		public function updateMediaReference($mediaReference)
		{
			throw new RuntimeException("function currently not implemented."); //todo: implement
		}

		/**
		 * @param SourceReference[] $mediaReferences
		 *
		 * @return \Gedcomx\Rs\Client\PersonState
		 */
		public function updateMediaReferences($mediaReferences)
		{
			throw new RuntimeException("function currently not implemented."); //todo: implement
		}

		/**
		 * @param SourceReference $mediaReference
		 *
		 * @return \Gedcomx\Rs\Client\PersonState
		 */
		public function deleteMediaReference($mediaReference)
		{
			throw new RuntimeException("function currently not implemented."); //todo: implement
		}

		/**
		 * @param EvidenceReference $evidenceReference
		 *
		 * @return \Gedcomx\Rs\Client\PersonState
		 */
		public function addEvidenceReference($evidenceReference)
		{
			throw new RuntimeException("function currently not implemented."); //todo: implement
		}

		/**
		 * @param EvidenceReference[] $evidenceReferences
		 *
		 * @return \Gedcomx\Rs\Client\PersonState
		 */
		public function addEvidenceReferences($evidenceReferences)
		{
			throw new RuntimeException("function currently not implemented."); //todo: implement
		}

		/**
		 * @param EvidenceReference $evidenceReference
		 *
		 * @return \Gedcomx\Rs\Client\PersonState
		 */
		public function updateEvidenceReference($evidenceReference)
		{
			throw new RuntimeException("function currently not implemented."); //todo: implement
		}

		/**
		 * @param EvidenceReference[] $evidenceReferences
		 *
		 * @return \Gedcomx\Rs\Client\PersonState
		 */
		public function updateEvidenceReferences($evidenceReferences)
		{
			throw new RuntimeException("function currently not implemented."); //todo: implement
		}

		/**
		 * @param EvidenceReference $evidenceReference
		 *
		 * @return \Gedcomx\Rs\Client\PersonState
		 */
		public function deleteEvidenceReference($evidenceReference)
		{
			throw new RuntimeException("function currently not implemented."); //todo: implement
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
         * @param \Gedcomx\Common\Note                             $note
         * @param \Gedcomx\Rs\Client\Options\StateTransitionOption $option,...
		 *
		 * @return \Gedcomx\Rs\Client\PersonState
		 */
		public function addNote(Note $note, StateTransitionOption $option = null)
		{
			return $this->passOptionsTo('addNotes', array(array($note)), func_get_args());
		}

        /**
         * @param \Gedcomx\Common\Note[]                           $notes
         * @param \Gedcomx\Rs\Client\Options\StateTransitionOption $option,...
         *
         * @return \Gedcomx\Rs\Client\PersonState
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
         * @return \Gedcomx\Rs\Client\PersonState
         */
		public function updateNote(Note $note, StateTransitionOption $option = null)
		{
			return $this->passOptionsTo('updateNotes', array(array($note)), func_get_args());
		}

		/**
         * @param \Gedcomx\Common\Note[]                           $notes
         * @param \Gedcomx\Rs\Client\Options\StateTransitionOption $option,...
		 *
		 * @return \Gedcomx\Rs\Client\PersonState
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
         * @return \Gedcomx\Rs\Client\PersonState
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
			$request = $this->createAuthenticatedGedcomxRequest(Request::POST, $target->getHref());
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
		 * @return \Gedcomx\Rs\Client\PersonState
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

        /*
         * @return \Gedcomx\Conclusion\Relationship
         */
        protected function createEmptySelf() {
            $relationship = new Relationship();
            $relationship->setId($this->getLocalSelfId());
            return $relationship;
        }

        /**
         * @return null|string
         */
        protected function getLocalSelfId() {
            $me = $this->getRelationship();
            return $me == null ? null : $me->getId();
        }


    }