<?php
namespace Acciona\Users\Test\TestCase\Model\Table;

use Acciona\Users\Model\Table\PermissionsTable;
use Acciona\Users\Utils\CollectionsUtils;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * Acciona\Users\Model\Table\PermissionsTable Test Case
 */
class PermissionsTableTest extends TestCase
{

    /**
     * Test subject
     *
     * @var \Acciona\Users\Model\Table\PermissionsTable
     */
    public $Permissions;

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
        $config = TableRegistry::exists('Permissions') ? [] : ['className' => 'Acciona\Users\Model\Table\PermissionsTable'];
        $this->Permissions = TableRegistry::get('Permissions', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->Permissions);

        parent::tearDown();
    }

    /**
     * Test findUserPermissions method
     *
     * @return void
     */
    public function testFindUserPermissions()
    {
        $result = $this->Permissions->findUserPermissions(1);
        $this->assertTrue($result->isEmpty());

        $result = $this->Permissions->findUserPermissions(2);
        $expected = array(array(1, array(1,2)), array(3, array(9,10)));
        $resultArray = $this->getActionsFromResultSet($result);
        $this->assertFalse($result->isEmpty());
        $this->assertEquals($resultArray, $expected);

        $result = $this->Permissions->findUserPermissions(3);
        $expected = array(array(1, array(1,2)),
                          array(3, array(9,10)),
                          array(2, array('*')));
        $resultArray = $this->getActionsFromResultSet($result);
        $this->assertFalse($result->isEmpty());
        $this->assertEquals($resultArray, $expected);

        $result = $this->Permissions->findUserPermissions(4);
        $this->assertTrue($result->isEmpty());

        //TODO(Danilo): need more tests and data
    }

    private function getActionsFromResultSet($result)
    {
        return array_map(function ($e) {
            $actions = CollectionsUtils::flatMap(function ($rol) {
              return $this->splitActions($rol->_joinData['actions']);
            }, $e->roles);
            return array($e->id, $actions);
        }, $result->toArray());
    }

    private function splitActions($actions)
    {
      if ($actions == '*') {
        return ['*'];
      }

      return explode(',', $actions);
    }

}
