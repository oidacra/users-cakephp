<?php
namespace Acciona\Users\Test\TestCase\Model\Table;

use Acciona\Users\Model\Table\PasswordTokensTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;
use phpmock\MockBuilder;

/**
 * @property \Acciona\Users\Model\Table\PasswordTokensTable $PasswordTokens
 */
class PasswordTokensTableTest extends TestCase
{

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'plugin.acciona/users.password_tokens',
        'plugin.acciona/users.users',
        'plugin.acciona/users.roles',
        'plugin.acciona/users.users_roles'
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $config = TableRegistry::exists('PasswordTokens') ? [] : ['className' => 'Acciona\Users\Model\Table\PasswordTokensTable'];
        $this->PasswordTokens = TableRegistry::get('PasswordTokens', $config);

        $builder = new MockBuilder();
        $builder->setNamespace('Acciona\Users\Model\Table')
            ->setName("time")
            ->setFunction(
                function () {
                    return 1;
                }
            );
        $this->mock = $builder->build();
        $this->mock->enable();
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->PasswordTokens);
        $this->mock->disable();

        parent::tearDown();
    }

    /**
     * Test initialize method
     *
     * @return void
     */
    public function testCreateAndSaveToken()
    {
        $token = $this->PasswordTokens->generateAndSaveToken(2, 300);
        // the record must exists
        $record = $this->PasswordTokens->find()->where(['PasswordTokens.token' => $token]);

        $this->assertFalse($record->isEmpty());

        $data = $record->first();
        $this->assertEquals(2, $data->user_id);
        $this->assertEquals($token, $data->token);
        $this->assertEquals(301, $data->expiration);
    }
}
