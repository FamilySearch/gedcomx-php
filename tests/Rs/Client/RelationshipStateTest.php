<?php


namespace Gedcomx\Tests\Rs\Client;


use Gedcomx\Common\ResourceReference;
use Gedcomx\Conclusion\Relationship;
use Gedcomx\Extensions\FamilySearch\Platform\Tree\ChildAndParentsRelationship;
use Gedcomx\Extensions\FamilySearch\Rs\Client\FamilyTree\FamilyTreeStateFactory;
use Gedcomx\Extensions\FamilySearch\Rs\Client\Rel;
use Gedcomx\Tests\ApiTestCase;

class RelationshipStateTest extends ApiTestCase {

	/**
	 * @link https://familysearch.org/developers/docs/api/tree/Create_Couple_Relationship_Note_usecase
	 */
	public function testCreateCoupleRelationshipNote()
	{
        $factory = new FamilyTreeStateFactory();
        $this->collectionState($factory);

        $person1 = $this->createPerson();
        $person2 = $this->createPerson();

        $oneRef = new ResourceReference(array(
            "resource" => $person1->getSelfUri()
        ));
        $twoRef = new ResourceReference(array(
            "resource" => $person2->getSelfUri()
        ));

        $relationship = new Relationship();
        $relationship->setPerson1($oneRef);
        $relationship->setPerson2($twoRef);

        $relation = $this->collectionState()->addRelationship($relationship);

		$this->assertTrue(true);
	}

    /**
     * @link https://familysearch.org/developers/docs/api/tree/Create_Child-and-Parents_Relationship_usecase
     */
    public function testCreateChildAndParentsRelationship(){
        $factory = new FamilyTreeStateFactory();
        $this->collectionState($factory);

        $child = $this->createPerson();
        $person1 = $this->createPerson();
        $person2 = $this->createPerson();

        $childRef = new ResourceReference(array(
            "resource" => $child->getSelfUri()
        ));
        $oneRef = new ResourceReference(array(
            "resource" => $person1->getSelfUri()
        ));
        $twoRef = new ResourceReference(array(
            "resource" => $person2->getSelfUri()
        ));


        $relationship = new ChildAndParentsRelationship();
        $relationship->setFather($oneRef);
        $relationship->setMother($twoRef);
        $relationship->setChild($childRef);

    }
} 