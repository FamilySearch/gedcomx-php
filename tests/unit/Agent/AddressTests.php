<?php

namespace Gedcomx\Tests\Unit\Agent;

use Gedcomx\Tests\ApiTestCase;
use Gedcomx\Agent\Address;

class AddressTests extends ApiTestCase
{
    public function testAddressDeserialization()
    {
        $address = new Address($this->loadJson('address.json'));

        $this->assertEquals("123 Main Street\nSpringfield, IL 62701\nUSA", $address->getValue());
        $this->assertEquals('Springfield', $address->getCity());
        $this->assertEquals('Illinois', $address->getStateOrProvince());
        $this->assertEquals('62701', $address->getPostalCode());
        $this->assertEquals('USA', $address->getCountry());
    }

    public function testAddressGettersAndSetters()
    {
        $address = new Address();
        $address->setValue('456 Oak Avenue');
        $address->setCity('Boston');
        $address->setStateOrProvince('Massachusetts');
        $address->setPostalCode('02101');
        $address->setCountry('USA');

        $this->assertEquals('456 Oak Avenue', $address->getValue());
        $this->assertEquals('Boston', $address->getCity());
        $this->assertEquals('Massachusetts', $address->getStateOrProvince());
        $this->assertEquals('02101', $address->getPostalCode());
        $this->assertEquals('USA', $address->getCountry());
    }

    public function testAddressWithMinimalData()
    {
        $address = new Address();
        $address->setValue('123 Main St');

        $this->assertEquals('123 Main St', $address->getValue());
        $this->assertNull($address->getCity());
    }
}
