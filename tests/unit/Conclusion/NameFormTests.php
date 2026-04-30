<?php

namespace Gedcomx\Tests\Unit\Conclusion;

use Gedcomx\Conclusion\NameForm;
use Gedcomx\Tests\ApiTestCase;

class NameFormTests extends ApiTestCase
{
    public function testNameFormDeserialization()
    {
        $json = $this->loadJson('name-form.json');
        $nameForm = new NameForm($json);

        $this->assertEquals('en', $nameForm->getLang());
        $this->assertEquals('John Smith', $nameForm->getFullText());
        $this->assertCount(2, $nameForm->getParts());
    }

    public function testNameFormGettersAndSetters()
    {
        $nameForm = new NameForm();
        $nameForm->setLang('fr');
        $nameForm->setFullText('Jean Dupont');

        $this->assertEquals('fr', $nameForm->getLang());
        $this->assertEquals('Jean Dupont', $nameForm->getFullText());
    }

    public function testNameFormWithoutParts()
    {
        $nameForm = new NameForm([
            'fullText' => 'Unknown Name'
        ]);

        $this->assertEquals('Unknown Name', $nameForm->getFullText());
        $this->assertEmpty($nameForm->getParts());
    }
}
