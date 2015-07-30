<?php 

namespace Gedcomx\Tests\Functional;

use Gedcomx\Gedcomx;
use Gedcomx\Rs\Client\GedcomxApplicationState;
use Gedcomx\Rs\Client\StateFactory;
use Gedcomx\Rs\Client\Util\HttpStatus;
use Gedcomx\Tests\SandboxCredentials;
use Gedcomx\Tests\ApiTestCase;

class OrdinanceTests extends ApiTestCase
{
    private $ordinanceUri = "https://sandbox.familysearch.org/platform/ordinances/ordinances";
    private $policyUri = "https://sandbox.familysearch.org/platform/ordinances/policy";

    /**
     * @vcr OrdinanceTests/testReadOrdinancePolicy.json
     * @link https://familysearch.org/developers/docs/api/ordinances/Read_Ordinance_Policy_usecase
     */
    public function testReadOrdinancePolicy()
    {
        $this->collectionState(new StateFactory());
        $response = $this->collectionState()->getClient()->get($this->policyUri, [
            'headers' => [
                'Authorization' => "Bearer " . $this->collectionState()->getAccessToken(),
                'Accept' => Gedcomx::PLAIN_TEXT
            ]
        ]);
        $this->assertEquals(
            HttpStatus::OK,
            $response->getStatusCode(),
            'Error retrieving ordinance policy in English. Returned: ' . HttpStatus::getText($response->getStatusCode()) . "(".$response->getStatusCode().")"
        );
        $this->assertContains(
            "Who You Can Do Ordinances For",
            (string) $response->getBody(),
            "Response doesn't appear to be in English."
        );
    }

    /**
     * @vcr OrdinanceTests/testReadOrdinancePolicyInFrench.json
     * @link https://familysearch.org/developers/docs/api/ordinances/Read_Ordinance_Policy_in_French_usecase
     */
    public function testReadOrdinancePolicyInFrench()
    {
        $this->collectionState(new StateFactory());
        $response = $this->collectionState()->getClient()->get($this->policyUri, [
            'headers' => [
                'Authorization' => "Bearer " . $this->collectionState()->getAccessToken(),
                'Accept' => Gedcomx::PLAIN_TEXT,
                'Accept-Language' => 'fr'
            ]
        ]);
        $this->assertEquals(
            HttpStatus::OK,
            $response->getStatusCode(),
            'Error retrieving ordinance policy in French. Returned: ' . HttpStatus::getText($response->getStatusCode()) . "(".$response->getStatusCode().")"
        );
        $this->assertContains(
            "Personnes pour lesquelles vous pouvez accomplir des ordonnances",
            (string) $response->getBody(),
            "Response doesn't appear to be in French."
        );
    }

    /**
     * @vcr OrdinanceTests/testReadOrdinances.json
     * @link https://familysearch.org/developers/docs/api/ordinances/Read_Ordinances_usecase
     */
    public function testReadOrdinances()
    {
        $this->collectionState(new StateFactory());
        $response = $this->collectionState()->getClient()->get($this->ordinanceUri, [
            'headers' => [
                'Authorization' => "Bearer " . $this->collectionState()->getAccessToken(),
                'Accept' => Gedcomx::JSON_MEDIA_TYPE,
                'Content-Type' => Gedcomx::JSON_MEDIA_TYPE
            ]
        ]);
        $this->assertEquals(
            HttpStatus::OK,
            $response->getStatusCode(),
            'Error with valid Ordinance test. Returned: ' . HttpStatus::getText($response->getStatusCode()) . "(".$response->getStatusCode().")"
        );

        $returnedData = json_decode($response->getBody(true));
        $this->assertObjectHasAttribute(
            'collections',
            $returnedData,
            'No collection information found.'
        );
    }

    /**
     * @vcr OrdinanceTests/testReadOrdinancesForbidden.json
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
                SandboxCredentials::API_KEY);
        $response = $collectionState->getClient()->get($this->ordinanceUri, [
            'headers' => [
                'Authorization' => "Bearer " . $collectionState->getAccessToken(),
                'Accept' => Gedcomx::JSON_MEDIA_TYPE,
                'Content-Type' => Gedcomx::JSON_MEDIA_TYPE
            ]
        ]);
        $this->assertEquals(
            HttpStatus::FORBIDDEN,
            $response->getStatusCode(),
            'Error with invalid Ordinance test. Returned: ' . HttpStatus::getText($response->getStatusCode()) . "(".$response->getStatusCode().")"
        );
    }
}
