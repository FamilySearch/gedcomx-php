<?php 

namespace Gedcomx\Tests\Functional;

use Gedcomx\Common\ResourceReference;
use Gedcomx\Conclusion\DateInfo;
use Gedcomx\Conclusion\Fact;
use Gedcomx\Conclusion\Relationship;
use Gedcomx\Extensions\FamilySearch\Platform\Tree\ChildAndParentsRelationship;
use Gedcomx\Extensions\FamilySearch\Rs\Client\FamilyTree\ChildAndParentsRelationshipState;
use Gedcomx\Extensions\FamilySearch\Rs\Client\FamilyTree\FamilyTreeCollectionState;
use Gedcomx\Extensions\FamilySearch\Rs\Client\FamilyTree\FamilyTreePersonState;
use Gedcomx\Extensions\FamilySearch\Rs\Client\FamilyTree\FamilyTreeRelationshipState;
use Gedcomx\Extensions\FamilySearch\Rs\Client\FamilyTree\FamilyTreeStateFactory;
use Gedcomx\Rs\Client\Util\HttpStatus;
use Gedcomx\Tests\ApiTestCase;
use Gedcomx\Tests\FactBuilder;

class ParentsAndChildrenTests extends ApiTestCase
{
    
    /**
     * @vcr ParentsAndChildrenTests/testCreateChildAndParentsRelationship.json
     * @link https://familysearch.org/developers/docs/api/tree/Create_Child-and-Parents_Relationship_usecase
     */
    public function testCreateChildAndParentsRelationship()
    {
        $factory = new FamilyTreeStateFactory();
        /** @var FamilyTreeCollectionState $collection */
        $this->collectionState($factory);
        /** @var ChildAndParentsRelationshipState $relation */
        $relation = $this->createRelationship();
        $relation = $relation->get();
        $this->assertEquals(
            HttpStatus::OK,
            $relation->getStatus(),
            $this->buildFailMessage(__METHOD__, $relation)
        );
        $this->assertNotNull($relation->getEntity(), 'Relationship entity is null.');
        $this->assertNotEmpty($relation->getRelationship(), 'Relationship object missing.');
        $this->assertNotNull($relation->getRelationship()->getFather(), 'Father reference is missing.');
        $this->assertInstanceOf('\Gedcomx\Common\ResourceReference', $relation->getRelationship()->getFather(), 'Father is not a ResourceReference.');
        $this->assertNotNull($relation->getRelationship()->getMother(), 'Mother reference is missing.');
        $this->assertInstanceOf('\Gedcomx\Common\ResourceReference', $relation->getRelationship()->getMother(), 'Mother is not a ResourceReference');
        $this->assertNotNull($relation->getRelationship()->getChild(), 'Child reference is missing.');
        $this->assertInstanceOf('\Gedcomx\Common\ResourceReference', $relation->getRelationship()->getChild(), 'Child is not a ResourceReference');
    }

    /**
     * @link https://familysearch.org/developers/docs/api/tree/Create_Child-and-Parents_Relationship_Source_Reference_usecase
     * @see SourcesTests::testCreateChildAndParentsRelationshipSourceReferences
     */

    /**
     * @vcr ParentsAndChildrenTests/testCreateChildAndParentsRelationshipConclusion.json
     * @link https://familysearch.org/developers/docs/api/tree/Create_Child-and-Parents_Relationship_Conclusion_usecase
     */
    public function testCreateChildAndParentsRelationshipConclusion()
    {
        $factory = new FamilyTreeStateFactory();
        /** @var FamilyTreeCollectionState $collection */
        $this->collectionState($factory);

        /** @var ChildAndParentsRelationshipState $relation */
        $relation = $this->createRelationship();

        $fact = FactBuilder::adoptiveParent();
        $factState = $relation->addFatherFact($fact);
        $this->assertEquals(
            HttpStatus::NO_CONTENT,
            $factState->getStatus(),
            $this->buildFailMessage(__METHOD__, $factState)
        );
        /** @var ChildAndParentsRelationshipState $factState */
        $factState = $factState->get();
        $this->assertEquals(
            HttpStatus::OK,
            $factState->getStatus(),
            $this->buildFailMessage(__METHOD__, $factState)
        );
        $this->assertNotNull($factState->getEntity(), "FactState entity is null");
        $this->assertNotEmpty($factState->getRelationship()->getFatherFacts(), "FatherFacts missing from relationship.");
    }

    /**
     * @vcr ParentsAndChildrenTests/testReadChildAndParentsRelationship.json
     * @link https://familysearch.org/developers/docs/api/tree/Read_Child-and-Parents_Relationship_usecase
     */
    public function testReadChildAndParentsRelationship()
    {
        $factory = new FamilyTreeStateFactory();
        $this->collectionState($factory);

        $relation = $this->createRelationship();

        /** @var ChildAndParentsRelationshipState $relation */
        $relation = $relation->get();
        $this->assertEquals(
            HttpStatus::OK,
            $relation->getStatus(),
            $this->buildFailMessage(__METHOD__, $relation)
        );
        $this->assertNotNull($relation->getEntity(), "Relationship entity is null.");
        $this->assertNotNull($relation->getRelationship(), "Relationship object is null.");
        $this->assertNotNull($relation->getRelationship()->getFather(), "Father object is null.");
        $this->assertNotNull($relation->getRelationship()->getMother(), "Mother object is null.");
        $this->assertNotNull($relation->getRelationship()->getChild(), "Child object is null.");

        /** @var ChildAndParentsRelationship $entity */
        $entity = $relation->getRelationship();
        $data_check =
            $entity->getFather() instanceof ResourceReference &&
            $entity->getMother() instanceof ResourceReference &&
            $entity->getChild() instanceof ResourceReference;
        $this->assertTrue( $data_check );
    }

    /**
     * @link https://familysearch.org/developers/docs/api/tree/Read_Child-and-Parents_Relationship_Source_References_usecase
     * @see SourcesTests::testReadChildAndParentsRelationshipSourceReferences
     */

    /**
     * @link https://familysearch.org/developers/docs/api/tree/Read_Child-and-Parents_Relationship_Sources_usecase
     * @see SourcesTests::testReadChildAndParentsRelationshipSources
     */

    /**
     * @link https://familysearch.org/developers/docs/api/tree/Read_Child-and-Parents_Relationship_Note_usecase
     * @see NotesTests::testReadChildAndParentsRelationshipNote
     */

    /**
     * @vcr ParentsAndChildrenTests/testUpdateChildAndParentRelationship.json
     * @link https://familysearch.org/developers/docs/api/tree/Update_Child-and-Parents_Relationship_usecase
     */
    public function testUpdateChildAndParentRelationship()
    {
        $factory = new FamilyTreeStateFactory();
        $this->collectionState($factory);

        /** @var ChildAndParentsRelationshipState $relation */
        $relation = $this->createRelationship();

        /** @var FamilyTreePersonState $mother */
        $mother = $this->createPerson('female');
        $this->assertEquals(
            HttpStatus::CREATED,
            $mother->getStatus(),
            $this->buildFailMessage(__METHOD__."(createMother)", $mother)
        );
        $mother = $mother->get();
        $this->assertEquals(
            HttpStatus::OK,
            $mother->getStatus(),
            $this->buildFailMessage(__METHOD__."(readMother)", $mother)
        );
        $this->queueForDelete($mother);

        /** @var FamilyTreeRelationshipState $updated */
        $updated = $relation->updateMotherWithPersonState($mother);
        $this->assertEquals(
            HttpStatus::NO_CONTENT,
            $updated->getStatus(),
            $this->buildFailMessage(__METHOD__."(update)", $updated)
        );
        
        $relation = $relation->get();
        $this->assertEquals(
            HttpStatus::OK,
            $relation->getStatus(),
            $this->buildFailMessage(__METHOD__."(readRelationship)", $relation)
        );
        $this->assertNotNull($relation->getEntity(), "Relationship entity is null.");

        $relationship = $relation->getRelationship();
        $this->assertEquals(
            $relationship->getMother()->getResourceId(),
            $mother->getPerson()->getId(),
            "Mother ids do not match"
        );
    }

    /**
     * @vcr ParentsAndChildrenTests/testUpdateChildAndParentsRelationshipConclusion.json
     * @link https://familysearch.org/developers/docs/api/tree/Update_Child-and-Parents_Relationship_Conclusion_usecase
     */
    public function testUpdateChildAndParentsRelationshipConclusion()
    {
        $factory = new FamilyTreeStateFactory();
        $this->collectionState($factory);

        /** @var ChildAndParentsRelationshipState $relation */
        $relation = $this->createRelationship();

        $fact = FactBuilder::adoptiveParent();
        $relation = $relation->addFatherFact($fact);
        $this->assertEquals(
            HttpStatus::NO_CONTENT,
            $relation->getStatus(),
            $this->buildFailMessage(__METHOD__."(addFact)", $relation)
        );

        $relation = $relation->get();
        $this->assertEquals(
            HttpStatus::OK,
            $relation->getStatus(),
            $this->buildFailMessage(__METHOD__."(addFact)", $relation)
        );
        /** @var Fact[] $facts */
        $facts = $relation->getRelationship()->getFatherFacts();
        $facts[0]->setDate(new DateInfo(array('original' => "January 1, 1901")));

        /** @var ChildAndParentsRelationshipState $factState */
        $factState = $relation->updateFatherFact($facts[0]);
        $this->assertEquals(
            HttpStatus::NO_CONTENT,
            $factState->getStatus(),
            $this->buildFailMessage(__METHOD__."(updateFact)", $factState)
        );
    }

    /**
     * @vcr ParentsAndChildrenTests/testDeleteAndRestoreChildAndParentsRelationship.json
     * @link https://familysearch.org/developers/docs/api/tree/Update_Child-and-Parents_Relationship_Conclusion_usecase
     * @link https://familysearch.org/developers/docs/api/tree/Restore_Child-and-Parents_Relationship_usecase
     */
    public function testDeleteAndRestoreChildAndParentsRelationship()
    {
        $factory = new FamilyTreeStateFactory();
        $this->collectionState($factory);

        /** @var ChildAndParentsRelationshipState $relation */
        $relation = $this->createRelationship();

        // DELETE
        /** @var ChildAndParentsRelationshipState $deleted */
        $deleted = $relation->delete();
        $this->assertEquals(
            HttpStatus::NO_CONTENT,
            $deleted->getStatus(),
            $this->buildFailMessage(__METHOD__."(delete)", $deleted)
        );

        /** @var ChildAndParentsRelationshipState $missing */
        $missing = $deleted->get();
        $this->assertEquals(
            HttpStatus::GONE,
            $missing->getStatus(),
            $this->buildFailMessage(__METHOD__."(read)", $missing)
        );

        /** @var ChildAndParentsRelationshipState $restored */
        $restored = $missing->restore();
        $this->assertEquals(
            HttpStatus::NO_CONTENT,
            $restored->getStatus(),
            $this->buildFailMessage(__METHOD__."(restore)", $restored)
        );
    }

    /**
     * @link https://familysearch.org/developers/docs/api/tree/Delete_Child-and-Parents_Relationship_Source_Reference_usecase
     * @see SourcesTests::testDeleteChildAndParentsRelationshipSourceReference
     */

    /**
     * @vcr ParentsAndChildrenTests/testDeleteChildAndParentsRelationshipConclusion.json
     * @link https://familysearch.org/developers/docs/api/tree/Delete_Child-and-Parents_Relationship_Conclusion_usecase
     */
    public function testDeleteChildAndParentsRelationshipConclusion(){
        $factory = new FamilyTreeStateFactory();
        $this->collectionState($factory);

        /** @var ChildAndParentsRelationshipState $relation */
        $relation = $this->createRelationship();

        $fact = FactBuilder::adoptiveParent();
        $relation = $relation->addFatherFact($fact);
        $this->assertEquals(
            HttpStatus::NO_CONTENT,
            $relation->getStatus(),
            $this->buildFailMessage(__METHOD__."(addFact)", $relation)
        );

        $relation = $relation->get();
        $this->assertEquals(
            HttpStatus::OK,
            $relation->getStatus(),
            $this->buildFailMessage(__METHOD__."(readRelationship)", $relation)
        );
        $this->assertNotNull($relation->getEntity(), "Relationship entity is null.");
        $facts = $relation->getRelationship()->getFatherFacts();
        $this->assertNotEmpty($facts, "FatherFacts are empty.");

        $factState = $relation->deleteFact($facts[0]);
        $this->assertEquals(
            HttpStatus::NO_CONTENT,
            $factState->getStatus(),
            $this->buildFailMessage(__METHOD__."(deleteFact)", $factState)
        );
    }

    /**
     * @vcr ParentsAndChildrenTests/testDeleteParentFromRelationship.json
     * @link https://familysearch.org/developers/docs/api/tree/Delete_Child-and-Parents_Relationship_Parent_usecase
     */
    public function testDeleteParentFromRelationship()
    {
        $factory = new FamilyTreeStateFactory();
        $this->collectionState($factory);

        /** @var ChildAndParentsRelationshipState $relation */
        $relation = $this->createRelationship();

        $relation = $relation->get();
        $this->assertEquals(
            HttpStatus::OK,
            $relation->getStatus(),
            $this->buildFailMessage(__METHOD__."(readRelationship)", $relation)
        );
        $updated = $relation->deleteFather();
        $this->assertEquals(
            HttpStatus::NO_CONTENT,
            $updated->getStatus(),
            $this->buildFailMessage(__METHOD__."(update)", $updated)
        );
    }

}
