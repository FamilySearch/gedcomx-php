<?php

namespace Gedcomx\Tests\Unit;

use Gedcomx\Tests\ApiTestCase;
use Gedcomx\Conclusion\Person;

class PersonTests extends ApiTestCase
{
    
    public function testPerson()
    {
        $person = new Person($this->loadJson('person.json'));
        $this->assertEquals($person->getId(), 'PPPJ-MYZ');
        $this->assertEquals(count($person->getFacts()), 2);
        $this->assertEquals(count($person->getNames()), 1);
    }

}
