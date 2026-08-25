<?php

namespace Gedcomx\Tests\Unit;

use Gedcomx\Common\ResourceReference;
use Gedcomx\Conclusion\Date;
use Gedcomx\Extensions\FamilySearch\Platform\Ordinances\Ordinance;
use Gedcomx\Extensions\FamilySearch\Platform\Ordinances\OrdinanceActions;
use Gedcomx\Extensions\FamilySearch\Platform\Ordinances\OrdinanceParticipant;
use Gedcomx\Extensions\FamilySearch\Platform\Ordinances\OrdinanceReservation;
use Gedcomx\Extensions\FamilySearch\Platform\Ordinances\OrdinanceRollup;
use Gedcomx\Extensions\FamilySearch\Platform\Ordinances\OrdinanceSummary;
use Gedcomx\Extensions\FamilySearch\Platform\Ordinances\OrdinanceType;
use Gedcomx\Extensions\FamilySearch\Platform\Ordinances\OrdinanceStatus;
use Gedcomx\Extensions\FamilySearch\Platform\Ordinances\OrdinanceStatusReason;
use Gedcomx\Extensions\FamilySearch\Platform\Ordinances\OrdinanceRoleType;
use Gedcomx\Extensions\FamilySearch\Platform\Ordinances\OrdinanceSexType;
use Gedcomx\Extensions\FamilySearch\Platform\Ordinances\OrdinanceReservationClaimType;
use Gedcomx\Extensions\FamilySearch\Platform\Ordinances\OrdinanceReservationAssigneeType;
use Gedcomx\Extensions\FamilySearch\Platform\Ordinances\OrdinanceRollupStatus;
use Gedcomx\Tests\ApiTestCase;

class OrdinancesTest extends ApiTestCase
{
    public function testOrdinanceTypeEnum()
    {
        $this->assertEquals('http://churchofjesuschrist.org/Baptism', OrdinanceType::BAPTISM);
        $this->assertEquals('http://churchofjesuschrist.org/Confirmation', OrdinanceType::CONFIRMATION);
        $this->assertEquals('http://churchofjesuschrist.org/Initiatory', OrdinanceType::INITIATORY);
        $this->assertEquals('http://churchofjesuschrist.org/Endowment', OrdinanceType::ENDOWMENT);
        $this->assertEquals('http://churchofjesuschrist.org/SealingToSpouse', OrdinanceType::SEALING_TO_SPOUSE);
        $this->assertEquals('http://churchofjesuschrist.org/SealingChildToParents', OrdinanceType::SEALING_CHILD_TO_PARENTS);
    }

    public function testOrdinanceStatusEnum()
    {
        $this->assertEquals('http://familysearch.org/v1/BornInCovenant', OrdinanceStatus::BORN_IN_COVENANT);
        $this->assertEquals('http://familysearch.org/v1/Completed', OrdinanceStatus::COMPLETED);
        $this->assertEquals('http://familysearch.org/v1/Ready', OrdinanceStatus::READY);
        $this->assertEquals('http://familysearch.org/v1/NotReady', OrdinanceStatus::NOT_READY);
    }

    public function testOrdinanceStatusReasonEnum()
    {
        $this->assertEquals('http://familysearch.org/v1/BornInCovenant', OrdinanceStatusReason::BORN_IN_COVENANT);
        $this->assertEquals('http://familysearch.org/v1/DiedBeforeAgeEight', OrdinanceStatusReason::DIED_BEFORE_AGE_EIGHT);
        $this->assertEquals('http://familysearch.org/v1/NotDeadAtLeastOneYear', OrdinanceStatusReason::NOT_DEAD_AT_LEAST_ONE_YEAR);
    }

    public function testOrdinanceActionsConstruction()
    {
        $actions = new OrdinanceActions();
        $actions->setReservable(true);
        $actions->setShareable(true);
        $actions->setPrintable(false);

        $this->assertTrue($actions->isReservable());
        $this->assertTrue($actions->isShareable());
        $this->assertFalse($actions->isPrintable());
    }

    public function testOrdinanceParticipantConstruction()
    {
        $participant = new OrdinanceParticipant();
        $participant->setRoleType(OrdinanceRoleType::PARENT);
        $participant->setSexType(OrdinanceSexType::MALE);
        $participant->setFullName('John Doe');

        $ref = new ResourceReference();
        $ref->setResource('https://familysearch.org/platform/persons/PPPP-PPP');
        $participant->setParticipant($ref);

        $this->assertEquals(OrdinanceRoleType::PARENT, $participant->getRoleType());
        $this->assertEquals('Parent', $participant->getKnownRoleType());
        $this->assertEquals(OrdinanceSexType::MALE, $participant->getSexType());
        $this->assertEquals('Male', $participant->getKnownSexType());
        $this->assertEquals('John Doe', $participant->getFullName());
    }

    public function testOrdinanceReservationConstruction()
    {
        $reservation = new OrdinanceReservation();

        $owner = new ResourceReference();
        $owner->setResource('https://familysearch.org/platform/users/1234');
        $reservation->setOwner($owner);

        $reservation->setReserveDate(new \DateTime('2024-01-15'));
        $reservation->setUpdateDate(new \DateTime('2024-01-20'));
        $reservation->setExpirationDate(new \DateTime('2024-07-15'));
        $reservation->setClaimType(OrdinanceReservationClaimType::DEFAULT_TYPE);
        $reservation->setAssigneeType(OrdinanceReservationAssigneeType::PERSONAL);

        $this->assertNotNull($reservation->getOwner());
        $this->assertInstanceOf(\DateTime::class, $reservation->getReserveDate());
        $this->assertEquals('Default', $reservation->getKnownClaimType());
        $this->assertEquals('Personal', $reservation->getKnownAssigneeType());
    }

    public function testOrdinanceSummaryConstruction()
    {
        $summary = new OrdinanceSummary();
        $summary->setNotSharedReservationCount(5);
        $summary->setNotSharedReservationLimit(10);
        $summary->setSharedReservationCount(3);

        $this->assertEquals(5, $summary->getNotSharedReservationCount());
        $this->assertEquals(10, $summary->getNotSharedReservationLimit());
        $this->assertEquals(3, $summary->getSharedReservationCount());
    }

    public function testOrdinanceRollupConstruction()
    {
        $rollup = new OrdinanceRollup();
        $rollup->setType(OrdinanceType::BAPTISM);
        $rollup->setRollupStatus(OrdinanceRollupStatus::ROLLED_UP_COMPLETED);

        $this->assertEquals(OrdinanceType::BAPTISM, $rollup->getType());
        $this->assertEquals('Baptism', $rollup->getKnownType());
        $this->assertEquals(OrdinanceRollupStatus::ROLLED_UP_COMPLETED, $rollup->getRollupStatus());
        $this->assertEquals('RolledUpCompleted', $rollup->getKnownRollupStatus());
    }

    public function testOrdinanceConstruction()
    {
        $ordinance = new Ordinance();
        $ordinance->setType(OrdinanceType::BAPTISM);
        $ordinance->setStatus(OrdinanceStatus::COMPLETED);
        $ordinance->setStatusReasons([
            OrdinanceStatusReason::BORN_IN_COVENANT,
            OrdinanceStatusReason::DIED_BEFORE_AGE_EIGHT
        ]);

        $person = new ResourceReference();
        $person->setResource('https://familysearch.org/platform/persons/PPPP-PPP');
        $ordinance->setPerson($person);

        $ordinance->setSexType(OrdinanceSexType::MALE);
        $ordinance->setFullName('John Smith');
        $ordinance->setTempleCode('SLAKE');

        $date = new Date();
        $date->setOriginal('15 January 2020');
        $ordinance->setCompleteDate($date);

        $this->assertEquals(OrdinanceType::BAPTISM, $ordinance->getType());
        $this->assertEquals('Baptism', $ordinance->getKnownType());
        $this->assertEquals(OrdinanceStatus::COMPLETED, $ordinance->getStatus());
        $this->assertEquals('Completed', $ordinance->getKnownStatus());
        $this->assertCount(2, $ordinance->getStatusReasons());
        $this->assertEquals('John Smith', $ordinance->getFullName());
        $this->assertEquals('SLAKE', $ordinance->getTempleCode());
    }

    public function testOrdinanceWithParticipants()
    {
        $ordinance = new Ordinance();
        $ordinance->setType(OrdinanceType::SEALING_TO_SPOUSE);

        $participant1 = new OrdinanceParticipant();
        $participant1->setRoleType(OrdinanceRoleType::SPOUSE);
        $participant1->setFullName('Jane Doe');

        $participant2 = new OrdinanceParticipant();
        $participant2->setRoleType(OrdinanceRoleType::SPOUSE);
        $participant2->setFullName('John Doe');

        $ordinance->setParticipants([$participant1, $participant2]);

        $this->assertCount(2, $ordinance->getParticipants());
        $this->assertEquals('Jane Doe', $ordinance->getParticipants()[0]->getFullName());
    }

    public function testOrdinanceWithReservations()
    {
        $ordinance = new Ordinance();

        $reservation = new OrdinanceReservation();
        $owner = new ResourceReference();
        $owner->setResource('https://familysearch.org/platform/users/1234');
        $reservation->setOwner($owner);

        $ordinance->setReservation($reservation);
        $ordinance->setSecondaryReservation($reservation);
        $ordinance->setCallerReservation($reservation);

        $this->assertNotNull($ordinance->getReservation());
        $this->assertNotNull($ordinance->getSecondaryReservation());
        $this->assertNotNull($ordinance->getCallerReservation());
    }

    public function testOrdinanceActionsJsonRoundTrip()
    {
        $actions = new OrdinanceActions([
            'reservable' => true,
            'unReservable' => false,
            'shareable' => true,
            'unShareable' => false,
            'printable' => true
        ]);

        $json = $actions->toJson();
        $this->assertStringContainsString('reservable', $json);

        $decoded = json_decode($json, true);
        $actions2 = new OrdinanceActions($decoded);

        $this->assertTrue($actions2->isReservable());
        $this->assertTrue($actions2->isShareable());
        $this->assertTrue($actions2->isPrintable());
    }

    public function testOrdinanceParticipantJsonRoundTrip()
    {
        $participant = new OrdinanceParticipant([
            'roleType' => OrdinanceRoleType::PARENT,
            'sexType' => OrdinanceSexType::FEMALE,
            'fullName' => 'Mary Smith'
        ]);

        $json = $participant->toJson();
        $this->assertStringContainsString('Mary Smith', $json);

        $decoded = json_decode($json, true);
        $participant2 = new OrdinanceParticipant($decoded);

        $this->assertEquals('Mary Smith', $participant2->getFullName());
        $this->assertEquals('Parent', $participant2->getKnownRoleType());
    }

    public function testOrdinanceJsonRoundTrip()
    {
        $ordinance = new Ordinance([
            'type' => OrdinanceType::BAPTISM,
            'status' => OrdinanceStatus::COMPLETED,
            'fullName' => 'Test Person',
            'templeCode' => 'PROVO'
        ]);

        $json = $ordinance->toJson();
        $this->assertStringContainsString('Test Person', $json);
        $this->assertStringContainsString('PROVO', $json);

        $decoded = json_decode($json, true);
        $ordinance2 = new Ordinance($decoded);

        $this->assertEquals('Test Person', $ordinance2->getFullName());
        $this->assertEquals('PROVO', $ordinance2->getTempleCode());
        $this->assertEquals('Baptism', $ordinance2->getKnownType());
    }

    public function testOrdinanceRollupJsonRoundTrip()
    {
        $rollup = new OrdinanceRollup([
            'type' => OrdinanceType::ENDOWMENT,
            'rollupStatus' => OrdinanceRollupStatus::ROLLED_UP_READY
        ]);

        $json = $rollup->toJson();
        $decoded = json_decode($json, true);
        $rollup2 = new OrdinanceRollup($decoded);

        $this->assertEquals('Endowment', $rollup2->getKnownType());
        $this->assertEquals('RolledUpReady', $rollup2->getKnownRollupStatus());
    }
}
