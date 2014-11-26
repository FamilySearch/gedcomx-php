<?php

namespace Gedcomx\Tests\Rs\Client;

use Gedcomx\Common\Attribution;
use Gedcomx\Common\Note;
use Gedcomx\Conclusion\Gender;
use Gedcomx\Conclusion\Person;
use Gedcomx\Conclusion\Relationship;
use Gedcomx\Extensions\FamilySearch\Platform\Tree\ChildAndParentsRelationship;
use Gedcomx\Extensions\FamilySearch\Platform\Tree\DiscussionReference;
use Gedcomx\Extensions\FamilySearch\Rs\Client\FamilyTree\FamilyTreeStateFactory;
use Gedcomx\Rs\Client\Options\HeaderParameter;
use Gedcomx\Rs\Client\Options\Preconditions;
use Gedcomx\Rs\Client\Options\QueryParameter;
use Gedcomx\Rs\Client\Rel;
use Gedcomx\Rs\Client\StateFactory;
use Gedcomx\Source\SourceReference;
use Gedcomx\Tests\ApiTestCase;
use Gedcomx\Tests\DiscussionBuilder;
use Gedcomx\Tests\FactBuilder;
use Gedcomx\Tests\NoteBuilder;
use Gedcomx\Tests\PersonBuilder;
use Gedcomx\Tests\SourceBuilder;
use Gedcomx\Types\GenderType;
use Gedcomx\Rs\Client\Util\HttpStatus;

/*
 * Testing use cases https://familysearch.org/developers/docs/api/tree/Person_resource
 *
 * Only testing we get the expected response codes from the API. Data validation will
 * have to be added elsewhere.
 */
class PersonStateTest extends ApiTestCase{

    /**
     * @link https://familysearch.org/developers/docs/api/tree/Create_Person_Source_Reference_usecase
     * @throws \Gedcomx\Rs\Client\Exception\GedcomxApplicationException
     */
    public function testCreatePersonSourceReferenceWithStateObject()
    {
        $factory = new StateFactory();
        $this->collectionState($factory);

        if( self::$personState == null ){
            self::$personState = $this->createPerson();
        }
        $source = SourceBuilder::newSource();
        $sourceState = $this->collectionState()->addSourceDescription($source);

        $newState = self::$personState->addSourceReferenceState($sourceState);

        $this->assertAttributeEquals(HttpStatus::CREATED, "statusCode", $newState->getResponse() );
    }

    /**
     * @link https://familysearch.org/developers/docs/api/tree/Read_User_usecase
     */
    public function testAgentReadUser()
    {
        $factory = new StateFactory();
        $this->collectionState($factory);
        $personState = $this->createPerson()->get();
        $names = $personState->getPerson()->getNames();
        $agentState = $personState->readAttributableContributor($names[0]);

        $this->assertEquals(
            HttpStatus::OK,
            $agentState->getResponse()->getStatusCode(),
            $this->buildFailMessage(__METHOD__, $agentState)
        );
        $this->assertNotEmpty($agentState->getAgent());
    }


}