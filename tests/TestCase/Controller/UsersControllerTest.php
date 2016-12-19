<?php
namespace Acciona\Users\Test\TestCase\Controller;

use Acciona\Users\Controller\UsersController;
use Cake\TestSuite\IntegrationTestCase;
use Cake\ORM\TableRegistry;
use phpmock\MockBuilder;

/**
 * Acciona\Users\Controller\PermissionsController Test Case
 */
class UsersControllerTest extends IntegrationTestCase
{
    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'plugin.acciona/users.permissions',
        'plugin.acciona/users.permissions_actions',
        'plugin.acciona/users.roles',
        'plugin.acciona/users.roles_permissions',
        'plugin.acciona/users.users',
        'plugin.acciona/users.users_roles',
        'plugin.acciona/users.password_tokens',
    ];

    public function setUp()
    {
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

        parent::setUp();
    }

    public function tearDown()
    {
        $this->mock->disable();
        parent::tearDown();
    }

    /**
     * Test reset method wit bad email
     *
     * @return void
     */
    public function testResetBadEmail() {
        $this->configRequest([
            'headers' => ['Accept' => 'application/json']
        ]);
        $this->post('/users/reset.json', ['email' => 'user33@acciona.net']);
        $this->assertResponseCode(400);


    }

    /**
     * Test reset method wit correct email
     *
     * @return void
     */
    public function testResetCorrectEmail() {
        $this->configRequest([
            'headers' => ['Accept' => 'application/json']
        ]);
        $this->post('/users/reset.json', ['email' => 'user3@acciona.net']);
        $this->assertResponseOk();

        $query = $this->PasswordTokens
            ->find()
            ->where(['PasswordTokens.user_id' => 3])
            ->order(['PasswordTokens.expiration DESC']);
        $this->assertFalse($query->isEmpty());

        $record = $query->first();
        $this->assertEquals(301, $record->expiration);
    }

    public function controllerSpy($event, $controller = null){
        parent::controllerSpy($event, $controller);

        $emailer = $this->createMock('Cake\Mailer\Email');
        $emailer->method('send')->willReturn('true');
        $emailer->method('template')->willReturn($emailer);
        $emailer->method('to')->willReturn($emailer);
        $emailer->method('from')->willReturn($emailer);
        $emailer->method('viewVars')->willReturn($emailer);

        $this->_controller->emailer = $emailer;
    }
}
