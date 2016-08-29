<?php
namespace Acciona\Users\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

/**
 * RolesPermissionsFixture
 *
 */
class RolesPermissionsFixture extends TestFixture
{

    /**
     * Fields
     *
     * @var array
     */
    // @codingStandardsIgnoreStart
    public $fields = [
        'id' => ['type' => 'integer', 'length' => 11, 'unsigned' => false, 'null' => false, 'default' => null, 'comment' => '', 'autoIncrement' => true, 'precision' => null],
        'actions' => ['type' => 'text', 'length' => null, 'null' => false, 'default' => null, 'comment' => '', 'precision' => null],
        'rol_id' => ['type' => 'integer', 'length' => 11, 'unsigned' => false, 'null' => false, 'default' => null, 'comment' => '', 'precision' => null, 'autoIncrement' => null],
        'permission_id' => ['type' => 'integer', 'length' => 11, 'unsigned' => false, 'null' => false, 'default' => null, 'comment' => '', 'precision' => null, 'autoIncrement' => null],
        '_indexes' => [
            'fk_roles_permissions_roles1_idx' => ['type' => 'index', 'columns' => ['rol_id'], 'length' => []],
            'fk_roles_permissions_permissions1_idx' => ['type' => 'index', 'columns' => ['permission_id'], 'length' => []],
        ],
        '_constraints' => [
            'primary' => ['type' => 'primary', 'columns' => ['id'], 'length' => []],
            'fk_roles_permissions_permissions1' => ['type' => 'foreign', 'columns' => ['permission_id'], 'references' => ['permissions', 'id'], 'update' => 'noAction', 'delete' => 'noAction', 'length' => []],
            'fk_roles_permissions_roles1' => ['type' => 'foreign', 'columns' => ['rol_id'], 'references' => ['roles', 'id'], 'update' => 'noAction', 'delete' => 'noAction', 'length' => []],
        ],
        '_options' => [
            'engine' => 'InnoDB',
            'collation' => 'utf8_general_ci'
        ],
    ];
    // @codingStandardsIgnoreEnd

    /**
     * Records
     *
     * @var array
     */
    public $records = [
        [
            'id' => 1,
            'actions' => '1,2', // Users, index,add
            'rol_id' => 2,
            'permission_id' => 1
        ],
        [
            'id' => 2,
            'actions' => '*', // Controllers2, all actions
            'rol_id' => 3,
            'permission_id' => 2
        ],
        [
            'id' => 3,
            'actions' => '9,10', // Controllers3, action2,action3
            'rol_id' => 2,
            'permission_id' => 3
        ],
    ];
}
