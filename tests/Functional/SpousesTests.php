<?php 

namespace Gedcomx\Tests\Functional;

use Gedcomx\Common\Attribution;
use Gedcomx\Common\ResourceReference;
use Gedcomx\Conclusion\DateInfo;
use Gedcomx\Conclusion\Fact;
use Gedcomx\Conclusion\Relationship;
use Gedcomx\Extensions\FamilySearch\Rs\Client\FamilyTree\FamilyTreeStateFactory;
use Gedcomx\Rs\Client\Options\HeaderParameter;
use Gedcomx\Rs\Client\RelationshipState;
use Gedcomx\Rs\Client\Util\HttpStatus;
use Gedcomx\Tests\ApiTestCase;
use Gedcomx\Tests\FactBuilder;
use Gedcomx\Tests\PersonBuilder;
use Gedcomx\Types\FactType;

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
     * @link https://familysearch.org/developers/docs/api/tree/Create_Couple_Relationship_Conclusion_usecase
     */
    public function testCreateCoupleRelationshipConclusion()
    {
        $factory = new FamilyTreeStateFactory();
        $this->collectionState($factory);

        $person1 = $this->createPerson('male')->get();
        $person2 = $this->createPerson('female')->get();

        /* Create Relationship */
        /** @var $relation RelationshipState */
        $relation = $this->collectionState()->addSpouseRelationship($person1, $person2)->get();
        $this->queueForDelete($relation);
        $this->assertAttributeEquals(HttpStatus::OK, "statusCode", $relation->getResponse(), $this->buildFailMessage(__METHOD__."(addSpouse)", $relation));

        /* Create Marriage Fact */
        /** @var Fact $birth */
        $birth = $person1->getPerson()->getFactsOfType(FactType::BIRTH);
        $marriage = FactBuilder::marriage($birth->getDate()->getDateTime());
        $relation = $relation->addFact($marriage);
        $this->assertAttributeEquals(HttpStatus::NO_CONTENT, "statusCode", $relation->getResponse(), $this->buildFailMessage(__METHOD__."(createSource)", $relation));
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
     * @link https://familysearch.org/developers/docs/api/tree/Read_Couple_Relationship_(Conditional)_usecase
     */
    public function testReadCoupleRelationshipConditional()
    {
        $factory = new FamilyTreeStateFactory();
        $this->collectionState($factory);

        $person1 = $this->createPerson('male')->get();
        $person2 = $this->createPerson('female')->get();

        //$relation = $this->collectionState()->addSpouseRelationship($person1, $person2);
        $relation = $person1->addSpouse($person2);
        $this->queueForDelete($relation);

        $relation = $relation->get();
        $this->assertAttributeEquals(HttpStatus::OK, "statusCode", $relation->getResponse(), $this->buildFailMessage(__METHOD__, $relation));

        $etagHeader = $relation->getEtag()->toArray();
        $noneMatch = new HeaderParameter(true, HeaderParameter::IF_NONE_MATCH, $etagHeader[0]);
        $eTag = new HeaderParameter(true, HeaderParameter::ETAG, $etagHeader[0]);

        $secondState = $relation->get($noneMatch, $eTag);

        $this->assertAttributeEquals(HttpStatus::NOT_MODIFIED, "statusCode", $secondState->getResponse() );
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

        $person1 = $this->createPerson();
        $relation = $person1->readRelationship($relationship);
        $this->assertAttributeEquals(HttpStatus::NOT_FOUND, "statusCode", $relation->getResponse());
    }

    /**
     * @link https://familysearch.org/developers/docs/api/tree/Head_Couple_Relationship_usecase
     */
    public function testHeadCoupleRelationship()
    {
        $factory = new FamilyTreeStateFactory();
        $this->collectionState($factory);

        $person1 = $this->createPerson('male')->get();
        $person2 = $this->createPerson('female')->get();

        $relation = $this->collectionState()->addSpouseRelationship($person1, $person2);
        $this->queueForDelete($relation);

        $this->assertAttributeEquals(HttpStatus::CREATED, "statusCode", $relation->getResponse(), $this->buildFailMessage(__METHOD__, $relation));

        $headers = $relation->head();
        $this->assertAttributeEquals(HttpStatus::OK, "statusCode", $headers->getResponse(), $this->buildFailMessage(__METHOD__, $headers));
    }

    /**
     * @link https://familysearch.org/developers/docs/api/tree/Update_Persons_of_a_Couple_Relationship_usecase
     */
    public function testUpdatePersonsOfCoupleRelationship()
    {
        $factory = new FamilyTreeStateFactory();
        $this->collectionState($factory);

        $person1 = $this->createPerson('male')->get();
        $person2 = $this->createPerson('female')->get();

        /* Create Relationship */
        /** @var $relation RelationshipState */
        $relation = $this->collectionState()->addSpouseRelationship($person1, $person2)->get();
        $this->queueForDelete($relation);

        $this->assertAttributeEquals(HttpStatus::OK, "statusCode", $relation->getResponse(), $this->buildFailMessage(__METHOD__."(addSpouse)", $relation));

        $person3 = $this->createPerson('female')->get();
        $relationship = $relation->getRelationship();
        $relationship->setPerson2($person3->getResourceReference());
        $relation = $relation->updateSelf($relationship);

        $this->assertAttributeEquals(HttpStatus::NO_CONTENT, "statusCode", $relation->getResponse(), $this->buildFailMessage(__METHOD__."(addSpouse)", $relation));
    }

    /**
     * @link https://familysearch.org/developers/docs/api/tree/Update_Couple_Relationship_Conclusion_usecase
     */
    public function testUpdateCoupleRelationshipConclusion()
    {
        $factory = new FamilyTreeStateFactory();
        $this->collectionState($factory);

        $person1 = $this->createPerson('male')->get();
        $person2 = $this->createPerson('female')->get();

        /* Create Relationship */
        /** @var $relation RelationshipState */
        $relation = $this->collectionState()->addSpouseRelationship($person1, $person2)->get();
        $this->queueForDelete($relation);
        $this->assertAttributeEquals(HttpStatus::OK, "statusCode", $relation->getResponse(), $this->buildFailMessage(__METHOD__."(addSpouse)", $relation));

        /* Create Marriage Fact */
        /** @var Fact $birth */
        $birth = $person1->getPerson()->getFactsOfType(FactType::BIRTH);
        $marriage = FactBuilder::marriage($birth->getDate()->getDateTime());
        $relation = $relation->addFact($marriage);
        $this->assertAttributeEquals(HttpStatus::NO_CONTENT, "statusCode", $relation->getResponse(), $this->buildFailMessage(__METHOD__."(createSource)", $relation));

        /* Alter Marriage Fact */
        $marriage->setAttribution(new Attribution(array(
                                                      "changeMessage" => $this->faker->sentence(6)
                                                  )));
        $currentDate = $marriage->getDate()->getDateTime();
        $newDate = new \DateTime($currentDate->format('Y-m-d')." +5 days");
        $marriage->setDate(new DateInfo(array(
                                            "original" => $newDate->format('F m, Y')
                                        )));
        $relation = $relation->addFact($marriage);
        $this->assertAttributeEquals(HttpStatus::NO_CONTENT, "statusCode", $relation->getResponse(), $this->buildFailMessage(__METHOD__."(createSource)", $relation));
    }

    /**
     * @link https://familysearch.org/developers/docs/api/tree/Update_Illegal_Couple_Relationship_usecase
     */
    public function testUpdateIllegalCoupleRelationship()
    {
        $factory = new FamilyTreeStateFactory();
        $this->collectionState($factory);

        $person1 = $this->createPerson('male')->get();
        $person2 = $this->createPerson('female')->get();

        /* CREATE */
        $relation = $this->collectionState()->addSpouseRelationship($person1, $person2);
        $this->queueForDelete($relation);
        $this->assertAttributeEquals(HttpStatus::CREATED, "statusCode", $relation->getResponse(), $this->buildFailMessage(__METHOD__, $relation));

        /* READ */
        $relation = $relation->get();
        $this->assertAttributeEquals(HttpStatus::OK, "statusCode", $relation->getResponse(), $this->buildFailMessage(__METHOD__, $relation));

        /** @var $entity Relationship */
        $entity = $relation->getRelationship();

        /* Person1 must be male. Person2 must be female. */
        $person3 = $this->createPerson('male');
        $entity->setPerson2($person3->getResourceReference());
        $updated = $relation->updateSelf($entity);
        $this->assertAttributeEquals(HttpStatus::BAD_REQUEST, "statusCode", $updated->getResponse(), $this->buildFailMessage(__METHOD__, $updated));

        $warnings = $updated->getHeader('warning');
        $this->assertNotEmpty($warnings, "No warnings found in headers.");
    }

    /**
     * testDeleteCoupleRelationship
     * @link https://familysearch.org/developers/docs/api/tree/Delete_Couple_Relationship_usecase
     * @see this::testCoupleRelationshipCRUD
     */

    /**
     * @link https://familysearch.org/developers/docs/api/tree/Delete_Couple_Relationship_Conclusion_usecase
     */
    public function testDeleteCoupleRelationshipConclusion()
    {
        $factory = new FamilyTreeStateFactory();
        $this->collectionState($factory);

        $person1 = $this->createPerson('male')->get();
        $person2 = $this->createPerson('female')->get();

        /* Create Relationship */
        /** @var $relation RelationshipState */
        $relation = $this->collectionState()->addSpouseRelationship($person1, $person2)->get();
        $this->queueForDelete($relation);

        $this->assertAttributeEquals(HttpStatus::OK, "statusCode", $relation->getResponse(), $this->buildFailMessage(__METHOD__."(addSpouse)", $relation));

        /* Create Marriage Fact */
        /** @var Fact $birth */
        $birth = $person1->getPerson()->getFactsofType(FactType::BIRTH);
        $marriage = FactBuilder::marriage($birth->getDate()->getDateTime());
        $relation = $relation->addFact($marriage);
        $this->assertAttributeEquals(HttpStatus::NO_CONTENT, "statusCode", $relation->getResponse(), $this->buildFailMessage(__METHOD__."(createSource)", $relation));

        $relation = $relation->get()->loadConclusions();

        /** @var Fact $birth */
        $marriage = $relation->getRelationship()->getFactsOfType(FactType::MARRIAGE);
        $deleted = $relation->deleteFact($marriage);
        $this->assertAttributeEquals(HttpStatus::NO_CONTENT, "statusCode", $deleted->getResponse(), $this->buildFailMessage(__METHOD__."(createSource)", $deleted));
    }

    /**
     * @link https://familysearch.org/developers/docs/api/tree/Restore_Couple_Relationship_usecase
     */
    public function testRestoreCoupleRelationship()
    {
        $factory = new FamilyTreeStateFactory();
        $this->collectionState($factory);

        $person1 = $this->createPerson('male')->get();
        $person2 = $this->createPerson('female')->get();

        /* CREATE */
        $relation = $this->collectionState()->addSpouseRelationship($person1, $person2);
        $this->queueForDelete($relation);

        $this->assertAttributeEquals(HttpStatus::CREATED, "statusCode", $relation->getResponse(), $this->buildFailMessage(__METHOD__, $relation));
        $relation = $relation->get();

        $relationship = new Relationship(array(
                                             "links" => array(
                                                 array(
                                                     "rel" => 'relationship',
                                                     'href' => "https://sandbox.familysearch.org/platform/tree/couple-relationships/" . $relation->getRelationship()->getId()
                                                 )
                                             )
                                         ));

        /* DELETE */
        $deleted = $relation->delete();
        $this->assertAttributeEquals(HttpStatus::NO_CONTENT, "statusCode", $deleted->getResponse(), "Check deleted success. Returned {$deleted->getResponse()->getStatusCode()}");

        $missing = $person1->readRelationship($relationship);
        $this->assertAttributeEquals(HttpStatus::GONE, "statusCode", $missing->getResponse(), "Read deleted person failed. Returned {$missing->getResponse()->getStatusCode()}");

        /* RESTORE */
        $restored = $missing->restore();
        $this->assertAttributeEquals(HttpStatus::NO_CONTENT, "statusCode", $restored->getResponse(), "Restore person failed. Returned {$restored->getResponse()->getStatusCode()}");
    }

    /**
     * @link https://familysearch.org/developers/docs/api/tree/Create_Couple_Relationship_usecase
     * @link https://familysearch.org/developers/docs/api/tree/Read_Couple_Relationship_usecase
     * @link https://familysearch.org/developers/docs/api/tree/Update_Persons_of_a_Couple_Relationship_usecase
     * @link https://familysearch.org/developers/docs/api/tree/Delete_Couple_Relationship_usecase
     */
    public function testCoupleRelationshipCRUD()
    {
        $factory = new FamilyTreeStateFactory();
        $this->collectionState($factory);

        $person1 = $this->createPerson('male')->get();
        $person2 = $this->createPerson('female')->get();

        /* CREATE */
        $relation = $this->collectionState()->addSpouseRelationship($person1, $person2);
        $this->assertAttributeEquals(HttpStatus::CREATED, "statusCode", $relation->getResponse(), $this->buildFailMessage(__METHOD__, $relation));

        /* READ */
        $relation = $relation->get();
        $this->assertAttributeEquals(HttpStatus::OK, "statusCode", $relation->getResponse(), $this->buildFailMessage(__METHOD__, $relation));

        /** @var $entity Relationship */
        $entity = $relation->getRelationship();
        $data_check = $entity->getPerson1() instanceof ResourceReference
            && $entity->getPerson2() instanceof ResourceReference;
        $this->assertTrue( $data_check );

        /* UPDATE */
        $person3 = $this->createPerson('female');
        $entity->setPerson2($person3->getResourceReference());
        $updated = $relation->updateSelf($entity);
        $this->assertAttributeEquals(HttpStatus::NO_CONTENT, "statusCode", $updated->getResponse(), $this->buildFailMessage(__METHOD__, $updated));

        /* DELETE */
        $deleted = $relation->delete();
        $this->assertAttributeEquals(HttpStatus::NO_CONTENT, "statusCode", $deleted->getResponse(), $this->buildFailMessage(__METHOD__, $deleted));
    }
}