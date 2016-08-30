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
        $this->assertFalse($result->isEmpty());
        $resultArray = $this->getActionsFromResultSet($result);
        $this->assertEquals($expected, $resultArray);

        $result = $this->Permissions->findUserPermissions(3);
        $expected = array(array(1, array(1,2)),
                          array(3, array(9,10)),
                          array(2, array('*')));
        $resultArray = $this->getActionsFromResultSet($result);
        $this->assertFalse($result->isEmpty());
        $this->assertEquals($expected, $resultArray);

        $result = $this->Permissions->findUserPermissions(4);
        $this->assertTrue($result->isEmpty());


        $result = $this->Permissions->findUserPermissions(5);
        $this->assertFalse($result->isEmpty());
        $expected = array(array(1, array(1)),
                          array(1, array(3)),
                          array(2, array(6)),
                          array(3, array(9)));
        $resultArray = $this->getActionsFromResultSet($result);
        $this->assertFalse($result->isEmpty());
        $this->assertEquals($expected, $resultArray);
    }

    /**
     * Test findUserPermissions method
     *
     * @return void
     */
    public function testGetUserActionsByEntity()
    {
        $result = $this->Permissions->getUserActionsByEntity(1,
                                        'Acciona/Users', 'Users');
        $this->assertTrue(empty($result));

        // user 2 has 1,2 (Users, index,add)
        $result = $this->Permissions->getUserActionsByEntity(2,
                                        'Acciona/Users', 'Users');
        $this->assertFalse(empty($result));
        $expected = array(1,2);
        $this->assertEquals($expected, $result);

        // user 2 does not have Controller2
        $result = $this->Permissions->getUserActionsByEntity(2,
                                         '', 'Controllers1');
        $this->assertTrue(empty($result));

         // user 3 has roles 2 and 3.
         $result = $this->Permissions->getUserActionsByEntity(3,
                                         'Acciona/Users', 'Users');
         $this->assertFalse(empty($result));
         $expected = array(1,2);
         $this->assertEquals($expected, $result);

         $result = $this->Permissions->getUserActionsByEntity(3,
                                         '', 'Controllers1');
         $this->assertFalse(empty($result));
         $expected = array('*');
         $this->assertEquals($expected, $result);

         $result = $this->Permissions->getUserActionsByEntity(3,
                                         '', 'Controllers2');
         $this->assertFalse(empty($result));
         $expected = array(9,10);
         $this->assertEquals($expected, $result);

         $result = $this->Permissions->getUserActionsByEntity(3,
                                         '', 'Controllers3');
         $this->assertTrue(empty($result));

         // user 5 has rol 5
         $result = $this->Permissions->getUserActionsByEntity(5,
                                         '', 'Controllers1');
         $this->assertFalse(empty($result));
         $expected = array(6);
         $this->assertEquals($expected, $result);

         $result = $this->Permissions->getUserActionsByEntity(5,
                                         '', 'Controllers2');
         $this->assertFalse(empty($result));
         $expected = array(9);
         $this->assertEquals($expected, $result);

         $result = $this->Permissions->getUserActionsByEntity(5,
                                         'Acciona/Users', 'Users');
         $this->assertFalse(empty($result));
         $expected = array(1,3);
         $this->assertEquals($expected, $result);

         $result = $this->Permissions->getUserActionsByEntity(5,
                                         '', 'Users');
         $this->assertTrue(empty($result));
    }

    /**
     * Test getUserActions method
     *
     * @return void
     */
    public function testGetUserActions()
    {
        $result = $this->Permissions->getUserActions(1);
        $this->assertTrue(empty($result));

        $result = $this->Permissions->getUserActions(2);
        $expected = [
            [
                'domain' => 'Acciona/Users',
                'entity' => 'Users',
                'action' => 'index',
            ],
            [
                'domain' => 'Acciona/Users',
                'entity' => 'Users',
                'action' => 'add',
            ],
            [
                'domain' => '',
                'entity' => 'Controllers2',
                'action' => 'index',
            ],
            [
                'domain' => '',
                'entity' => 'Controllers2',
                'action' => 'add',
            ]
        ];
        $this->assertTrue($expected, $result);
    }

    private function getActionsFromResultSet($result)
    {
        return array_map(function ($e) {
            $actions = array_unique(CollectionsUtils::flatMap(function ($permission) {
                return $this->splitActions($permission->actions);
            }, $e->_matchingData));

            return array($e->id, $actions);
        }, $result->toArray());
    }

    private function splitActions($actions)
    {
        if ($actions == '*') {
            return ['*'];
        }

        $strs = explode(',', $actions);
        $actionArray = array_map(function ($e) {
            return intval($e);
        }, $strs);

        return $actionArray;
    }

}
