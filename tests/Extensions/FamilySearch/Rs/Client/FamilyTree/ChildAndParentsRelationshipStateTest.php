<?php

namespace Gedcomx\tests\Extensions\FamilySearch\Rs\Client\FamilyTree;

use Gedcomx\Extensions\FamilySearch\Platform\Tree\ChildAndParentsRelationship;
use Gedcomx\Extensions\FamilySearch\Rs\Client\FamilyTree\FamilyTreeCollectionState;
use Gedcomx\Extensions\FamilySearch\Rs\Client\FamilyTree\FamilyTreeStateFactory;
use Gedcomx\Rs\Client\Util\HttpStatus;
use Gedcomx\Tests\ApiTestCase;
use Gedcomx\Tests\NoteBuilder;

class ChildAndParentsRelationshipStateTest extends ApiTestCase
{
    /**
     * @link https://familysearch.org/developers/docs/api/tree/Create_Child-and-Parents_Relationship_usecase
     */
    public function testCreateChildAndParentsRelationship()
    {
        $factory = new FamilyTreeStateFactory();
        /** @var FamilyTreeCollectionState $collection */
        $collection = $this->collectionState($factory);

        $relation = $this->createRelationship();
        $newState = $collection->addChildAndParentsRelationship($relation);

        $this->assertAttributeEquals(HttpStatus::CREATED, "statusCode", $newState->getResponse(), $this->buildFailMessage(__METHOD__, $newState));
    }

    /**
     * @link https://familysearch.org/developers/docs/api/tree/Create_Child-and-Parents_Relationship_Source_Reference_usecase
     */
    public function testCreateChildAndParentsRelationshipSourceReferences()
    {
        //todo
    }

    /**
     * @link https://familysearch.org/developers/docs/api/tree/Create_Child-and-Parents_Relationship_Conclusion_usecase
     */
    public function testCreateChildAndParentsRelationshipConclusion()
    {
        //todo
    }

    /**
     * @link https://familysearch.org/developers/docs/api/tree/Create_Child-and-Parents_Relationship_Note_usecase
     */
    public function testCreateChildAndParentsRelationshipNote()
    {
        $factory = new FamilyTreeStateFactory();
        /** @var FamilyTreeCollectionState $collection */
        $collection = $this->collectionState($factory);

        $relation = $this->createRelationship();
        $relationState = $collection->addChildAndParentsRelationship($relation);
        $this->assertAttributeEquals(HttpStatus::CREATED, "statusCode", $relationState->getResponse(), $this->buildFailMessage(__METHOD__, $relationState));

        $note = NoteBuilder::createNote();
        $noteState = $relationState->addNote($note);
        $this->assertAttributeEquals(HttpStatus::CREATED, "statusCode", $noteState->getResponse(), $this->buildFailMessage(__METHOD__, $noteState));
    }

    /**
     * @link https://familysearch.org/developers/docs/api/tree/Read_Child-and-Parents_Relationship_usecase
     */
    public function testReadChildAndParentsRelationship()
    {
        //todo
    }

    /**
     * @link https://familysearch.org/developers/docs/api/tree/Read_Child-and-Parents_Relationship_Source_References_usecase
     * @link https://familysearch.org/developers/docs/api/tree/Read_Child-and-Parents_Relationship_Sources_usecase
     */
    public function testReadChildAndParentsRelationshipSourceReferences()
    {
        //todo
    }

    /**
     * @link https://familysearch.org/developers/docs/api/tree/Read_Child-and-Parents_Relationship_Notes_usecase
     */
    public function testReadChildAndParentsRelationshipNotes()
    {
        $factory = new FamilyTreeStateFactory();
        /** @var FamilyTreeCollectionState $collection */
        $collection = $this->collectionState($factory);

        $relation = $this->createRelationship();
        $relationState = $collection->addChildAndParentsRelationship($relation);
        $this->assertAttributeEquals(HttpStatus::CREATED, "statusCode", $relationState->getResponse(), $this->buildFailMessage(__METHOD__, $relationState));

        $note = NoteBuilder::createNote();
        $noteState = $relationState->addNote($note);
        $this->assertAttributeEquals(HttpStatus::CREATED, "statusCode", $noteState->getResponse(), $this->buildFailMessage(__METHOD__, $noteState));

        $relationState = $relationState->get();
        $relationState = $relationState->loadNotes();
        $this->assertNotEmpty($relationState->getRelationship()->getNotes());
    }

    /**
     * @link https://familysearch.org/developers/docs/api/tree/Update_Child-and-Parents_Relationship_usecase
     */
    public function testUpdateChildAndParentsRelationship()
    {
        //todo
    }

    /**
     * @link https://familysearch.org/developers/docs/api/tree/Update_Child-and-Parents_Relationship_Conclusion_usecase
     */
    public function testUpdateChildAndParentsRelationshipConclusion()
    {
        //todo
    }

    /**
     * @link https://familysearch.org/developers/docs/api/tree/Delete_Child-and-Parents_Relationship_Source_Reference_usecase
     */
    public function testDeleteChildAndParentsRelationshipSourceReference()
    {
        //todo
    }

    /**
     * @link https://familysearch.org/developers/docs/api/tree/Update_Child-and-Parents_Relationship_Conclusion_usecase
     * @link https://familysearch.org/developers/docs/api/tree/Restore_Child-and-Parents_Relationship_usecase
     */
    public function testDeleteAndRestoreChildAndParentsRelationship()
    {
        //todo
    }

    private function createRelationship()
    {
        $father = $this->createPerson('male')->get();
        $mother = $this->createPerson('female')->get();
        $child = $this->createPerson()->get();

        $rel = new ChildAndParentsRelationship();
        $rel->setChild($child->getResourceReference());
        $rel->setFather($father->getResourceReference());
        $rel->setMother($mother->getResourceReference());

        return $rel;
    }
}