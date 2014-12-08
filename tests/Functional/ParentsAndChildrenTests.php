<?php 

namespace Gedcomx\Tests\Functional;

use Gedcomx\Common\Attribution;
use Gedcomx\Common\ResourceReference;
use Gedcomx\Conclusion\Relationship;
use Gedcomx\Extensions\FamilySearch\Platform\Tree\ChildAndParentsRelationship;
use Gedcomx\Extensions\FamilySearch\Rs\Client\FamilyTree\ChildAndParentsRelationshipState;
use Gedcomx\Extensions\FamilySearch\Rs\Client\FamilyTree\FamilyTreeCollectionState;
use Gedcomx\Extensions\FamilySearch\Rs\Client\FamilyTree\FamilyTreeStateFactory;
use Gedcomx\Rs\Client\Util\HttpStatus;
use Gedcomx\Source\SourceReference;
use Gedcomx\Tests\ApiTestCase;
use Gedcomx\Tests\FactBuilder;

class ParentsAndChildrenTests extends ApiTestCase
{
    /**
     * @link https://familysearch.org/developers/docs/api/tree/Create_Child-and-Parents_Relationship_usecase
     */
    public function testCreateChildAndParentsRelationship()
    {
        $factory = new FamilyTreeStateFactory();
        /** @var FamilyTreeCollectionState $collection */
        $this->collectionState($factory);
        /** @var ChildAndParentsRelationshipState $relation */
        $relation = $this->createRelationship();

        $this->assertAttributeEquals(HttpStatus::CREATED, "statusCode", $relation->getResponse(), $this->buildFailMessage(__METHOD__, $relation));
    }

    /**
     * @link https://familysearch.org/developers/docs/api/tree/Create_Child-and-Parents_Relationship_Source_Reference_usecase
     * @see SourcesTests::testCreateChildAndParentsRelationshipSourceReferences
     */

    /**
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
        $this->assertAttributeEquals(HttpStatus::NO_CONTENT, "statusCode", $factState->getResponse(), $this->buildFailMessage(__METHOD__, $factState));
    }

    /**
     * @link https://familysearch.org/developers/docs/api/tree/Create_Child-and-Parents_Relationship_Note_usecase
     * @see NotesTests::testCreateChildAndParentsRelationshipNote
     */

    /**
     * @link https://familysearch.org/developers/docs/api/tree/Create_Couple_Relationship_usecase
     */
    public function testCreateCoupleRelationship()
    {
        $factory = new FamilyTreeStateFactory();
        $this->collectionState($factory);

        $person1 = $this->createPerson('male')->get();
        $person2 = $this->createPerson('female')->get();

        $relation = $this->collectionState()->addSpouseRelationship($person1, $person2);
        $this->queueForDelete($relation);

        $this->assertAttributeEquals(HttpStatus::CREATED, "statusCode", $relation->getResponse(), $this->buildFailMessage(__METHOD__, $relation));

        $relation = $relation->get();
        /** @var Relationship $entity */
        $entity = $relation->getRelationship();

        $data_check = $entity->getPerson1() instanceof ResourceReference
            && $entity->getPerson2() instanceof ResourceReference;
        $this->assertTrue( $data_check );
    }

    /**
     * @link https://familysearch.org/developers/docs/api/tree/Read_Child-and-Parents_Relationship_usecase
     */
    public function testReadChildAndParentsRelationship()
    {
        $factory = new FamilyTreeStateFactory();
        $this->collectionState($factory);

        $relation = $this->createRelationship();
        $this->assertAttributeEquals(HttpStatus::CREATED, "statusCode", $relation->getResponse(), $this->buildFailMessage(__METHOD__, $relation) );

        /** @var ChildAndParentsRelationshipState $relation */
        $relation = $relation->get();
        $this->assertAttributeEquals(HttpStatus::OK, "statusCode", $relation->getResponse(), $this->buildFailMessage(__METHOD__, $relation) );

        /** @var ChildAndParentsRelationship $entity */
        $entity = $relation->getRelationship();
        $data_check = $entity->getFather() instanceof ResourceReference
            && $entity->getMother() instanceof ResourceReference
            && $entity->getChild() instanceof ResourceReference;
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
     * @link https://familysearch.org/developers/docs/api/tree/Update_Child-and-Parents_Relationship_usecase
     */
    public function testUpdateChildAndParentRelationship()
    {
        $factory = new FamilyTreeStateFactory();
        $this->collectionState($factory);

        $relation = $this->createRelationship();
        $this->assertAttributeEquals(HttpStatus::CREATED, "statusCode", $relation->getResponse(), $this->buildFailMessage(__METHOD__."(create)", $relation) );

        $relation = $relation->get();
        $this->assertAttributeEquals(HttpStatus::OK, "statusCode", $relation->getResponse(), $this->buildFailMessage(__METHOD__."(read)", $relation) );

        $mother = $this->createPerson('female')->get();
        $updated = $relation->updateMotherWithPersonState($mother);
        $this->assertAttributeEquals(HttpStatus::NO_CONTENT, "statusCode", $updated->getResponse(), $this->buildFailMessage(__METHOD__."(update)", $updated) );
    }

    /**
     * @link https://familysearch.org/developers/docs/api/tree/Update_Child-and-Parents_Relationship_Conclusion_usecase
     */
    public function testUpdateChildAndParentsRelationshipConclusion()
    {
        $factory = new FamilyTreeStateFactory();
        $this->collectionState($factory);

        /** @var ChildAndParentsRelationshipState $relation */
        $relation = $this->createRelationship();
        $this->assertAttributeEquals(HttpStatus::CREATED, "statusCode", $relation->getResponse(), $this->buildFailMessage(__METHOD__."(create)", $relation));

        $fact = FactBuilder::adoptiveParent();
        $relation = $relation->addFatherFact($fact);
        $this->assertAttributeEquals(HttpStatus::NO_CONTENT, "statusCode", $relation->getResponse(), $this->buildFailMessage(__METHOD__."(addFact)", $relation));

        $relation = $relation->get();
        $facts = $relation->getRelationship()->getFatherFacts();
        $factState = $relation->updateFatherFact($facts[0]);
        $this->assertAttributeEquals(HttpStatus::NO_CONTENT, "statusCode", $factState->getResponse(), $this->buildFailMessage(__METHOD__."(updateFact)", $factState));
    }

    /**
     * @link https://familysearch.org/developers/docs/api/tree/Update_Child-and-Parents_Relationship_Conclusion_usecase
     * @link https://familysearch.org/developers/docs/api/tree/Restore_Child-and-Parents_Relationship_usecase
     */
    public function testDeleteAndRestoreChildAndParentsRelationship()
    {
        $factory = new FamilyTreeStateFactory();
        $this->collectionState($factory);

        /** @var ChildAndParentsRelationshipState $relation */
        $relation = $this->createRelationship();
        $this->assertAttributeEquals(HttpStatus::CREATED, "statusCode", $relation->getResponse(), $this->buildFailMessage(__METHOD__."(create)", $relation));

        /* DELETE */
        $deleted = $relation->delete();
        $this->assertAttributeEquals(HttpStatus::NO_CONTENT, "statusCode", $deleted->getResponse(), $this->buildFailMessage(__METHOD__."(delete)", $deleted));

        $missing = $deleted->get();
        $this->assertAttributeEquals(HttpStatus::GONE, "statusCode", $missing->getResponse(), $this->buildFailMessage(__METHOD__."(read)", $missing));

        $restored = $missing->restore();
        $this->assertAttributeEquals(HttpStatus::NO_CONTENT, "statusCode", $restored->getResponse(), $this->buildFailMessage(__METHOD__."(restore)", $restored));
    }

    /**
     * @link https://familysearch.org/developers/docs/api/tree/Delete_Child-and-Parents_Relationship_Source_Reference_usecase
     * @see SourcesTests::testDeleteChildAndParentsRelationshipSourceReference
     */

    /**
     * @link https://familysearch.org/developers/docs/api/tree/Delete_Child-and-Parents_Relationship_Conclusion_usecase
     */
    public function testDeleteChildAndParentsRelationshipConclusion(){
        $factory = new FamilyTreeStateFactory();
        $this->collectionState($factory);

        /** @var ChildAndParentsRelationshipState $relation */
        $relation = $this->createRelationship();
        $this->assertAttributeEquals(HttpStatus::CREATED, "statusCode", $relation->getResponse(), $this->buildFailMessage(__METHOD__."(create)", $relation));

        $fact = FactBuilder::adoptiveParent();
        $relation = $relation->addFatherFact($fact);
        $this->assertAttributeEquals(HttpStatus::NO_CONTENT, "statusCode", $relation->getResponse(), $this->buildFailMessage(__METHOD__."(addFact)", $relation));

        $relation = $relation->get();
        $facts = $relation->getRelationship()->getFatherFacts();
        $factState = $relation->deleteFact($facts[0]);
        $this->assertAttributeEquals(HttpStatus::NO_CONTENT, "statusCode", $factState->getResponse(), $this->buildFailMessage(__METHOD__."(updateFact)", $factState));
    }

    /**
     * @link https://familysearch.org/developers/docs/api/tree/Delete_Child-and-Parents_Relationship_Parent_usecase
     */
    public function testDeleteParentFromRelationship()
    {
        $factory = new FamilyTreeStateFactory();
        $this->collectionState($factory);

        /** @var ChildAndParentsRelationshipState $relation */
        $relation = $this->createRelationship();
        $this->assertAttributeEquals(HttpStatus::CREATED, "statusCode", $relation->getResponse(), $this->buildFailMessage(__METHOD__."(create)", $relation) );

        $relation = $relation->get();
        $updated = $relation->deleteFather();
        $this->assertAttributeEquals(HttpStatus::NO_CONTENT, "statusCode", $updated->getResponse(), $this->buildFailMessage(__METHOD__."(update)", $updated) );

        $relation = $relation->get();
        $this->assertEmpty($relation->getRelationship()->getFather(), "Father should have been deleted" );
    }

}