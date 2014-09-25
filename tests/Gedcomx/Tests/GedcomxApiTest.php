<?php


namespace Gedcomx\Tests;

use Gedcomx\Rs\Client\StateFactory;

class GedcomxApiTest extends \PHPUnit_Framework_TestCase {

    public function testLoginAndReadCurrentUser()
    {
        $stateFactory = new StateFactory();
        $currentPerson = $stateFactory
            ->newCollectionState("https://sandbox.familysearch.org/platform/collections/tree")
            ->authenticateViaOAuth2Password("sdktester", "1234sdkpass", "WCQY-7J1Q-GKVV-7DNM-SQ5M-9Q5H-JX3H-CMJK")
            ->readPersonForCurrentUser();


        $this->assertNotNull($currentPerson->getPerson());
    }
    
    public function testLoginAndGetCurrentUserAncestry()
    {
        
        $stateFactory = new StateFactory();
        $ancestryResultsState = $stateFactory
            ->newCollectionState("https://sandbox.familysearch.org/platform/collections/tree")
            ->authenticateViaOAuth2Password("sdktester", "1234sdkpass", "WCQY-7J1Q-GKVV-7DNM-SQ5M-9Q5H-JX3H-CMJK")
            ->readPersonForCurrentUser()
            ->readAncestry();

        $this->assertNotNull($ancestryResultsState);
        
    }

}
