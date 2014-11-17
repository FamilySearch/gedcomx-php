<?php

namespace Gedcomx\tests\Rs\Client;

use Gedcomx\Rs\Client\GedcomxApplicationState;
use Gedcomx\Rs\Client\StateFactory;
use Gedcomx\Rs\Client\Util\HttpStatus;
use Gedcomx\Tests\ApiTestCase;
use Guzzle\Http\Message\Request;

class OrdinanceTest extends ApiTestCase
{
    private $ordinanceUri = "https://sandbox.familysearch.org/platform/ordinances/ordinances";
    private $policyUri = "https://sandbox.familysearch.org/platform/ordinances/policy";

    /**
     * @link https://familysearch.org/developers/docs/api/ordinances/Read_Ordinances_usecase
     */
    public function testReadOrdinances()
    {
        $this->collectionState(new StateFactory());
        $request = $this->collectionState()->getClient()->createRequest(Request::GET, $this->ordinanceUri);
        $request->addHeader('Authorization', "Bearer " . $this->collectionState()->getAccessToken());
        $request->setHeader('Accept', GedcomxApplicationState::JSON_MEDIA_TYPE);
        $request->setHeader('Content-Type', GedcomxApplicationState::JSON_MEDIA_TYPE);
        $response = $request->send();
        $this->assertEquals(
            HttpStatus::OK,
            $response->getStatusCode(),
            'Error with valid Ordinance test. Returned: ' . HttpStatus::getText($response->getStatusCode()) . "(".$response->getStatusCode().")"
        );
    }

    /**
     * @link https://familysearch.org/developers/docs/api/ordinances/Read_Ordinances_%28Access_Forbidden%29_usecase
     */
    public function testReadOrdinancesForbidden()
    {
        $factory = new StateFactory();
        $collectionState = $factory
            ->newCollectionState()
            ->authenticateViaOAuth2Password(
                'general_public_user',
                '1234pass',
                $this->apiCredentials->apiKey);
        $request = $this->collectionState()->getClient()->createRequest(Request::GET, $this->ordinanceUri);
        $request->addHeader('Authorization', "Bearer " . $this->collectionState()->getAccessToken());
        $request->setHeader('Accept', GedcomxApplicationState::JSON_MEDIA_TYPE);
        $request->setHeader('Content-Type', GedcomxApplicationState::JSON_MEDIA_TYPE);
        $response = $request->send();
        $this->assertEquals(
            HttpStatus::FORBIDDEN,
            $response->getStatusCode(),
            'Error with invalid Ordinance test. Returned: ' . HttpStatus::getText($response->getStatusCode()) . "(".$response->getStatusCode().")"
        );
    }

    /**
     * @link https://familysearch.org/developers/docs/api/ordinances/Read_Ordinance_Policy_usecase
     */
    public function testReadOrdinancePolicy()
    {
        $this->collectionState(new StateFactory());
        $request = $this->collectionState()->getClient()->createRequest(Request::GET, $this->policyUri);
        $request->addHeader('Authorization', "Bearer " . $this->collectionState()->getAccessToken());
        $request->setHeader('Accept', GedcomxApplicationState::PLAIN_TEXT);
        $response = $request->send();
        $this->assertEquals(
            HttpStatus::OK,
            $response->getStatusCode(),
            'Error retrieving ordinance policy in English. Returned: ' . HttpStatus::getText($response->getStatusCode()) . "(".$response->getStatusCode().")"
        );
        $this->assertContains(
            "Who You Can Do Ordinances For",
            $response->getBody(true),
            "Response doesn't appear to be in English."
        );
    }

    /**
     * @link https://familysearch.org/developers/docs/api/ordinances/Read_Ordinance_Policy_in_French_usecase
     */
    public function testReadOrdinancePolicyInFrench()
    {
        $this->collectionState(new StateFactory());
        $request = $this->collectionState()->getClient()->createRequest(Request::GET, $this->policyUri);
        $request->addHeader('Authorization', "Bearer " . $this->collectionState()->getAccessToken());
        $request->setHeader('Accept', GedcomxApplicationState::PLAIN_TEXT);
        $request->setHeader('Accept-Language', "fr");
        $response = $request->send();
        $this->assertEquals(
            HttpStatus::OK,
            $response->getStatusCode(),
            'Error retrieving ordinance policy in French. Returned: ' . HttpStatus::getText($response->getStatusCode()) . "(".$response->getStatusCode().")"
        );
        $this->assertContains(
            "Personnes pour lesquelles vous pouvez accomplir des ordonnances",
            $response->getBody(true),
            "Response doesn't appear to be in French."
        );
    }
}