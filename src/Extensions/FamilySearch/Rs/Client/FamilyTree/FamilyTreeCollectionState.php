<?php

    namespace Gedcomx\Extensions\FamilySearch\Rs\Client\FamilyTree;

    use Gedcomx\Common\ResourceReference;
    use Gedcomx\Conclusion\Relationship;
    use Gedcomx\Extensions\FamilySearch\FamilySearchPlatform;
    use Gedcomx\Extensions\FamilySearch\Rs\Client\FamilySearchCollectionState;
    use Gedcomx\Extensions\FamilySearch\Rs\Client\Rel;
    use Gedcomx\Extensions\FamilySearch\Rs\Client\Helpers\FamilySearchRequest;
    use Gedcomx\Extensions\FamilySearch\Platform\Tree\ChildAndParentsRelationship;
    use Gedcomx\Rs\Client\Exception\GedcomxApplicationException;
    use Gedcomx\Rs\Client\Options\StateTransitionOption;
    use Gedcomx\Rs\Client\PersonState;
    use Gedcomx\Rs\Client\RelationshipsState;
    use Gedcomx\Rs\Client\RelationshipState;
    use Gedcomx\Types\RelationshipType;
    use Gedcomx\Rs\Client\Util\HttpStatus;
    use GuzzleHttp\Client;
    use GuzzleHttp\Psr7\Request;
    use GuzzleHttp\Psr7\Response;

    class FamilyTreeCollectionState extends FamilySearchCollectionState
    {
        /**
         * Create a new FamilyTreeCollectionState object
         *
         * @param \GuzzleHttp\Client                                                          $client
         * @param \GuzzleHttp\Psr7\Request                                                 $request
         * @param \GuzzleHttp\Psr7\Response                                                $response
         * @param string                                                                       $accessToken
         * @param \Gedcomx\Extensions\FamilySearch\Rs\Client\FamilyTree\FamilyTreeStateFactory $stateFactory
         */
        function __construct(Client $client, Request $request, Response $response, $accessToken, FamilyTreeStateFactory $stateFactory)
        {
            parent::__construct($client, $request, $response, $accessToken, $stateFactory);
        }

        /**
         * Clone the current FamilyTreeCollectionState object
         *
         * @param \GuzzleHttp\Psr7\Request  $request
         * @param \GuzzleHttp\Psr7\Response $response
         *
         * @return \Gedcomx\Extensions\FamilySearch\Rs\Client\FamilyTree\FamilyTreeCollectionState
         */
        protected function reconstruct(Request $request, Response $response)
        {
            return new FamilyTreeCollectionState($this->client, $request, $response, $this->accessToken, $this->stateFactory);
        }

        /**
         * Begin and unauthenticated session
         *
         * @param string $clientId
         * @param string $ipAddress
         *
         * @return FamilyTreeCollectionState
         */
        public function authenticateViaUnauthenticatedAccess($clientId, $ipAddress)
        {
            $formData = array(
                "grant_type" => "unauthenticated_session",
                "client_id"  => $clientId,
                "ip_address" => $ipAddress
            );

            return $this->authenticateViaOAuth2($formData);
        }

        /**
         * Add a new relationship to the collection
         *
         * @param \Gedcomx\Conclusion\Relationship                 $relationship
         * @param \Gedcomx\Rs\Client\Options\StateTransitionOption $option,...
         *
         * @return \Gedcomx\Rs\Client\RelationshipState|null
         * @throws GedcomxApplicationException
         */
        public function addRelationship(Relationship $relationship, StateTransitionOption $option = null)
        {
            if ($relationship->getKnownType() == RelationshipType::PARENTCHILD) {
                throw new GedcomxApplicationException("FamilySearch Family Tree doesn't support adding parent-child relationships. You must instead add a child-and-parents relationship.");
            }

            return parent::addRelationship($relationship);
        }

        /**
         * Add a list of relationships to the collection
         *
         * @param \Gedcomx\Conclusion\Relationship[]               $relationships
         * @param \Gedcomx\Rs\Client\Options\StateTransitionOption $option,...
         *
         * @return RelationshipState
         * @throws GedcomxApplicationException
         */
        public function addRelationships(array $relationships, StateTransitionOption $option = null)
        {
            foreach ($relationships as $r) {
                if ($r->getKnownType() == RelationshipType::PARENTCHILD) {
                    throw new GedcomxApplicationException("FamilySearch Family Tree doesn't support adding parent-child relationships. You must instead add a child-and-parents relationship.");
                }
            }

            return $this->passOptionsTo('addRelationships', array($relationships), func_get_args(), 'parent');
            //return parent::addRelationships($relationships);
        }

        /**
         * Add a relationship by defining the child and parents
         *
         * @param \Gedcomx\Rs\Client\PersonState                   $child
         * @param \Gedcomx\Rs\Client\PersonState                   $father
         * @param \Gedcomx\Rs\Client\PersonState                   $mother
         * @param \Gedcomx\Rs\Client\Options\StateTransitionOption $option,...
         *
         * @return ChildAndParentsRelationshipState
         */
        public function addChildAndParents(PersonState $child, PersonState $father = null, PersonState $mother = null, StateTransitionOption $option = null)
        {
            $rel = new ChildAndParentsRelationship();
            $rel->setChild($child->getResourceReference());
            if ($father != null) {
                $rel->setFather($father->getResourceReference());
            }
            if ($mother != null) {
                $rel->setMother($mother->getResourceReference());
            }

            return $this->passOptionsTo('addChildAndParentsRelationship', array($rel), func_get_args());
        }

        /**
         * Add a ChildAndParentsRelationship to the collection
         *
         * @param \Gedcomx\Extensions\FamilySearch\Platform\Tree\ChildAndParentsRelationship $rel
         * @param \Gedcomx\Rs\Client\Options\StateTransitionOption                           $option,...
         *
         * @return ChildAndParentsRelationshipState
         * @throws GedcomxApplicationException
         */
        public function addChildAndParentsRelationship(ChildAndParentsRelationship $rel, StateTransitionOption $option = null)
        {
            $link = $this->getLink(Rel::RELATIONSHIPS);
            if ($link == null || $link->getHref() == null) {
                throw new GedcomxApplicationException(sprintf("FamilySearch Family Tree at %s didn't provide a 'relationships' $link->", $this->getUri()));
            }

            $entity = new FamilySearchPlatform();
            $entity->setChildAndParentsRelationships(array($rel));
            $request = $this->createAuthenticatedRequest('POST', $link->getHref(), FamilySearchRequest::getMediaTypes(), null, $entity->toJson());

            return $this->stateFactory->createState(
                'ChildAndParentsRelationshipState',
                $this->client,
                $request,
                $this->passOptionsTo('invoke', array($request), func_get_args()),
                $this->accessToken
            );
        }

        /**
         * Add a list of ChildAndParentsRelationships to the collection
         *
         * @param \Gedcomx\Extensions\FamilySearch\Platform\Tree\ChildAndParentsRelationship[] $chaps
         * @param \Gedcomx\Rs\Client\Options\StateTransitionOption                             $option,...
         *
         * @return \Gedcomx\Rs\Client\RelationshipsState
         * @throws GedcomxApplicationException
         */
        public function addChildAndParentsRelationships(array $chaps, StateTransitionOption $option = null)
        {
            $link = $this->getLink(Rel::RELATIONSHIPS);
            if ($link == null || $link->getHref() == null) {
                throw new GedcomxApplicationException(sprintf("FamilySearch Family Tree at %s didn't provide a 'relationships' $link->", $this->getUri()));
            }

            $entity = new FamilySearchPlatform();
            $entity->setChildAndParentsRelationships($chaps);
            $request = $this->createAuthenticatedRequest('POST', $link->getHref(), FamilySearchRequest::getMediaTypes(), null, $entity->toJson());

            return $this->stateFactory->createState(
                'RelationshipsState',
                $this->client,
                $request,
                $this->passOptionsTo('invoke', array($request), func_get_args()), $this->accessToken);
        }

        /**
         * Read the discovery document for this collection
         *
         * @param \Gedcomx\Rs\Client\Options\StateTransitionOption $option,...
         *
         * @return FamilyTreeCollectionState
         */
        public function readDiscoveryDocument(StateTransitionOption $option = null)
        {
            $request = $this->createAuthenticatedFeedRequest('GET', $this->getSelfUri());

            return $this->reconstruct($request,$this->passOptionsTo('invoke', array($request), func_get_args()));
        }

        /**
         * Read a person with a given id
         *
         * @param string                                           $id
         * @param \Gedcomx\Rs\Client\Options\StateTransitionOption $option,...
         *
         * @return FamilyTreePersonState|null
         */
        public function readPersonById($id, StateTransitionOption $option = null)
        {
            $link = $this->getLink(Rel::PERSON);
            if ($link == null || $link->getTemplate() == null) {
                return null;
            }

            $uri = array(
                $link->getTemplate(),
                array(
                    "pid" => $id
                )
            );

            $request = $this->createAuthenticatedRequest('GET', $uri, FamilySearchRequest::getMediaTypes());

            return $this->stateFactory->createState(
                'PersonState',
                $this->client,
                $request,
                $this->passOptionsTo('invoke', array($request), func_get_args()),
                $this->accessToken
            );
        }

        /**
         * Read a person and their relationships by the person's id
         *
         * @param string                                           $id
         * @param \Gedcomx\Rs\Client\Options\StateTransitionOption $option,...
         *
         * @return FamilyTreePersonState
         * @throws GedcomxApplicationException
         */
        public function readPersonWithRelationshipsById($id, StateTransitionOption $option = null)
        {
            $link = $this->getLink(Rel::PERSON_WITH_RELATIONSHIPS);
            if ($link == null || $link->getTemplate() == null) {
                return null;
            }

            $uri = array(
                $link->getTemplate(),
                array(
                    "person" => $id
                )
            );
            $request = $this->createAuthenticatedRequest('GET', $uri, FamilySearchRequest::getMediaTypes());

            return $this->stateFactory->createState(
                'PersonState',
                $this->client,
                $request,
                $this->passOptionsTo('invoke', array($request), func_get_args()),
                $this->accessToken
            );
        }

        /**
         * Read the preferred spouse relationship on a person
         *
         * @param string                                           $treeUserId
         * @param string                                           $personId
         * @param \Gedcomx\Rs\Client\Options\StateTransitionOption $option,...
         *
         * @return \Gedcomx\Extensions\FamilySearch\Rs\Client\FamilyTree\PreferredRelationshipState
         */
        public function readPreferredSpouseRelationship($treeUserId, $personId, StateTransitionOption $option = null)
        {
            return $this->passOptionsTo(
                'readPreferredRelationship',
                array(Rel::PREFERRED_SPOUSE_RELATIONSHIP, $treeUserId, $personId),
                func_get_args()
            );
        }

        /**
         * Read the preferred parent relationship for a person
         *
         * @param string                                           $treeUserId
         * @param string                                           $personId
         * @param \Gedcomx\Rs\Client\Options\StateTransitionOption $option,...
         *
         * @return \Gedcomx\Extensions\FamilySearch\Rs\Client\FamilyTree\PreferredRelationshipState
         */
        public function readPreferredParentRelationship($treeUserId, $personId, StateTransitionOption $option = null)
        {
            return $this->passOptionsTo(
                'readPreferredRelationship',
                array(Rel::PREFERRED_PARENT_RELATIONSHIP, $treeUserId, $personId),
                func_get_args()
            );
        }

        /**
         * Read a preferred relationship for a user
         *
         * @param string                                           $rel
         * @param string                                           $treeUserId
         * @param string                                           $personId
         * @param \Gedcomx\Rs\Client\Options\StateTransitionOption $option,...
         *
         * @return \Gedcomx\Extensions\FamilySearch\Rs\Client\FamilyTree\PreferredRelationshipState
         * @throws GedcomxApplicationException
         */
        protected function readPreferredRelationship($rel, $treeUserId, $personId, StateTransitionOption $option = null)
        {
            $link = $this->getLink($rel);
            if ($link == null || $link->getTemplate() == null) {
                return null;
            }

            $uri = array(
                $link->getTemplate(),
                array(
                    "pid" => $personId,
                    "uid" => $treeUserId
                )
            );
            $request = $this->createAuthenticatedRequest('GET', $uri, FamilySearchRequest::getMediaTypes());
            $response = $this->passOptionsTo('invoke', array($request), func_get_args());
            if ($response->getStatusCode() == HttpStatus::NO_CONTENT) {
                return null;
            }

            $fsp = new FamilySearchPlatform(json_decode($response->getBody()));
            if ($fsp->getChildAndParentsRelationships() != null && count($fsp->getChildAndParentsRelationships()) > 0) {
                return $this->stateFactory->createState(
                    'ChildAndParentsRelationshipState',
                    $this->client,
                    $request,
                    $response,
                    $this->accessToken
                );
            } else {
                return $this->stateFactory->createState(
                    'RelationshipState',
                    $this->client,
                    $request,
                    $response,
                    $this->accessToken
                );
            }
        }

        /**
         * Update the preferred spouse relationship for a person
         *
         * @param string                                                                           $treeUserId
         * @param string                                                                           $personId
         * @param \Gedcomx\Extensions\FamilySearch\Rs\Client\FamilyTree\PreferredRelationshipState $relationshipState
         * @param \Gedcomx\Rs\Client\Options\StateTransitionOption                                 $option,...
         *
         * @return FamilyTreePersonState
         */
        public function updatePreferredSpouseRelationship($treeUserId, $personId, PreferredRelationshipState $relationshipState, StateTransitionOption $option = null)
        {
            return $this->passOptionsTo(
                'updatePreferredRelationship',
                array(Rel::PREFERRED_SPOUSE_RELATIONSHIP, $treeUserId, $personId, $relationshipState),
                func_get_args()
            );
        }

        /**
         * Update the preferred parent relationship for a person
         *
         * @param string                                                                           $treeUserId
         * @param string                                                                           $personId
         * @param \Gedcomx\Extensions\FamilySearch\Rs\Client\FamilyTree\PreferredRelationshipState $relationshipState
         * @param \Gedcomx\Rs\Client\Options\StateTransitionOption                                 $option,...
         *
         * @return FamilyTreePersonState
         */
        public function updatePreferredParentRelationship($treeUserId, $personId, PreferredRelationshipState $relationshipState, StateTransitionOption $option = null)
        {
            return $this->passOptionsTo(
                'updatePreferredRelationship',
                array(Rel::PREFERRED_PARENT_RELATIONSHIP, $treeUserId, $personId, $relationshipState),
                func_get_args()
            );
        }

        /**
         * Update a preferred relationship for a person
         *
         * @param string                                                                           $rel
         * @param string                                                                           $treeUserId
         * @param string                                                                           $personId
         * @param \Gedcomx\Extensions\FamilySearch\Rs\Client\FamilyTree\PreferredRelationshipState $relationshipState
         * @param \Gedcomx\Rs\Client\Options\StateTransitionOption                                 $option,...
         *
         * @return FamilyTreePersonState
         * @throws \Gedcomx\Rs\Client\Exception\GedcomxApplicationException
         */
        protected function updatePreferredRelationship($rel, $treeUserId, $personId, PreferredRelationshipState $relationshipState, StateTransitionOption $option = null)
        {
            $link = $this->getLink($rel);
            if ($link == null || $link->getTemplate() == null) {
                return null;
            }

            $uri = array(
                $link->getTemplate(),
                array(
                    "pid" => $personId,
                    "uid" => $treeUserId)
            );

            $headers = FamilySearchRequest::getMediaTypes();
            $headers['Location'] = $relationshipState->getSelfUri();
            $request = $this->createAuthenticatedRequest('PUT', $uri, $headers);
            
            return $this->stateFactory->createState(
                'PersonState',
                $this->client,
                $request,
                $this->passOptionsTo('invoke', array($request), func_get_args()),
                $this->accessToken
            );
        }

        /**
         * Remove the preferred spouse relationship from a person
         *
         * @param string                                           $treeUserId
         * @param string                                           $personId
         * @param \Gedcomx\Rs\Client\Options\StateTransitionOption $option,...
         *
         * @return FamilyTreePersonState
         */
        public function deletePreferredSpouseRelationship($treeUserId, $personId, StateTransitionOption $option = null)
        {
            return $this->passOptionsTo(
                'deletePreferredRelationship',
                array($treeUserId, $personId, Rel::PREFERRED_SPOUSE_RELATIONSHIP),
                func_get_args()
            );
        }

        /**
         * Remove the preferred parent relationship for a person
         *
         * @param string                                           $treeUserId
         * @param string                                           $personId
         * @param \Gedcomx\Rs\Client\Options\StateTransitionOption $option,...
         *
         * @return FamilyTreePersonState
         */
        public function deletePreferredParentRelationship($treeUserId, $personId, StateTransitionOption $option = null)
        {
            return $this->passOptionsTo(
                'deletePreferredRelationship',
                array($treeUserId, $personId, Rel::PREFERRED_PARENT_RELATIONSHIP),
                func_get_args()
            );
        }

        /**
         * Remove a preferred relationship from a person
         *
         * @param                                                  $treeUserId
         * @param                                                  $personId
         * @param                                                  $rel
         * @param \Gedcomx\Rs\Client\Options\StateTransitionOption $option,...
         *
         * @return FamilyTreePersonState
         * @throws \Gedcomx\Rs\Client\Exception\GedcomxApplicationException
         */
        protected function deletePreferredRelationship($treeUserId, $personId, $rel, StateTransitionOption $option = null)
        {
            $link = $this->getLink($rel);
            if ($link == null || $link->getTemplate() == null) {
                return null;
            }

            $uri = array(
                $link->getTemplate(),
                array(
                    "pid" => $personId,
                    "uid" => $treeUserId
                )
            );

            $request = $this->createAuthenticatedRequest('DELETE', $uri, FamilySearchRequest::getMediaTypes());

            return $this->stateFactory->createState(
                'PersonState',
                $this->client,
                $request,
                $this->passOptionsTo('invoke', array($request), func_get_args()),
                $this->accessToken
            );
        }

    }