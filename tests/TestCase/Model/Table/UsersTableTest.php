<?php
namespace Acciona\Users\Test\TestCase\Model\Table;

use Acciona\Users\Model\Table\UsersTable;
use Acciona\Users\Model\Entity\User;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * Acciona\Users\Model\Table\UsersTable Test Case
 */
class UsersTableTest extends TestCase
{

    /**
     * Test subject
     *
     * @var \Acciona\Users\Model\Table\UsersTable
     */
    public $Users;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'plugin.acciona/users.users'
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $config = TableRegistry::exists('Users') ? [] : ['className' => 'Acciona\Users\Model\Table\UsersTable'];
        $this->Users = TableRegistry::get('Users', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->Users);

        parent::tearDown();
    }

    /**
     * Test register user
     *
     * @return void
     */
    public function testRegister()
    {
        $data = [
            'email' => 'user2sda@acciona.net',
            'password' => '123456%Abcd',
            'retype_password' => '123456%Abcd',
            'administrator' => 0,
            'active' => 1,
            'name' => 'User 2',
            'last_name' => 'Users',
        ];
        $user = $this->Users->newEntity();
        $user = $this->Users->patchEntity($user, $data);

        $result = $this->Users->register($user);

        $this->assertInstanceOf(User::class, $result);
        $this->assertEquals(0, $result->active);
        $this->assertEquals(0, $result->administrator);

        // test getting record from db
        $record = $this->Users->get($result->id);

        $this->assertInstanceOf(User::class, $record);
        $this->assertEquals(0, $record->active);
        $this->assertEquals(0, $record->administrator);
    }

    /**
     * Test validationDefault method
     *
     * @return void
     */
    public function testValidationPassword()
    {
        // lacks a upper case
        $password = '123456%abcd';
        $user = $this->getUser($password = $password, $retypePassword = $password);

        $result = $this->Users->save($user);
        $this->assertFalse($result);

        // lacks a symbol
        $password = '123456Aabcd';
        $user = $this->getUser($password = $password, $retypePassword = $password);

        $result = $this->Users->save($user);
        $this->assertFalse($result);

        // too short
        $password = '1%Aac';
        $user = $this->getUser($password = $password, $retypePassword = $password);

        $result = $this->Users->save($user);
        $this->assertFalse($result);

        // different passwords
        // too short
        $password = '221%Aac';
        $retypePassword = '221%acC';
        $user = $this->getUser($password = $password, $retypePassword = $retypePassword );

        $result = $this->Users->save($user);
        $this->assertFalse($result);
    }

    private function getUser($email = 'user2@acciona.net',
                            $password = '1234%Abcd',
                            $retypePassword = '1234%Abcd')
    {
        $data = [
            'email' => $email,
            'password' => $password,
            'retype_password' => $retypePassword,
            'administrator' => 0,
            'active' => 1,
            'name' => 'User 2',
            'last_name' => 'Users',
        ];
        $user = $this->Users->newEntity();
        $user = $this->Users->patchEntity($user, $data);

        return $user;
    }

    /**
     * Test buildRules method
     *
     * @return void
     */
    public function testBuildRules()
    {
        // test unique email
        $user = $this->getUser($email = 'user3adas@acciona.net');
        $user2 = $this->getUser($email = 'user4dsda@acciona.net');

        $result = $this->Users->save($user);
        $this->assertInstanceOf(User::class, $result);

        $result = $this->Users->save($user2);
        $this->assertInstanceOf(User::class, $result);

        $user = $this->getUser($email = 'user3adas@acciona.net');
        $result = $this->Users->save($user);
        $this->assertFalse($result);
    }
}
