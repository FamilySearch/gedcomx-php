<?php 

namespace Gedcomx\Tests\Functional;

use Gedcomx\Common\Attribution;
use Gedcomx\Common\ResourceReference;
use Gedcomx\Conclusion\DateInfo;
use Gedcomx\Conclusion\Fact;
use Gedcomx\Conclusion\Relationship;
use Gedcomx\Extensions\FamilySearch\Rs\Client\FamilyTree\FamilyTreePersonState;
use Gedcomx\Extensions\FamilySearch\Rs\Client\FamilyTree\FamilyTreeRelationshipState;
use Gedcomx\Extensions\FamilySearch\Rs\Client\FamilyTree\FamilyTreeStateFactory;
use Gedcomx\Rs\Client\GedcomxApplicationState;
use Gedcomx\Rs\Client\Options\HeaderParameter;
use Gedcomx\Rs\Client\RelationshipState;
use Gedcomx\Rs\Client\Util\HttpStatus;
use Gedcomx\Tests\ApiTestCase;
use Gedcomx\Tests\FactBuilder;
use Gedcomx\Types\FactType;
use Gedcomx\Tests\TestBuilder;

class SpousesTests extends ApiTestCase
{
    
    /**
     * testCreateCoupleRelationship
     * @link https://familysearch.org/developers/docs/api/tree/Create_Couple_Relationship_usecase
     * @see this::testCoupleRelationshipCRUD
     */

    /**
     * @link https://familysearch.org/developers/docs/api/tree/Create_Couple_Relationship_Source_Reference_usecase
     * @see SourcesTests::testCreateCoupleRelationshipSourceReference
     */

    /**
     * @vcr SpousesTests/testCreateCoupleRelationshipConclusion.json
     * @link https://familysearch.org/developers/docs/api/tree/Create_Couple_Relationship_Conclusion_usecase
     */
    public function testCreateCoupleRelationshipConclusion()
    {
        $factory = new FamilyTreeStateFactory();
        $this->collectionState($factory);

        list($husband, $family) = $this->initializeRelationship(array('husband','family'));

        /** @var FamilyTreePersonState $husband */
        $husband = $husband->get();
        $this->assertEquals(
            HttpStatus::OK,
            $husband->getStatus(),
            $this->buildFailMessage(__METHOD__."(getHusband)", $husband)
        );
        $this->assertNotNull($husband->getEntity(), "Get failed. Entity is null.");

        /** @var RelationshipState $family */
        $family = $family->get();
        $this->assertEquals(
            HttpStatus::OK,
            $family->getStatus(),
            $this->buildFailMessage(__METHOD__."(getRelationship)", $family)
        );
        $this->assertNotNull($family->getEntity(), "Get failed. Entity is null.");

        //  Create Marriage Fact

        /** @var Fact $birth */
        $birth = $husband->getPerson()->getFactsOfType(FactType::BIRTH);
        $marriage = FactBuilder::marriage($birth->getDate()->getDateTime());
        $family = $family->addFact($marriage);
        $this->assertEquals(
            HttpStatus::CREATED,
            $family->getStatus(),
            $this->buildFailMessage(__METHOD__."(createSource)", $family)
        );
    }

    /**
     * @link https://familysearch.org/developers/docs/api/tree/Create_Couple_Relationship_Note_usecase
     * @see NotesTests::testCreateCoupleRelationshipNote
     */

    /**
     * testReadCoupleRelationship
     * @link https://familysearch.org/developers/docs/api/tree/Read_Couple_Relationship_usecase
     * @see this::testCoupleRelationshipCRUD
     */

    /**
     * @vcr SpousesTests/testReadCoupleRelationshipConditional.json
     * @link https://familysearch.org/developers/docs/api/tree/Read_Couple_Relationship_(Conditional)_usecase
     */
    public function testReadCoupleRelationshipConditional()
    {
        $factory = new FamilyTreeStateFactory();
        $this->collectionState($factory);

        list($family) = $this->initializeRelationship(array('family'));

        /** @var RelationshipState $family */
        $family = $family->get();
        $this->assertEquals(
            HttpStatus::OK,
            $family->getStatus(),
            $this->buildFailMessage(__METHOD__, $family)
        );

        $etagHeader = $family->getETag();
        $noneMatch = new HeaderParameter(true, HeaderParameter::IF_NONE_MATCH, $etagHeader[0]);
        $eTag = new HeaderParameter(true, HeaderParameter::ETAG, $etagHeader[0]);

        $secondState = $family->get($noneMatch, $eTag, $this->createCacheBreakerQueryParam());

        $this->assertEquals(
            HttpStatus::NOT_MODIFIED,
            $secondState->getStatus(),
            $this->buildFailMessage(__METHOD__, $secondState)
        );
    }

    /**
     * @link https://familysearch.org/developers/docs/api/tree/Read_Couple_Relationship_Source_References_usecase
     * @see SourcesTests::testReadCoupleRelationshipSourceReferences
     */

    /**
     * @link https://familysearch.org/developers/docs/api/tree/Read_Couple_Relationship_Sources_usecase
     * @see SourcesTests::testReadCoupleRelationshipSources
     */

    /**
     * @link https://familysearch.org/developers/docs/api/tree/Read_Couple_Relationship_Notes_usecase
     * @see NotesTests::testReadCoupleRelationshipNotes
     */

    /**
     * @vcr SpousesTests/testReadNonExistentCoupleRelationship.json
     * @link https://familysearch.org/developers/docs/api/tree/Read_Non-Existent_Couple_Relationship_usecase
     */
    public function testReadNonExistentCoupleRelationship()
    {
        $factory = new FamilyTreeStateFactory();
        $this->collectionState($factory);

        $relationship = new Relationship(array(
             "links" => array(
                 array(
                     "rel" => 'relationship',
                     'href' => "https://sandbox.familysearch.org/platform/tree/couple-relationships/XXX-XXXX"
                 )
             )
         ));

        $person = $this->createPerson();
        $this->assertEquals(
            HttpStatus::CREATED,
            $person->getStatus(),
            $this->buildFailMessage(__METHOD__."(person)", $person)
        );

        $relation = $person->readRelationship($relationship);
        $this->assertEquals(
            HttpStatus::NOT_FOUND,
            $relation->getStatus(),
            $this->buildFailMessage(__METHOD__, $relation)
        );
    }

    /**
     * @vcr SpousesTests/testHeadCoupleRelationship.json
     * @link https://familysearch.org/developers/docs/api/tree/Head_Couple_Relationship_usecase
     */
    public function testHeadCoupleRelationship()
    {
        $factory = new FamilyTreeStateFactory();
        $this->collectionState($factory);

        /** @var RelationshipState $family */
        list($family) = $this->initializeRelationship(array('family'));

        /** @var GedcomxApplicationState $headers */
        $headers = $family->head();
        $this->assertEquals(
            HttpStatus::OK,
            $headers->getStatus(),
            $this->buildFailMessage(__METHOD__, $headers)
        );
    }

    /**
     * @vcr SpousesTests/testUpdatePersonsOfCoupleRelationship.json
     * @link https://familysearch.org/developers/docs/api/tree/Update_Persons_of_a_Couple_Relationship_usecase
     */
    public function testUpdatePersonsOfCoupleRelationship()
    {
        $factory = new FamilyTreeStateFactory();
        $this->collectionState($factory);

        /** @var RelationshipState $family */
        list($family) = $this->initializeRelationship(array('family'));

        $family = $family->get();
        $this->assertEquals(
            HttpStatus::OK,
            $family->getStatus(),
            $this->buildFailMessage(__METHOD__."(getFamily1)", $family)
        );
        $this->assertNotNull($family->getEntity(), "Family entity is null.");

        $person3 = $this->createPerson('female');
        $this->assertEquals(
            HttpStatus::CREATED,
            $person3->getStatus(),
            $this->buildFailMessage(__METHOD__."(person3)", $person3)
        );
        $person3 = $person3->get();
        $this->assertEquals(
            HttpStatus::OK,
            $person3->getStatus(),
            $this->buildFailMessage(__METHOD__."(getPerson3)", $person3)
        );
        $this->assertNotNull($person3->getEntity(), "Person3 entity is null.");

        $relationship = $family->getRelationship();
        $relationship->setPerson2($person3->getResourceReference());

        $family = $family->updateSelf($relationship);
        $this->assertEquals(
            HttpStatus::NO_CONTENT,
            $family->getStatus(),
            $this->buildFailMessage(__METHOD__."(updateRelationship)", $family)
        );

        $family = $family->get($this->createCacheBreakerQueryParam());
        $this->assertEquals(
            HttpStatus::OK,
            $family->getStatus(),
            $this->buildFailMessage(__METHOD__."(getFamily2)", $family)
        );
        $this->assertNotNull($family->getEntity(), "Family entity is null.");
        $this->assertEquals(
            $family->getRelationship()->getPerson2()->getResourceId(),
            $person3->getPerson()->getId(),
            "Person3 is not person2 on the relationship object."
        );
    }

    /**
     * @vcr SpousesTests/testUpdateCoupleRelationshipConclusion.json
     * @link https://familysearch.org/developers/docs/api/tree/Update_Couple_Relationship_Conclusion_usecase
     */
    public function testUpdateCoupleRelationshipConclusion()
    {
        $factory = new FamilyTreeStateFactory();
        $this->collectionState($factory);

        list($husband, $family) = $this->initializeRelationship(array('husband','family'));

        /** @var FamilyTreePersonState $husband */
        $husband = $husband->get();
        $this->assertEquals(
            HttpStatus::OK,
            $husband->getStatus(),
            $this->buildFailMessage(__METHOD__."(getHusband)", $husband)
        );
        $this->assertNotNull($husband->getEntity(), "Get failed. Husband entity is null.");

        /** @var RelationshipState $family */
        $family = $family->get();
        $this->assertEquals(
            HttpStatus::OK,
            $family->getStatus(),
            $this->buildFailMessage(__METHOD__."(getRelationship)", $family)
        );
        $this->assertNotNull($family->getEntity(), "Get failed. Family entity is null.");

        //  Create Marriage Fact

        /** @var Fact $birth */
        $birth = $husband->getPerson()->getFactsOfType(FactType::BIRTH);
        $marriage = FactBuilder::marriage($birth->getDate()->getDateTime());
        $factState = $family->addFact($marriage);
        $this->assertEquals(
            HttpStatus::CREATED,
            $factState->getStatus(),
            $this->buildFailMessage(__METHOD__."(createSource)", $factState)
        );

        //  Alter Marriage Fact

        $marriage->setAttribution(new Attribution(array(
                                                      "changeMessage" => TestBuilder::faker()->sentence(6)
                                                  )));
        $currentDate = $marriage->getDate()->getDateTime();
        $newDate = new \DateTime($currentDate->format('Y-m-d')." +5 days");
        $marriage->setDate(new DateInfo(array(
                                            "original" => $newDate->format('F m, Y')
                                        )));
        $family = $family->addFact($marriage);
        $this->assertEquals(
            HttpStatus::CREATED,
            $family->getStatus(),
            $this->buildFailMessage(__METHOD__."(addFact)", $family)
        );
    }

    /**
     * @vcr SpousesTests/testUpdateIllegalCoupleRelationship.json
     * @link https://familysearch.org/developers/docs/api/tree/Update_Illegal_Couple_Relationship_usecase
     */
    public function testUpdateIllegalCoupleRelationship()
    {
        $factory = new FamilyTreeStateFactory();
        $this->collectionState($factory);

        list($family) = $this->initializeRelationship(array('family'));

        /** @var RelationshipState $family */
        $family = $family->get();
        $this->assertEquals(
            HttpStatus::OK,
            $family->getStatus(),
            $this->buildFailMessage(__METHOD__, $family)
        );
        $this->assertNotNull($family->getEntity(), "Family entity is null.");

        /** @var $entity Relationship */
        $entity = $family->getRelationship();

        /* Person1 must be male. Person2 must be female. */
        $person3 = $this->createPerson('male');
        $this->assertEquals(
            HttpStatus::CREATED,
            $person3->getStatus(),
            $this->buildFailMessage(__METHOD__, $person3)
        );

        $entity->setPerson2($person3->getResourceReference());
        /** @var RelationshipState $updated */
        $updated = $family->updateSelf($entity);
        $this->assertEquals(
            HttpStatus::BAD_REQUEST,
            $updated->getStatus(),
            $this->buildFailMessage(__METHOD__, $updated)
        );

        $warnings = $updated->getHeader('warning');
        $this->assertNotEmpty($warnings, "No warnings found in headers.");
    }

    /**
     * testDeleteCoupleRelationship
     * @link https://familysearch.org/developers/docs/api/tree/Delete_Couple_Relationship_usecase
     * @see this::testCoupleRelationshipCRUD
     */

    /**
     * @vcr SpousesTests/testDeleteCoupleRelationshipConclusion.json
     * @link https://familysearch.org/developers/docs/api/tree/Delete_Couple_Relationship_Conclusion_usecase
     */
    public function testDeleteCoupleRelationshipConclusion()
    {
        $factory = new FamilyTreeStateFactory();
        $this->collectionState($factory);

        /** @var FamilyTreePersonState $husband */
        /** @var RelationshipState     $family */
        list($husband, $family) = $this->initializeRelationship(array('husband','family'));

        $husband = $husband->get();
        $this->assertEquals(
            HttpStatus::OK,
            $husband->getStatus(),
            $this->buildFailMessage(__METHOD__."(getHusband)", $husband)
        );
        $this->assertNotNull($husband->getEntity(), "Get failed. Husband entity is null.");

        /* Create Marriage Fact */
        /** @var Fact $birth */
        $birth = $husband->getPerson()->getFactsofType(FactType::BIRTH);
        $marriage = FactBuilder::marriage($birth->getDate()->getDateTime());
        $factState = $family->addFact($marriage);
        $this->assertEquals(
            HttpStatus::CREATED,
            $factState->getStatus(),
            $this->buildFailMessage(__METHOD__."(createSource)", $factState)
        );

        /** @var RelationshipState $family */
        $family = $family->get();
        $this->assertEquals(
            HttpStatus::OK,
            $family->getStatus(),
            $this->buildFailMessage(__METHOD__."(getRelationship)", $family)
        );
        $family->loadConclusions();
        $this->assertNotNull($family->getEntity(), "Family entity is null.");
        $this->assertGreaterThan(
            0,
            count($family->getRelationship()->getFacts()),
            "Fact count should be greater than zero."
        );

        $marriage = $family->getRelationship()->getFactsOfType(FactType::MARRIAGE);
        /** @var RelationshipState $deleted */
        $deleted = $family->deleteFact($marriage);
        $this->assertEquals(
            HttpStatus::NO_CONTENT,
            $deleted->getStatus(),
            $this->buildFailMessage(__METHOD__."(deleteSource)", $deleted)
        );
        $family = $family->get($this->createCacheBreakerQueryParam());
        $this->assertEquals(
            HttpStatus::OK,
            $family->getStatus(),
            $this->buildFailMessage(__METHOD__."(getRelationship)", $family)
        );
        $this->assertNotNull($family->getEntity(), "Family entity is null.");
        $this->assertEquals(
            0,
            count($family->getRelationship()->getFacts()),
            "Fact count should be zero."
        );
    }

    /**
     * @vcr SpousesTests/testRestoreCoupleRelationship.json
     * @link https://familysearch.org/developers/docs/api/tree/Restore_Couple_Relationship_usecase
     */
    public function testRestoreCoupleRelationship()
    {
        $factory = new FamilyTreeStateFactory();
        $this->collectionState($factory);

        /** @var FamilyTreePersonState $husband */
        /** @var RelationshipState     $family */
        list($husband, $family) = $this->initializeRelationship(array('husband','family'));

        $husband = $husband->get();
        $this->assertEquals(
            HttpStatus::OK,
            $husband->getStatus(),
            $this->buildFailMessage(__METHOD__."(getHusband)", $husband)
        );
        $this->assertNotNull($husband->getEntity(), "Get failed. Husband entity is null.");

        $family = $family->get();
        $this->assertEquals(
            HttpStatus::OK,
            $family->getStatus(),
            $this->buildFailMessage(__METHOD__."(getRelationship)", $family)
        );

        $relationship = new Relationship(array(
                                             "links" => array(
                                                 array(
                                                     "rel" => 'relationship',
                                                     'href' => "https://sandbox.familysearch.org/platform/tree/couple-relationships/" . $family->getRelationship()->getId()
                                                 )
                                             )
                                         ));

        /* DELETE */
        $deleted = $family->delete();
        $this->assertEquals(
            HttpStatus::NO_CONTENT,
            $deleted->getStatus(),
            $this->buildFailMessage(__METHOD__.'(delete)',$deleted)
        );

        /** @var FamilyTreeRelationshipState $missing */
        $missing = $husband->readRelationship($relationship, $this->createCacheBreakerQueryParam());
        $this->assertEquals(
            HttpStatus::GONE,
            $missing->getStatus(),
            $this->buildFailMessage(__METHOD__.'(readDeleted)',$missing)
        );

        //  RESTORE

        /** @var FamilyTreeRelationshipState $restored */
        $restored = $missing->restore();
        $this->assertEquals(
            HttpStatus::NO_CONTENT,
            $restored->getStatus(),
            $this->buildFailMessage(__METHOD__.'(restore)', $restored)
        );
        $restored = $husband->readRelationship($relationship, $this->createCacheBreakerQueryParam());
        $this->assertEquals(
            HttpStatus::OK,
            $restored->getStatus(),
            $this->buildFailMessage(__METHOD__.'(readDeleted)',$restored)
        );
        $this->assertNotNull($restored->getEntity(), "Relationship entity is null.");
        $this->assertNotEmpty($restored->getRelationship(), "Relationship not found after restore.");
    }

    /**
     * @vcr SpousesTests/testCoupleRelationshipCRUD.json
     * @link https://familysearch.org/developers/docs/api/tree/Create_Couple_Relationship_usecase
     * @link https://familysearch.org/developers/docs/api/tree/Read_Couple_Relationship_usecase
     * @link https://familysearch.org/developers/docs/api/tree/Update_Persons_of_a_Couple_Relationship_usecase
     * @link https://familysearch.org/developers/docs/api/tree/Delete_Couple_Relationship_usecase
     */
    public function testCoupleRelationshipCRUD()
    {
        $factory = new FamilyTreeStateFactory();
        $this->collectionState($factory);

        // CREATE

        /** @var RelationshipState $family */
        list($family) = $this->initializeRelationship(array('family'));

        //  READ

        $family = $family->get();
        $this->assertEquals(
            HttpStatus::OK,
            $family->getStatus(),
            $this->buildFailMessage(__METHOD__."(getRelationship)", $family)
        );

        /** @var $entity Relationship */
        $entity = $family->getRelationship();
        $data_check =
            $entity->getPerson1() instanceof ResourceReference &&
            $entity->getPerson2() instanceof ResourceReference;
        $this->assertTrue( $data_check );

        // UPDATE

        $person3 = $this->createPerson('female');
        $this->assertEquals(
            HttpStatus::CREATED,
            $person3->getStatus(),
            $this->buildFailMessage(__METHOD__."(person3)", $person3)
        );
        $entity->setPerson2($person3->getResourceReference());
        $updated = $family->updateSelf($entity);
        $this->assertEquals(
            HttpStatus::NO_CONTENT,
            $updated->getStatus(),
            $this->buildFailMessage(__METHOD__.'(update)', $updated)
        );

        //  DELETE

        $deleted = $family->delete();
        $this->assertEquals(
            HttpStatus::NO_CONTENT,
            $deleted->getStatus(),
            $this->buildFailMessage(__METHOD__.'(delete)', $deleted)
        );
    }

    private function initializeRelationship($returnList)
    {
        $husband = $this->createPerson('male');
        $this->assertEquals(
            HttpStatus::CREATED,
            $husband->getStatus(),
            $this->buildFailMessage(__METHOD__."(husband)", $husband)
        );
        $wife = $this->createPerson('female');
        $this->assertEquals(
            HttpStatus::CREATED,
            $wife->getStatus(),
            $this->buildFailMessage(__METHOD__."(wife)", $wife)
        );

        //  Create Relationship

        /** @var RelationshipState $family */
        $family = $this->collectionState()->addSpouseRelationship($husband, $wife);
        $this->assertEquals(
            HttpStatus::CREATED,
            $family->getStatus(),
            $this->buildFailMessage(__METHOD__."(family)", $family)
        );
        $this->queueForDelete($family);

        $return = array();
        foreach ($returnList as $var) {
            $return[] = $$var;
        }
        return $return;
    }
}
