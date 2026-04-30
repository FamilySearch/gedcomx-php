<?php

namespace Gedcomx\Tests\Unit;

use Gedcomx\Conclusion\PlaceDescription;
use Gedcomx\Conclusion\EventRole;
use Gedcomx\Conclusion\Identifier;
use Gedcomx\Conclusion\Subject;
use Gedcomx\Tests\ApiTestCase;

/**
 * Tests for additional GEDCOM X conclusion models not covered in ConclusionModelsTests
 */
class AdditionalConclusionModelsTests extends ApiTestCase
{
    public function testPlaceDescriptionConstruction()
    {
        $place = new PlaceDescription();
        $place->setId('PL-1');

        $this->assertEquals('PL-1', $place->getId());
    }

    public function testPlaceDescriptionWithNames()
    {
        $place = new PlaceDescription([
            'id' => 'PL-1',
            'names' => [
                ['value' => 'Springfield, Sangamon, Illinois, United States']
            ]
        ]);

        $this->assertEquals('PL-1', $place->getId());
        $this->assertNotEmpty($place->getNames());
    }

    public function testEventRoleConstruction()
    {
        $role = new EventRole();
        $role->setType('http://gedcomx.org/Witness');

        $this->assertEquals('http://gedcomx.org/Witness', $role->getType());
    }

    public function testEventRoleWithPerson()
    {
        $role = new EventRole([
            'type' => 'http://gedcomx.org/Principal',
            'person' => [
                'resource' => '#P-1'
            ]
        ]);

        $this->assertEquals('http://gedcomx.org/Principal', $role->getType());
        $this->assertNotNull($role->getPerson());
    }

    public function testIdentifierConstruction()
    {
        $identifier = new Identifier();
        $identifier->setValue('123-456-789');
        $identifier->setType('http://gedcomx.org/Primary');

        $this->assertEquals('123-456-789', $identifier->getValue());
        $this->assertEquals('http://gedcomx.org/Primary', $identifier->getType());
    }

    public function testIdentifierFromArray()
    {
        $identifier = new Identifier([
            'value' => 'PAL:12345',
            'type' => 'http://gedcomx.org/Persistent'
        ]);

        $this->assertEquals('PAL:12345', $identifier->getValue());
        $this->assertEquals('http://gedcomx.org/Persistent', $identifier->getType());
    }

    public function testSubjectConstruction()
    {
        $subject = new Subject();
        $subject->setId('SUBJ-1');

        $this->assertEquals('SUBJ-1', $subject->getId());
    }

    public function testSubjectWithEvidence()
    {
        $subject = new Subject([
            'id' => 'SUBJ-1',
            'evidence' => [
                [
                    'resource' => '#E-1'
                ]
            ]
        ]);

        $this->assertEquals('SUBJ-1', $subject->getId());
        $this->assertNotEmpty($subject->getEvidence());
    }

    public function testPlaceDescriptionJsonRoundTrip()
    {
        $place = new PlaceDescription([
            'id' => 'PL-TEST',
            'names' => [
                ['value' => 'Test Location']
            ]
        ]);

        $json = $place->toJson();
        $this->assertStringContainsString('PL-TEST', $json);

        $decoded = json_decode($json, true);
        $place2 = new PlaceDescription($decoded);
        $this->assertEquals('PL-TEST', $place2->getId());
    }
}
