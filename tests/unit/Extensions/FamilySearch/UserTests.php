<?php

namespace Gedcomx\Tests\Unit\Extensions\FamilySearch;

use Gedcomx\Tests\ApiTestCase;
use Gedcomx\Extensions\FamilySearch\Platform\Users\User;

class UserTests extends ApiTestCase
{
    public function testUserDeserialization()
    {
        $user = new User($this->loadJson('user.json'));

        $this->assertEquals('U-1', $user->getId());
        $this->assertEquals('John Doe', $user->getContactName());
        $this->assertEquals('john.doe@example.com', $user->getEmail());
        $this->assertEquals('en', $user->getPreferredLanguage());
    }

    public function testUserGettersAndSetters()
    {
        $user = new User();
        $user->setId('U-2');
        $user->setContactName('Jane Smith');
        $user->setEmail('jane.smith@example.com');
        $user->setPreferredLanguage('es');

        $this->assertEquals('U-2', $user->getId());
        $this->assertEquals('Jane Smith', $user->getContactName());
        $this->assertEquals('jane.smith@example.com', $user->getEmail());
        $this->assertEquals('es', $user->getPreferredLanguage());
    }

    public function testUserWithMinimalData()
    {
        $user = new User();
        $user->setId('U-3');

        $this->assertEquals('U-3', $user->getId());
        $this->assertNull($user->getContactName());
    }
}
