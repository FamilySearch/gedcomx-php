<?php

namespace Gedcomx\Tests\Rs\Client;

use Gedcomx\Rs\Client\Exception\GedcomxApplicationException;
use Gedcomx\Rs\Client\GedcomxApplicationState;
use Gedcomx\Rs\Client\Rel;
use Gedcomx\Rs\Client\StateFactory;
use Gedcomx\Rs\Client\Util\HttpStatus;
use Gedcomx\Tests\ApiTestCase;
use Guzzle\Http\Message\Request;

class GedcomxApplicationStateTest extends ApiTestCase
{
    private $clientId = 'ABCD-EFGH-JKLM-NOPQ-RSTU-VWXY-0123-4567';

    /**
     * @link https://familysearch.org/developers/docs/api/authentication/Initiate_Authorization_(GET)_usecase
     */
    public function testInitiateAuthorizationGet()
    {
        $factory = new StateFactory();
        $collectionState = $factory->newCollectionState();
        $link = $collectionState->getLink(Rel::OAUTH2_AUTHORIZE);
        $request = $collectionState->getClient()->createRequest(Request::GET, $link->getHref());
        $request->setHeader('Accept', GedcomxApplicationState::HTML_TYPE);

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
     * @link https://familysearch.org/developers/docs/api/authentication/Initiate_Authorization_(POST)_usecase
     */
    public function testInitiateAuthorizationPost()
    {
        $factory = new StateFactory();
        $collectionState = $factory->newCollectionState();
        $link = $collectionState->getLink(Rel::OAUTH2_AUTHORIZE);
        $request = $collectionState->getClient()->createRequest(Request::POST, $link->getHref());
        $request->setHeader('Accept', GedcomxApplicationState::HTML_TYPE);
        $request->setHeader('Content-Type', GedcomxApplicationState::FORM_DATA_TYPE);

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
     * @link https://familysearch.org/developers/docs/api/authentication/Initiate_Authorization_(Invalid_Parameter)_usecase
     */
    public function testInitiateAuthorizationInvalid()
    {
        $factory = new StateFactory();
        $collectionState = $factory->newCollectionState();
        $link = $collectionState->getLink(Rel::OAUTH2_AUTHORIZE);
        $request = $collectionState->getClient()->createRequest(Request::GET, $link->getHref());
        $request->setHeader('Accept', GedcomxApplicationState::HTML_TYPE);

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
     * @link https://familysearch.org/developers/docs/api/authentication/Obtain_Access_Token_with_Authorization_Code_usecase
     */
    public function testObtainAccessTokenWithAuthorizationCode()
    {
        //todo: need a valid authcode to test against
        $factory = new StateFactory();
        try{
            $collectionState = $factory
                ->newCollectionState()
                ->authenticateViaOAuth2AuthCode(
                    'tGzv3JOkF0XG5Qx2TlKWIA',
                    'https://familysearch.org/developers/sandbox-oauth2-redirect',
                    $this->apiCredentials->apiKey
                );
        }
        catch(GedcomxApplicationException $e){
            $this->assertTrue(false);
        }

        $this->assertNotEmpty($collectionState->getAccessToken());
    }

    /**
     * @link https://familysearch.org/developers/docs/api/authentication/Obtain_Access_Token_with_Username_and_Password_usecase
     */
    public function testObtainAccessTokenWithUsernameAndPassword()
    {
        $factory = new StateFactory();
        $collectionState = $factory
            ->newCollectionState()
            ->authenticateViaOAuth2Password(
                $this->apiCredentials->username,
                $this->apiCredentials->password,
                $this->apiCredentials->apiKey);

        $this->assertNotEmpty($collectionState->getAccessToken());
    }

    /**
     * @link https://familysearch.org/developers/docs/api/authentication/Obtain_Access_Token_without_Authenticating_usecase
     */
    public function testObtainAccessTokenWithoutAuthenticating()
    {
        $ipHtml = file_get_contents('http://checkip.dyndns.com/');
        preg_match('/\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}/',$ipHtml, $ipAddr);

        $factory = new StateFactory();
        $collectionState = $factory
            ->newCollectionState()
            ->authenticateViaOAuth2WithoutCredentials($ipAddr[0], $this->clientId);

        $this->assertNotEmpty($collectionState->getAccessToken());
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
     * @link https://familysearch.org/developers/docs/api/authentication/Delete_Access_Token_usecase
     */
    public function testDeleteAccessToken()
    {
        $factory = new StateFactory();
        $collectionState = $factory
            ->newCollectionState()
            ->authenticateViaOAuth2Password(
                $this->apiCredentials->username,
                $this->apiCredentials->password,
                $this->apiCredentials->apiKey
            );

        $link = $collectionState->getLink(Rel::OAUTH2_TOKEN);
        $request = $collectionState->getClient()->createRequest(Request::DELETE, $link->getHref());
        $request->setHeader('Accept', GedcomxApplicationState::APPLICATION_JSON_TYPE);
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
}