<?php
namespace Acciona\Users\Test\TestCase\Model\Table;

use Acciona\Users\Model\Table\PermissionsActionsTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * Acciona\Users\Model\Table\PermissionsActionsTable Test Case
 */
class PermissionsActionsTableTest extends TestCase
{

    /**
     * Test subject
     *
     * @var \Acciona\Users\Model\Table\PermissionsActionsTable
     */
    public $PermissionsActions;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'plugin.acciona/users.permissions_actions',
        'plugin.acciona/users.permissions',
        'plugin.acciona/users.roles',
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
        $config = TableRegistry::exists('PermissionsActions') ? [] : ['className' => 'Acciona\Users\Model\Table\PermissionsActionsTable'];
        $this->PermissionsActions = TableRegistry::get('PermissionsActions', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->PermissionsActions);

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
