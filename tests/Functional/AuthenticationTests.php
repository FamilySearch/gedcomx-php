<?php

namespace Gedcomx\Tests\Functional;

use Gedcomx\Gedcomx;
use Gedcomx\Rs\Client\GedcomxApplicationState;
use Gedcomx\Rs\Client\Rel;
use Gedcomx\Rs\Client\StateFactory;
use Gedcomx\Rs\Client\Util\HttpStatus;
use Gedcomx\Tests\ApiTestCase;
use Gedcomx\Tests\SandboxCredentials;
use Guzzle\Http\Message\Request;

class AuthenticationTests extends ApiTestCase
{
    private $clientId = 'ABCD-EFGH-JKLM-NOPQ-RSTU-VWXY-0123-4567';

    /**
     * @link https://familysearch.org/developers/docs/api/authentication/Delete_Access_Token_usecase
     */
    public function testDeleteAccessToken()
    {
        $factory = new StateFactory();
        $collectionState = $factory
            ->newCollectionState()
            ->authenticateViaOAuth2Password(
                SandboxCredentials::USERNAME,
                SandboxCredentials::PASSWORD,
                SandboxCredentials::API_KEY
            );

        $link = $collectionState->getLink(Rel::OAUTH2_TOKEN);
        $request = $collectionState->getClient()->createRequest(Request::DELETE, $link->getHref());
        $request->setHeader('Accept', Gedcomx::JSON_APPLICATION_TYPE);
        $request->setHeader('Authorization', "Bearer {$collectionState->getAccessToken()}");

        $query = $request->getQuery();
        $query->add('access_token', $collectionState->getAccessToken());
        $response = $request->send();

        $this->assertEquals(
            HttpStatus::NO_CONTENT,
            $response->getStatusCode(),
            __METHOD__ . " failed. " . HttpStatus::getText($response->getStatusCode()) . "(".$response->getStatusCode().") returned."
        );
    }

    /**
     * @link https://familysearch.org/developers/docs/api/authentication/Initiate_Authorization_(GET)_usecase
     */
    public function testInitiateAuthorizationGet()
    {
        $factory = new StateFactory();
        $collectionState = $factory->newCollectionState();
        $link = $collectionState->getLink(Rel::OAUTH2_AUTHORIZE);
        $request = $collectionState->getClient()->createRequest(Request::GET, $link->getHref());
        $request->setHeader('Accept', Gedcomx::HTML_TYPE);

        $query = $request->getQuery();
        $query->add('response_type', 'code');
        $query->add('client_id', $this->clientId);
        $query->add('redirect_uri', 'https://familysearch.org/developers/sandbox-oauth2-redirect');

        $response = $collectionState->getClient()->send($request);
        $doc = $response->getBody(true);
        $hasInput = strpos($doc,'id="userName"') !== false ? true : false;
        $this->assertTrue($hasInput);
    }

    /**
     * @link https://familysearch.org/developers/docs/api/authentication/Initiate_Authorization_(Invalid_Parameter)_usecase
     */
    public function testInitiateAuthorizationInvalid()
    {
        $factory = new StateFactory();
        $collectionState = $factory->newCollectionState();
        $link = $collectionState->getLink(Rel::OAUTH2_AUTHORIZE);
        $request = $collectionState->getClient()->createRequest(Request::GET, $link->getHref());
        $request->setHeader('Accept', Gedcomx::HTML_TYPE);

        $query = $request->getQuery();
        $query->add('response_type', 'code');
        $query->add('client_id', $this->clientId);
        $query->add('redirect_uri', 'https://hrpufnstuf.org/witchiepoo');

        $response = $request->send();
        $doc = $response->getBody(true);
        $hasInput = strpos($doc,'Oauth2 error') !== false ? true : false;
        $this->assertTrue($hasInput);
    }

    /**
     * @link https://familysearch.org/developers/docs/api/authentication/Initiate_Authorization_(POST)_usecase
     */
    public function testInitiateAuthorizationPost()
    {
        $factory = new StateFactory();
        $collectionState = $factory->newCollectionState();
        $link = $collectionState->getLink(Rel::OAUTH2_AUTHORIZE);
        /** @var \Guzzle\Http\Message\EntityEnclosingRequest $request */
        $request = $collectionState->getClient()->createRequest(Request::POST, $link->getHref());
        $request->setHeader('Accept', Gedcomx::HTML_TYPE);
        $request->setHeader('Content-Type', Gedcomx::FORM_DATA_TYPE);

        $formData = array(
            'response_type' => 'code',
            'client_id' => $this->clientId,
            'redirect_uri' => 'https://familysearch.org/developers/sandbox-oauth2-redirect'
        );
        $request->setBody(http_build_query($formData));
        $response = $collectionState->getClient()->send($request);
        $doc = $response->getBody(true);
        $hasInput = strpos($doc,'id="userName"') !== false ? true : false;
        $this->assertTrue($hasInput);
    }

    /**
     * @link https://familysearch.org/developers/docs/api/authentication/Obtain_Access_Token_(Bad_Parameters)_usecase
     *
     * @expectedException \Gedcomx\Rs\Client\Exception\GedcomxApplicationException
     */
    public function testBadAuthenticationRequest()
    {
        $factory = new StateFactory();
        $factory->newCollectionState()
            ->authenticateViaOAuth2WithoutCredentials('0.0.0.0', 'ABCD-1234-EFGH-5678-IJKL-9012-MNOP');
    }

    /**
     * testObtainAccessTokenWithAuthorizationCode
     * @link https://familysearch.org/developers/docs/api/authentication/Obtain_Access_Token_with_Authorization_Code_usecase
     *
     * No test case. Auth code is generated in the process of logging in with a third party provider.
     */

    /**
     * @link https://familysearch.org/developers/docs/api/authentication/Obtain_Access_Token_with_Username_and_Password_usecase
     */
    public function testObtainAccessTokenWithUsernameAndPassword()
    {
        $factory = new StateFactory();
        $collectionState = $factory
            ->newCollectionState()
            ->authenticateViaOAuth2Password(
                SandboxCredentials::USERNAME,
                SandboxCredentials::PASSWORD,
                SandboxCredentials::API_KEY
            );

        $this->assertNotEmpty($collectionState->getAccessToken());
    }

    /**
     * @link https://familysearch.org/developers/docs/api/authentication/Obtain_Access_Token_without_Authenticating_usecase
     */
    public function testObtainAccessTokenWithoutAuthenticating()
    {

        $factory = new StateFactory();
        $collectionState = $factory
            ->newCollectionState()
            ->authenticateViaOAuth2WithoutCredentials('0.0.0.0', $this->clientId);

        $this->assertNotEmpty($collectionState->getAccessToken());
    }
}