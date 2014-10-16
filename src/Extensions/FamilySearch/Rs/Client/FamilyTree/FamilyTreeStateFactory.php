<?php


    namespace Gedcomx\Extensions\FamilySearch\Rs\FamilyTree;

    use Gedcomx\Extensions\FamilySearch\Rs\Client\FamilySearchStateFactory;
    use Guzzle\Http\Client;
    use Guzzle\Http\Message\Request;
    use Guzzle\Http\Message\Response;

    class FamilyTreeStateFactory extends familysearchstatefactory
    {

        /**
         * @param \Guzzle\Http\Client           $client
         * @param \Guzzle\Http\Message\Request  $request
         * @param \Guzzle\Http\Message\Response $response
         * @param string                        $accessToken The access token for this session
         *
         * @returns FamilySearchFamilyTree
         */
        public function buildFamilyTreeState(Client $client, Request $request, Response $response, $accessToken)
        {
            return $this->newCollectionState($client, $request, $response, $accessToken, $this);
        }

        /**
         * @param \Guzzle\Http\Client           $client
         * @param \Guzzle\Http\Message\Request  $request
         * @param \Guzzle\Http\Message\Response $response
         * @param string                        $accessToken The access token for this session
         *
         * @return \Gedcomx\Extensions\FamilySearch\Rs\Client\DiscussionsState
         */
        protected function buildChildAndParentsRelationshipState(Client $client, Request $request, Response $response, $accessToken)
        {
            return new ChildAndParentsRelationshipState($client, $request, $response, $accessToken, $this);
        }

        /**
         * @param \Guzzle\Http\Client           $client
         * @param \Guzzle\Http\Message\Request  $request
         * @param \Guzzle\Http\Message\Response $response
         * @param string                        $accessToken The access token for this session
         *
         * @return \Gedcomx\Extensions\FamilySearch\Rs\Client\DiscussionsState
         */
        protected function newRelationshipsState(Client $client, Request $request, Response $response, $accessToken)
        {
            return new FamilyTreeRelationshipsState($client, $request, $response, $accessToken, $this);
        }

        /**
         * @param \Guzzle\Http\Client           $client
         * @param \Guzzle\Http\Message\Request  $request
         * @param \Guzzle\Http\Message\Response $response
         * @param string                        $accessToken The access token for this session
         *
         * @return \Gedcomx\Extensions\FamilySearch\Rs\Client\FamilySearchFamilyTree
         */
        protected function newCollectionState(Client $client, Request $request, Response $response, $accessToken)
        {
            return new FamilySearchFamilyTree($client, $request, $response, $accessToken, $this);
        }

        /**
         * @param \Guzzle\Http\Client           $client
         * @param \Guzzle\Http\Message\Request  $request
         * @param \Guzzle\Http\Message\Response $response
         * @param string                        $accessToken The access token for this session
         *
         * @return \Gedcomx\Extensions\FamilySearch\Rs\Client\FamilyTreePersonState
         */
        protected function newPersonState(Client $client, Request $request, Response $response, $accessToken)
        {
            return new FamilyTreePersonState($client, $request, $response, $accessToken, $this);
        }

        /**
         * @param \Guzzle\Http\Client           $client
         * @param \Guzzle\Http\Message\Request  $request
         * @param \Guzzle\Http\Message\Response $response
         * @param string                        $accessToken The access token for this session
         *
         * @return \Gedcomx\Extensions\FamilySearch\Rs\Client\FamilyTreeRelationshipState
         */
        protected function newRelationshipState(Client $client, Request $request, Response $response, $accessToken)
        {
            return new FamilyTreeRelationshipState($client, $request, $response, $accessToken, $this);
        }

        /**
         * @param \Guzzle\Http\Client           $client
         * @param \Guzzle\Http\Message\Request  $request
         * @param \Guzzle\Http\Message\Response $response
         * @param string                        $accessToken The access token for this session
         *
         * @return \Gedcomx\Extensions\FamilySearch\Rs\Client\FamilyTreePersonParentsState
         */
        protected function newPersonParentsState(Client $client, Request $request, Response $response, $accessToken)
        {
            return new FamilyTreePersonParentsState($client, $request, $response, $accessToken, $this);
        }

        /**
         * @param \Guzzle\Http\Client           $client
         * @param \Guzzle\Http\Message\Request  $request
         * @param \Guzzle\Http\Message\Response $response
         * @param string                        $accessToken The access token for this session
         *
         * @return \Gedcomx\Extensions\FamilySearch\Rs\Client\FamilyTreePersonChildrenState
         */
        protected function newPersonChildrenState(Client $client, Request $request, Response $response, $accessToken)
        {
            return new FamilyTreePersonChildrenState($client, $request, $response, $accessToken, $this);
        }

        /**
         * @param \Guzzle\Http\Client           $client
         * @param \Guzzle\Http\Message\Request  $request
         * @param \Guzzle\Http\Message\Response $response
         * @param string                        $accessToken The access token for this session
         *
         * @return \Gedcomx\Extensions\FamilySearch\Rs\Client\ChangeHistoryState
         */
        protected function newChangeHistoryState(Client $client, Request $request, Response $response, $accessToken)
        {
            return new ChangeHistoryState($client, $request, $response, $accessToken, $this);
        }

        /**
         * @param \Guzzle\Http\Client           $client
         * @param \Guzzle\Http\Message\Request  $request
         * @param \Guzzle\Http\Message\Response $response
         * @param string                        $accessToken The access token for this session
         *
         * @return \Gedcomx\Extensions\FamilySearch\Rs\Client\DiscoveryState
         */
        protected function newDiscoveryState(Client $client, Request $request, Response $response, $accessToken)
        {
            return new DiscoveryState($client, $request, $response, $accessToken, $this);
        }

    }