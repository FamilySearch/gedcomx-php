<?php

namespace Gedcomx\Tests\Unit\Conclusion;

use Gedcomx\Tests\ApiTestCase;
use Gedcomx\Conclusion\Gender;

class GenderTests extends ApiTestCase
{
    public function testGenderDeserialization()
    {
        $gender = new Gender($this->loadJson('gender.json'));

        $this->assertEquals('http://gedcomx.org/Male', $gender->getType());
    }

    public function testGenderGettersAndSetters()
    {
        $gender = new Gender();
        $gender->setType('http://gedcomx.org/Female');

        $this->assertEquals('http://gedcomx.org/Female', $gender->getType());
    }

    public function testGenderWithNullType()
    {
        $gender = new Gender();

        $this->assertNull($gender->getType());
    }
}
