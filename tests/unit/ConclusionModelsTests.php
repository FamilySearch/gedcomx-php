<?php

namespace Gedcomx\Tests\Unit;

use Gedcomx\Conclusion\Fact;
use Gedcomx\Conclusion\Gender;
use Gedcomx\Conclusion\Name;
use Gedcomx\Conclusion\NameForm;
use Gedcomx\Conclusion\NamePart;
use Gedcomx\Conclusion\Person;
use Gedcomx\Conclusion\Relationship;
use Gedcomx\Conclusion\DateInfo;
use Gedcomx\Conclusion\PlaceReference;
use Gedcomx\Conclusion\Document;
use Gedcomx\Conclusion\Event;
use Gedcomx\Tests\ApiTestCase;
use Gedcomx\Common\ResourceReference;

/**
 * Comprehensive tests for GEDCOM X core conclusion models
 * Tests construction, getters/setters, and serialization/deserialization
 */
class ConclusionModelsTests extends ApiTestCase
{
    public function testPersonConstruction()
    {
        $person = new Person();
        $person->setId('P-1');
        $person->setLiving(false);

        $this->assertEquals('P-1', $person->getId());
    }

    public function testPersonWithGender()
    {
        $person = new Person();
        $gender = new Gender();
        $gender->setType('http://gedcomx.org/Male');
        $person->setGender($gender);

        $this->assertNotNull($person->getGender());
        $this->assertEquals('http://gedcomx.org/Male', $person->getGender()->getType());
    }

    public function testPersonWithName()
    {
        $person = new Person();

        $namePart = new NamePart();
        $namePart->setValue('John');
        $namePart->setType('http://gedcomx.org/Given');

        $nameForm = new NameForm();
        $nameForm->setFullText('John Smith');
        $nameForm->setParts([$namePart]);

        $name = new Name();
        $name->setNameForms([$nameForm]);

        $person->setNames([$name]);

        $this->assertCount(1, $person->getNames());
        $this->assertEquals('John Smith', $person->getNames()[0]->getNameForms()[0]->getFullText());
    }

    public function testPersonWithFacts()
    {
        $person = new Person();

        $birthFact = new Fact();
        $birthFact->setType('http://gedcomx.org/Birth');
        $birthFact->setDate(new DateInfo(['original' => '1 January 1900']));
        $birthFact->setPlace(new PlaceReference(['original' => 'New York, USA']));

        $person->setFacts([$birthFact]);

        $this->assertCount(1, $person->getFacts());
        $this->assertEquals('http://gedcomx.org/Birth', $person->getFacts()[0]->getType());
        $this->assertEquals('1 January 1900', $person->getFacts()[0]->getDate()->getOriginal());
    }

    public function testFactConstruction()
    {
        $fact = new Fact([
            'type' => 'http://gedcomx.org/Birth',
            'date' => ['original' => '1900'],
            'place' => ['original' => 'London']
        ]);

        $this->assertEquals('http://gedcomx.org/Birth', $fact->getType());
        $this->assertEquals('1900', $fact->getDate()->getOriginal());
        $this->assertEquals('London', $fact->getPlace()->getOriginal());
    }

    public function testRelationshipConstruction()
    {
        $relationship = new Relationship();
        $relationship->setType('http://gedcomx.org/Couple');

        $person1 = new ResourceReference();
        $person1->setResource('#P-1');
        $relationship->setPerson1($person1);

        $person2 = new ResourceReference();
        $person2->setResource('#P-2');
        $relationship->setPerson2($person2);

        $this->assertEquals('http://gedcomx.org/Couple', $relationship->getType());
        $this->assertEquals('#P-1', $relationship->getPerson1()->getResource());
        $this->assertEquals('#P-2', $relationship->getPerson2()->getResource());
    }

    public function testRelationshipWithFacts()
    {
        $relationship = new Relationship();

        $marriageFact = new Fact();
        $marriageFact->setType('http://gedcomx.org/Marriage');
        $marriageFact->setDate(new DateInfo(['original' => '1 June 1920']));

        $relationship->setFacts([$marriageFact]);

        $this->assertCount(1, $relationship->getFacts());
        $this->assertEquals('http://gedcomx.org/Marriage', $relationship->getFacts()[0]->getType());
    }

    public function testDateInfo()
    {
        $date = new DateInfo();
        $date->setOriginal('circa 1900');
        $date->setFormal('+1900');

        $this->assertEquals('circa 1900', $date->getOriginal());
        $this->assertEquals('+1900', $date->getFormal());
    }

    public function testPlaceReference()
    {
        $place = new PlaceReference();
        $place->setOriginal('Springfield, Illinois, USA');

        $this->assertEquals('Springfield, Illinois, USA', $place->getOriginal());
    }

    public function testDocumentConstruction()
    {
        $document = new Document();
        $document->setId('D-1');
        $document->setType('http://gedcomx.org/Analysis');
        $document->setText('This is a document');

        $this->assertEquals('D-1', $document->getId());
        $this->assertEquals('http://gedcomx.org/Analysis', $document->getType());
        $this->assertEquals('This is a document', $document->getText());
    }

    public function testEventConstruction()
    {
        $event = new Event();
        $event->setId('E-1');
        $event->setType('http://gedcomx.org/Birth');
        $event->setDate(new DateInfo(['original' => '1 January 1900']));
        $event->setPlace(new PlaceReference(['original' => 'Boston, Massachusetts']));

        $this->assertEquals('E-1', $event->getId());
        $this->assertEquals('http://gedcomx.org/Birth', $event->getType());
        $this->assertEquals('1 January 1900', $event->getDate()->getOriginal());
        $this->assertEquals('Boston, Massachusetts', $event->getPlace()->getOriginal());
    }

    public function testNamePartTypes()
    {
        $givenPart = new NamePart();
        $givenPart->setValue('John');
        $givenPart->setType('http://gedcomx.org/Given');

        $surnamePart = new NamePart();
        $surnamePart->setValue('Smith');
        $surnamePart->setType('http://gedcomx.org/Surname');

        $this->assertEquals('John', $givenPart->getValue());
        $this->assertEquals('http://gedcomx.org/Given', $givenPart->getType());
        $this->assertEquals('Smith', $surnamePart->getValue());
        $this->assertEquals('http://gedcomx.org/Surname', $surnamePart->getType());
    }

    public function testPersonJsonRoundTrip()
    {
        $person = new Person([
            'id' => 'P-1',
            'living' => false,
            'gender' => [
                'type' => 'http://gedcomx.org/Male'
            ],
            'names' => [
                [
                    'nameForms' => [
                        [
                            'fullText' => 'John Smith'
                        ]
                    ]
                ]
            ]
        ]);

        $json = $person->toJson();
        $this->assertStringContainsString('P-1', $json);
        $this->assertStringContainsString('John Smith', $json);

        // Test round-trip
        $decoded = json_decode($json, true);
        $person2 = new Person($decoded);
        $this->assertEquals('P-1', $person2->getId());
    }

    public function testRelationshipJsonRoundTrip()
    {
        $relationship = new Relationship([
            'type' => 'http://gedcomx.org/ParentChild',
            'person1' => ['resource' => '#P-1'],
            'person2' => ['resource' => '#P-2']
        ]);

        $json = $relationship->toJson();
        $this->assertStringContainsString('ParentChild', $json);
        $this->assertStringContainsString('#P-1', $json);

        // Test round-trip
        $decoded = json_decode($json, true);
        $relationship2 = new Relationship($decoded);
        $this->assertEquals('http://gedcomx.org/ParentChild', $relationship2->getType());
    }
}
