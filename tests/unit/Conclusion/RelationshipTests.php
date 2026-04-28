<?php

namespace Gedcomx\Tests\Unit\Conclusion;

use Gedcomx\Tests\ApiTestCase;
use Gedcomx\Conclusion\Relationship;

class RelationshipTests extends ApiTestCase
{
    public function testRelationshipDeserialization()
    {
        $relationship = new Relationship($this->loadJson('relationship.json'));

        $this->assertEquals('http://gedcomx.org/Couple', $relationship->getType());

        $person1 = $relationship->getPerson1();
        $this->assertNotNull($person1);
        $this->assertEquals('person1', $person1->getResourceId());

        $person2 = $relationship->getPerson2();
        $this->assertNotNull($person2);
        $this->assertEquals('person2', $person2->getResourceId());

        $facts = $relationship->getFacts();
        $this->assertCount(1, $facts);
        $this->assertEquals('http://gedcomx.org/Marriage', $facts[0]->getType());
    }

    public function testRelationshipGettersAndSetters()
    {
        $relationship = new Relationship();
        $relationship->setType('http://gedcomx.org/ParentChild');

        $this->assertEquals('http://gedcomx.org/ParentChild', $relationship->getType());
    }

    public function testRelationshipWithoutFacts()
    {
        $relationship = new Relationship();
        $relationship->setType('http://gedcomx.org/Couple');

        $this->assertNull($relationship->getFacts());
    }
}
