<?php

namespace Gedcomx\Tests\Unit\Extensions\FamilySearch;

use Gedcomx\Extensions\FamilySearch\Platform\Tree\ChangeInfo;
use Gedcomx\Tests\ApiTestCase;

class ChangeInfoTests extends ApiTestCase
{
    public function testChangeInfoDeserialization()
    {
        $json = $this->loadJson('change-info.json');
        $changeInfo = new ChangeInfo($json);

        $this->assertEquals('Person', $changeInfo->getObjectType());
        $this->assertEquals('Create', $changeInfo->getOperation());
        $this->assertEquals('Initial record creation', $changeInfo->getReason());
        $this->assertEquals('Name', $changeInfo->getObjectModifier());
    }

    public function testChangeInfoGettersAndSetters()
    {
        $changeInfo = new ChangeInfo();
        $changeInfo->setObjectType('Relationship');
        $changeInfo->setOperation('Update');
        $changeInfo->setReason('Corrected date');

        $this->assertEquals('Relationship', $changeInfo->getObjectType());
        $this->assertEquals('Update', $changeInfo->getOperation());
        $this->assertEquals('Corrected date', $changeInfo->getReason());
    }

    public function testChangeInfoWithoutReason()
    {
        $changeInfo = new ChangeInfo([
            'objectType' => 'Fact',
            'operation' => 'Delete'
        ]);

        $this->assertEquals('Fact', $changeInfo->getObjectType());
        $this->assertEquals('Delete', $changeInfo->getOperation());
        $this->assertNull($changeInfo->getReason());
    }
}
