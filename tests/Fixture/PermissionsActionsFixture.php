<?php
namespace Acciona\Users\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

/**
 * PermissionsActionsFixture
 *
 */
class PermissionsActionsFixture extends TestFixture
{

    /**
     * Fields
     *
     * @var array
     */
    // @codingStandardsIgnoreStart
    public $fields = [
        'id' => ['type' => 'integer', 'length' => 11, 'unsigned' => false, 'null' => false, 'default' => null, 'comment' => '', 'autoIncrement' => true, 'precision' => null],
        'action' => ['type' => 'string', 'length' => 100, 'null' => true, 'default' => null, 'comment' => '', 'precision' => null, 'fixed' => null],
        'permission_id' => ['type' => 'integer', 'length' => 11, 'unsigned' => false, 'null' => false, 'default' => null, 'comment' => '', 'precision' => null, 'autoIncrement' => null],
        '_indexes' => [
            'fk_permission_actions_permissions_idx' => ['type' => 'index', 'columns' => ['permission_id'], 'length' => []],
        ],
        '_constraints' => [
            'primary' => ['type' => 'primary', 'columns' => ['id'], 'length' => []],
            'fk_permission_actions_permissions' => ['type' => 'foreign', 'columns' => ['permission_id'], 'references' => ['permissions', 'id'], 'update' => 'noAction', 'delete' => 'noAction', 'length' => []],
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
            'action' => 'index',
            'permission_id' => 1
        ],
        [
            'id' => 2,
            'action' => 'add',
            'permission_id' => 1
        ],
        [
            'id' => 3,
            'action' => 'edit',
            'permission_id' => 1
        ],
        [
            'id' => 4,
            'action' => 'delete',
            'permission_id' => 1
        ],
        [
            'id' => 5,
            'action' => 'action1',
            'permission_id' => 2
        ],
        [
            'id' => 6,
            'action' => 'action2',
            'permission_id' => 2
        ],
        [
            'id' => 7,
            'action' => 'action3',
            'permission_id' => 2
        ],
        [
            'id' => 8,
            'action' => 'action1',
            'permission_id' => 3
        ],
        [
            'id' => 9,
            'action' => 'action2',
            'permission_id' => 3
        ],
        [
            'id' => 10,
            'action' => 'action3',
            'permission_id' => 3
        ],
    ];
}
