<?php
namespace Acciona\Users\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;
use Cake\Auth\DefaultPasswordHasher;

/**
 * UsersFixture
 *
 */
class UsersLogsFixture extends TestFixture
{
    /**
     * Fields
     *
     * @var array
     */
    // @codingStandardsIgnoreStart
    public $fields = [
        'id' => ['type' => 'integer', 'length' => 11, 'unsigned' => false, 'null' => false, 'default' => null, 'comment' => '', 'autoIncrement' => true, 'precision' => null],
        'user_id' => ['type' => 'integer', 'length' => 11, 'unsigned' => false, 'null' => false, 'default' => null, 'comment' => '', 'precision' => null],
        'date' => ['type' => 'datetime', 'length' => null, 'null' => true, 'default' => null, 'comment' => '', 'precision' => null],
        '_constraints' => [
            'primary' => ['type' => 'primary', 'columns' => ['id'], 'length' => []],
        ],
        '_options' => [
            'engine' => 'InnoDB',
            'collation' => 'utf8'
        ],
    ];
    // @codingStandardsIgnoreEnd

   public function init()
   {
       $this->records = [
           [
               'id' => 1,
               'user_id' => 1,
               'date' => date('Y-m-d H:i:s')
           ],
       ];
       parent::init();
   }
}
