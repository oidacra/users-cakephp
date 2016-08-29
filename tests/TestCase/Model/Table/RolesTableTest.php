<?php
namespace Acciona\Users\Test\TestCase\Model\Table;

use Acciona\Users\Model\Table\RolesTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * Acciona\Users\Model\Table\RolesTable Test Case
 */
class RolesTableTest extends TestCase
{

    /**
     * Test subject
     *
     * @var \Acciona\Users\Model\Table\RolesTable
     */
    public $Roles;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'plugin.acciona/users.roles',
        'plugin.acciona/users.permissions',
        'plugin.acciona/users.roles_permissions',
        'plugin.acciona/users.users',
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
        $config = TableRegistry::exists('Roles') ? [] : ['className' => 'Acciona\Users\Model\Table\RolesTable'];
        $this->Roles = TableRegistry::get('Roles', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->Roles);

        parent::tearDown();
    }

    /**
     * Test find roles by user
     *
     * @return void
     */
    public function testFindRolesByUser()
    {
        $result = $this->Roles->findRolesByUserId(1);
        $this->assertTrue($result->isEmpty());

        $result = $this->Roles->findRolesByUserId(2);
        $expected = array(2);
        $this->assertFalse($result->isEmpty());
        $ids = $this->getRolesFromResultSet($result);
        $this->assertEquals($ids, $expected);

        $result = $this->Roles->findRolesByUserId(3);
        $expected = array(2,3);
        $this->assertFalse($result->isEmpty());
        $ids = $this->getRolesFromResultSet($result);
        $this->assertEquals($ids, $expected);

        $result = $this->Roles->findRolesByUserId(4);
        $expected = array(1);
        $this->assertFalse($result->isEmpty());
        $ids = $this->getRolesFromResultSet($result);
        $this->assertEquals($ids, $expected);
    }

    private function getRolesFromResultSet($result)
    {
        return array_map(function ($e) {
            return $e->id;
        }, $result->toArray());
    }
}
