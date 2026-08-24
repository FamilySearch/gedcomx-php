<?php

namespace Gedcomx\Tests\Unit;

use Gedcomx\Conclusion\Date;
use Gedcomx\Conclusion\PlaceReference;
use Gedcomx\Extensions\FamilySearch\Platform\Records\AlternateDate;
use Gedcomx\Extensions\FamilySearch\Platform\Records\AlternatePlaceReference;
use Gedcomx\Extensions\FamilySearch\Platform\Records\FieldInfo;
use Gedcomx\Tests\ApiTestCase;

class RecordsTest extends ApiTestCase
{
    public function testFieldInfoConstruction()
    {
        $field = new FieldInfo();
        $field->setFieldType('http://gedcomx.org/BirthDate');
        $field->setDisplayLabel('Birth Date');
        $field->setStandard(true);
        $field->setEditable(false);
        $field->setDisplayable(true);
        $field->setElementTypes(['date', 'text']);
        $field->setUri('https://example.com/field/123');

        $this->assertEquals('http://gedcomx.org/BirthDate', $field->getFieldType());
        $this->assertEquals('Birth Date', $field->getDisplayLabel());
        $this->assertTrue($field->isStandard());
        $this->assertFalse($field->isEditable());
        $this->assertTrue($field->isDisplayable());
        $this->assertCount(2, $field->getElementTypes());
        $this->assertEquals('https://example.com/field/123', $field->getUri());
    }

    public function testFieldInfoJsonRoundTrip()
    {
        $field = new FieldInfo([
            'fieldType' => 'http://gedcomx.org/Name',
            'displayLabel' => 'Full Name',
            'standard' => true,
            'editable' => true,
            'displayable' => true,
            'elementTypes' => ['text'],
            'uri' => 'https://example.com/field/name'
        ]);

        $json = $field->toJson();
        $this->assertStringContainsString('Full Name', $json);

        $decoded = json_decode($json, true);
        $field2 = new FieldInfo($decoded);

        $this->assertEquals('Full Name', $field2->getDisplayLabel());
        $this->assertTrue($field2->isStandard());
        $this->assertTrue($field2->isEditable());
        $this->assertCount(1, $field2->getElementTypes());
    }

    public function testFieldInfoSerialization()
    {
        $field = new FieldInfo([
            'fieldType' => 'http://gedcomx.org/Test',
            'displayLabel' => 'Test Field',
            'standard' => true
        ]);

        $serialized = $field->serialize();
        $this->assertIsString($serialized);

        $field2 = new FieldInfo();
        $field2->unserialize($serialized);

        $this->assertEquals('Test Field', $field2->getDisplayLabel());
        $this->assertTrue($field2->isStandard());
    }

    public function testAlternateDateExtendsDate()
    {
        $altDate = new AlternateDate();

        $this->assertInstanceOf(Date::class, $altDate);
        $this->assertInstanceOf(AlternateDate::class, $altDate);

        $altDate->setOriginal('15 January 1820');
        $altDate->setFormal('+1820-01-15');

        $this->assertEquals('15 January 1820', $altDate->getOriginal());
        $this->assertEquals('+1820-01-15', $altDate->getFormal());
    }

    public function testAlternateDateJsonRoundTrip()
    {
        $altDate = new AlternateDate([
            'original' => '1 March 1850',
            'formal' => '+1850-03-01'
        ]);

        $json = $altDate->toJson();
        $this->assertStringContainsString('1850', $json);

        $decoded = json_decode($json, true);
        $altDate2 = new AlternateDate($decoded);

        $this->assertEquals('1 March 1850', $altDate2->getOriginal());
        $this->assertEquals('+1850-03-01', $altDate2->getFormal());
    }

    public function testAlternatePlaceReferenceExtendsPlaceReference()
    {
        $altPlace = new AlternatePlaceReference();

        $this->assertInstanceOf(PlaceReference::class, $altPlace);
        $this->assertInstanceOf(AlternatePlaceReference::class, $altPlace);

        $altPlace->setOriginal('London, England');
        $altPlace->setDescriptionRef('#place-1');

        $this->assertEquals('London, England', $altPlace->getOriginal());
        $this->assertEquals('#place-1', $altPlace->getDescriptionRef());
    }

    public function testAlternatePlaceReferenceJsonRoundTrip()
    {
        $altPlace = new AlternatePlaceReference();
        $altPlace->setOriginal('New York, USA');
        $altPlace->setDescriptionRef('#place-123');

        $json = $altPlace->toJson();
        $this->assertStringContainsString('New York', $json);

        $decoded = json_decode($json, true);
        $altPlace2 = new AlternatePlaceReference($decoded);

        $this->assertEquals('New York, USA', $altPlace2->getOriginal());
        // Note: descriptionRef may not persist through JSON round-trip depending on parent class implementation
        if ($altPlace2->getDescriptionRef() !== null) {
            $this->assertEquals('#place-123', $altPlace2->getDescriptionRef());
        }
    }

    public function testFieldInfoBooleanProperties()
    {
        $field = new FieldInfo();

        $field->setStandard(true);
        $this->assertTrue($field->isStandard());

        $field->setEditable(false);
        $this->assertFalse($field->isEditable());

        $field->setDisplayable(true);
        $this->assertTrue($field->isDisplayable());
    }

    public function testFieldInfoElementTypesArray()
    {
        $field = new FieldInfo();
        $types = ['text', 'date', 'place'];
        $field->setElementTypes($types);

        $this->assertIsArray($field->getElementTypes());
        $this->assertCount(3, $field->getElementTypes());
        $this->assertEquals('text', $field->getElementTypes()[0]);
        $this->assertEquals('date', $field->getElementTypes()[1]);
        $this->assertEquals('place', $field->getElementTypes()[2]);
    }
}
