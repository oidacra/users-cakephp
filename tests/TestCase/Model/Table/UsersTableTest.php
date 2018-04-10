<?php
namespace Acciona\Users\Test\TestCase\Model\Table;

use Acciona\Users\Model\Table\UsersTable;
use Acciona\Users\Model\Entity\User;
use Cake\Core\Configure;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;
use Cake\Event\EventListenerInterface;

class UsersQueryAuth implements EventListenerInterface
{

    public function implementedEvents()
    {
        return [
            UsersTable::EVENT_BEFORE_AUTH => 'addRoles',
        ];
    }

    public function addRoles($event, $Users, $query)
    {
        $query->contain('Roles');

        return $query;
    }
}

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
        'plugin.acciona/users.permissions',
        'plugin.acciona/users.permissions_actions',
        'plugin.acciona/users.roles',
        'plugin.acciona/users.roles_permissions',
        'plugin.acciona/users.users',
        'plugin.acciona/users.users_roles',
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
        // correct
        $pass = '123456%Abcd';
        $user = $this->getUser('user15@acciona.net', $pass, $pass);

        $result = $this->Users->save($user);
        $this->assertNotFalse($result);

        // lacks a upper case
        $pass = '123456%abcd';
        $user = $this->getUser('user2@acciona.net', $pass, $pass);

        $result = $this->Users->save($user);
        $this->assertFalse($result);

        // lacks a symbol
        $pass = '123456Aabcd';
        $user = $this->getUser('user2@acciona.net', $pass, $pass);

        $result = $this->Users->save($user);
        $this->assertFalse($result);

        // too short
        $pass = '1%Aac';
        $user = $this->getUser('user2@acciona.net', $pass, $pass);

        $result = $this->Users->save($user);
        $this->assertFalse($result);

        // different passwords
        // too short
        $pass = '221%Aac';
        $retypePass = '221%acC';
        $user = $this->getUser('user2@acciona.net', $pass, $retypePass);

        $result = $this->Users->save($user);
        $this->assertFalse($result);
    }

    /**
     * Test validationDefault method
     *
     * @return void
     */
    public function testValidationCustomPassword()
    {
        // define custom password validator
        $customPasswordValidator = function ($password, $context) {
            $len = strlen($password);
            return $len > 3 && $len < 10;
        };
        Configure::write('Users.passwordValidator', $customPasswordValidator);

        // correct for custom password
        $pass = '123456';
        $user = $this->getUser('user15@acciona.net', $pass, $pass);

        $result = $this->Users->save($user);
        $this->assertNotFalse($result);

        // incorrect
        $pass = '1234567891011';
        $user = $this->getUser('user16@acciona.net', $pass, $pass);

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
     * Test findAuth method
     *
     * @return void
     */
     public function testFindAuth()
     {
         $query = $this->Users->find()->where([
             'Users.email' => 'user2@acciona.net',
         ]);

         // register event
         $usersQueryAuth = new UsersQueryAuth();
         $this->Users->eventManager()->on($usersQueryAuth);

         $result = $this->Users->findAuth($query, [])->first();
         $this->assertEquals(1, count($result->roles));

         $query = $this->Users->find()->where([
             'Users.email' => 'user3@acciona.net',
         ]);
         $result = $this->Users->findAuth($query, [])->first();
         $this->assertEquals(2, count($result->roles));
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
