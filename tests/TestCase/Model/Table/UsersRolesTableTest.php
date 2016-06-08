<?php
namespace Acciona\Users\Test\TestCase\Model\Table;

use Acciona\Users\Model\Table\UsersRolesTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * Acciona\Users\Model\Table\UsersRolesTable Test Case
 */
class UsersRolesTableTest extends TestCase
{

    /**
     * Test subject
     *
     * @var \Acciona\Users\Model\Table\UsersRolesTable
     */
    public $UsersRoles;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'plugin.acciona/users.users_roles',
        'plugin.acciona/users.users',
        'plugin.acciona/users.roles',
        'plugin.acciona/users.permissions',
        'plugin.acciona/users.roles_permissions'
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $config = TableRegistry::exists('UsersRoles') ? [] : ['className' => 'Acciona\Users\Model\Table\UsersRolesTable'];
        $this->UsersRoles = TableRegistry::get('UsersRoles', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->UsersRoles);

        parent::tearDown();
    }

    /**
     * Test initialize method
     *
     * @return void
     */
    public function testInitialize()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test validationDefault method
     *
     * @return void
     */
    public function testValidationDefault()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test buildRules method
     *
     * @return void
     */
    public function testBuildRules()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }
}
