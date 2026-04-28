<?php

namespace Gedcomx\Tests\Unit\Agent;

use Gedcomx\Agent\OnlineAccount;
use Gedcomx\Tests\ApiTestCase;

class OnlineAccountTests extends ApiTestCase
{
    public function testOnlineAccountDeserialization()
    {
        $json = $this->loadJson('online-account.json');
        $account = new OnlineAccount($json);

        $this->assertEquals('john_smith_1900', $account->getAccountName());
        $this->assertNotNull($account->getServiceHomepage());
    }

    public function testOnlineAccountGettersAndSetters()
    {
        $account = new OnlineAccount();
        $account->setAccountName('user_12345');

        $this->assertEquals('user_12345', $account->getAccountName());
    }

    public function testOnlineAccountWithoutServiceHomepage()
    {
        $account = new OnlineAccount([
            'accountName' => 'test_user'
        ]);

        $this->assertEquals('test_user', $account->getAccountName());
        $this->assertNull($account->getServiceHomepage());
    }
}
