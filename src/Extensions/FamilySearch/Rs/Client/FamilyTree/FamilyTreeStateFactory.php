<?php


    namespace Gedcomx\Extensions\FamilySearch\Rs\Client\FamilyTree;

    use Gedcomx\Extensions\FamilySearch\FamilySearchPlatform;
    use Gedcomx\Extensions\FamilySearch\Rs\Client\FamilySearchStateFactory;
    use Guzzle\Http\Client;
    use Guzzle\Http\Message\Request;
    use Guzzle\Http\Message\Response;

    class FamilyTreeStateFactory extends FamilySearchStateFactory
    {
        /**
         * @param null                $uri
         * @param string              $method The method.
         * @param \Guzzle\Http\Client $client The client to use.
         *
         * @return FamilyTreeCollectionState The collection state.
         */
        public function newCollectionState($uri = null, $method = "GET", Client $client = null)
        {
            if (!$client) {
                $client = $this->defaultClient();
            }

            if ($uri == null) {
                $uri = $this->production ? self::PRODUCTION_URI : self::SANDBOX_URI;
            }

            /** @var Request $request */
            $request = $client->createRequest($method, $uri);
            $request->setHeader("Accept", FamilySearchPlatform::JSON_MEDIA_TYPE);
            return new FamilyTreeCollectionState($client, $request, $client->send($request), null, $this);
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
        protected function buildRelationshipsState(Client $client, Request $request, Response $response, $accessToken)
        {
            return new FamilyTreeRelationshipsState($client, $request, $response, $accessToken, $this);
        }

        /**
         * @param \Guzzle\Http\Client           $client
         * @param \Guzzle\Http\Message\Request  $request
         * @param \Guzzle\Http\Message\Response $response
         * @param string                        $accessToken The access token for this session
         *
         * @return \Gedcomx\Extensions\FamilySearch\Rs\Client\FamilyTree\FamilyTreePersonState
         */
        protected function buildPersonState(Client $client, Request $request, Response $response, $accessToken)
        {
            return new FamilyTreePersonState($client, $request, $response, $accessToken, $this);
        }

        /**
         * @param \Guzzle\Http\Client           $client
         * @param \Guzzle\Http\Message\Request  $request
         * @param \Guzzle\Http\Message\Response $response
         * @param string                        $accessToken The access token for this session
         *
         * @return \Gedcomx\Extensions\FamilySearch\Rs\Client\FamilyTree\FamilyTreeRelationshipState
         */
        protected function buildRelationshipState(Client $client, Request $request, Response $response, $accessToken)
        {
            return new FamilyTreeRelationshipState($client, $request, $response, $accessToken, $this);
        }

        /**
         * @param \Guzzle\Http\Client           $client
         * @param \Guzzle\Http\Message\Request  $request
         * @param \Guzzle\Http\Message\Response $response
         * @param string                        $accessToken The access token for this session
         *
         * @return \Gedcomx\Extensions\FamilySearch\Rs\Client\FamilyTree\FamilyTreePersonParentsState
         */
        protected function buildPersonParentsState(Client $client, Request $request, Response $response, $accessToken)
        {
            return new FamilyTreePersonParentsState($client, $request, $response, $accessToken, $this);
        }

        /**
         * @param \Guzzle\Http\Client           $client
         * @param \Guzzle\Http\Message\Request  $request
         * @param \Guzzle\Http\Message\Response $response
         * @param string                        $accessToken The access token for this session
         *
         * @return \Gedcomx\Extensions\FamilySearch\Rs\Client\FamilyTree\FamilyTreePersonChildrenState
         */
        protected function buildPersonChildrenState(Client $client, Request $request, Response $response, $accessToken)
        {
            return new FamilyTreePersonChildrenState($client, $request, $response, $accessToken, $this);
        }

        /**
         * @param \Guzzle\Http\Client           $client
         * @param \Guzzle\Http\Message\Request  $request
         * @param \Guzzle\Http\Message\Response $response
         * @param string                        $accessToken The access token for this session
         *
         * @return \Gedcomx\Extensions\FamilySearch\Rs\Client\FamilyTree\ChangeHistoryState
         */
        protected function buildChangeHistoryState(Client $client, Request $request, Response $response, $accessToken)
        {
            return new ChangeHistoryState($client, $request, $response, $accessToken, $this);
        }
    }