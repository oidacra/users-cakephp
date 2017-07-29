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
    public function testResetBadEmail()
    {
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
    public function testResetCorrectEmail()
    {
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

    public function testPasswordRecovery()
    {
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
        $token = $record->token;

        // incorrect token
        $this->post('/users/password_recovery.json', ['token' => $token . '123']);
        $this->assertResponseCode(400);

        // correct token no password
        $this->mock->disable();
        $this->post('/users/password_recovery.json', ['token' => $token]);
        $this->assertResponseCode(400);

        // short password
        $this->post('/users/password_recovery.json', ['token' => $token, 'password' => '123']);
        $this->assertResponseOk();
        // check password was not correct
        $data = json_decode($this->_response->body());
        $this->assertEquals(false, $data->user->success);

        //correct token and password
        $this->post('/users/password_recovery.json', ['token' => $token, 'password' => '123%Abcd']);
        $this->assertResponseOk();
        $data = json_decode($this->_response->body());
        $this->assertEquals(true, $data->user->success);

        // check that token is not active
        $result = $this->PasswordTokens->findByToken($token);
        $this->assertFalse($result->isEmpty());
        $this->assertEquals(0, $result->first()->active);
    }

    public function controllerSpy($event, $controller = null)
    {
        parent::controllerSpy($event, $controller);

        $email = $this->createMock('Cake\Mailer\Email');
        $email->method('send')->willReturn('true');
        $email->method('template')->willReturn($email);
        $email->method('to')->willReturn($email);
        $email->method('from')->willReturn($email);
        $email->method('viewVars')->willReturn($email);

        $this->_controller->emailer = $email;
    }
}
