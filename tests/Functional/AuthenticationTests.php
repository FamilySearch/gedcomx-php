<?php

namespace Gedcomx\Tests\Functional;

use Gedcomx\Gedcomx;
use Gedcomx\Rs\Client\GedcomxApplicationState;
use Gedcomx\Rs\Client\Rel;
use Gedcomx\Rs\Client\StateFactory;
use Gedcomx\Rs\Client\Util\HttpStatus;
use Gedcomx\Tests\ApiTestCase;
use Gedcomx\Tests\SandboxCredentials;
use GuzzleHttp\Psr7\Request;

class AuthenticationTests extends ApiTestCase
{
    
    private $clientId = 'ABCD-EFGH-JKLM-NOPQ-RSTU-VWXY-0123-4567';

    /**
     * @vcr Authentication/testDeleteAccessToken.json
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
        $request = new Request('DELETE', $link->getHref(), [
            'Accept' => Gedcomx::JSON_APPLICATION_TYPE,
            'Authorization' => "Bearer {$collectionState->getAccessToken()}"
        ]);
        $request = $request->withUri($request->getUri()->withQuery('access_token=' . $collectionState->getAccessToken()));
        
        $response = $collectionState->getClient()->send($request);

        $this->assertEquals(
            HttpStatus::NO_CONTENT,
            $response->getStatusCode(),
            __METHOD__ . " failed. " . HttpStatus::getText($response->getStatusCode()) . "(".$response->getStatusCode().") returned."
        );
    }

    /**
     * @vcr Authentication/testInitiateAuthorizationGet.json
     * @link https://familysearch.org/developers/docs/api/authentication/Initiate_Authorization_(GET)_usecase
     */
    public function testInitiateAuthorizationGet()
    {
        $factory = new StateFactory();
        $collectionState = $factory->newCollectionState();
        $link = $collectionState->getLink(Rel::OAUTH2_AUTHORIZE);
        
        $request = new Request('GET', $link->getHref(), [
            'Accept' => Gedcomx::HTML_TYPE
        ]);
        $request = $request->withUri($request->getUri()->withQuery(
            \GuzzleHttp\Psr7\build_query([
                'response_type' => 'code',
                'client_id' => $this->clientId,
                'redirect_uri' => 'https://familysearch.org/developers/sandbox-oauth2-redirect'
            ])
        ));

        $response = $collectionState->getClient()->send($request);
        $doc = $response->getBody();
        $hasInput = strpos($doc,'id="userName"') !== false ? true : false;
        $this->assertTrue($hasInput);
    }

    /**
     * @vcr Authentication/testInitiateAuthorizationInvalid.json
     * @link https://familysearch.org/developers/docs/api/authentication/Initiate_Authorization_(Invalid_Parameter)_usecase
     */
    public function testInitiateAuthorizationInvalid()
    {
        $factory = new StateFactory();
        $collectionState = $factory->newCollectionState();
        $link = $collectionState->getLink(Rel::OAUTH2_AUTHORIZE);
        
        $request = new Request('GET', $link->getHref(), [
            'Accept' => Gedcomx::HTML_TYPE
        ]);
        $request = $request->withUri($request->getUri()->withQuery(
            \GuzzleHttp\Psr7\build_query([
                'response_type' => 'code',
                'client_id' => $this->clientId,
                'redirect_uri' => 'https://hrpufnstuf.org/witchiepoo'
            ])
        ));

        $response = $collectionState->getClient()->send($request);
        $doc = $response->getBody();
        $hasInput = strpos($doc,'Oauth2 error') !== false ? true : false;
        $this->assertTrue($hasInput);
    }

    /**
     * @vcr Authentication/testInitiateAuthorizationPost.json
     * @link https://familysearch.org/developers/docs/api/authentication/Initiate_Authorization_(POST)_usecase
     * 
     * TODO: vcr breaks this test for some reason. The API returns an error that occurs
     * when the Content-Type parameter is missing but you can see in the recorded
     * request that it's not missing. I don't know what to do about it other than
     * turn off vcr for this one because the test works without it. I turned off
     * vcr by breaking the annotation.
     */
    public function testInitiateAuthorizationPost()
    {
        $factory = new StateFactory();
        $collectionState = $factory->newCollectionState();
        $link = $collectionState->getLink(Rel::OAUTH2_AUTHORIZE);
        
        $headers =  [
            'Accept' => Gedcomx::HTML_TYPE,
            'Content-Type' => Gedcomx::FORM_DATA_TYPE
        ];
        $formData = [
            'response_type' => 'code',
            'client_id' => $this->clientId,
            'redirect_uri' => 'https://familysearch.org/developers/sandbox-oauth2-redirect'
        ];
        
        $request = new Request('POST', $link->getHref(), $headers, http_build_query($formData, null, '&'));
        
        $response = $collectionState->getClient()->send($request, ['curl' => ['body_as_string' => true]]);
        $doc = $response->getBody();
        $hasInput = strpos($doc,'id="userName"') !== false ? true : false;
        $this->assertTrue($hasInput);
    }

    /**
     * @vcr Authentication/testBadAuthenticationRequest.json
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
     * @vcr Authentication/testLogout.json
     *
     */
    public function testLogout()
    {
        $factory = new StateFactory();
        $state = $this->collectionState($factory);
        $this->authorize($state);
        $this->assertNotEmpty($state->getAccessToken());
        $state->logout();
        $this->assertEmpty($state->getAccessToken());
    }

    /**
     * testObtainAccessTokenWithAuthorizationCode
     * @link https://familysearch.org/developers/docs/api/authentication/Obtain_Access_Token_with_Authorization_Code_usecase
     *
     * No test case. Auth code is generated in the process of logging in with a third party provider.
     */

    /**
     * @vcr Authentication/testObtainAccessTokenWithUsernameAndPassword.json
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
     * @vcr Authentication/testObtainAccessTokenWithoutAuthenticating.json
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
