<?php
namespace Acciona\Users\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;
use Cake\Auth\DefaultPasswordHasher;

/**
 * UsersFixture
 *
 */
class UsersFixture extends TestFixture
{

    /**
     * Fields
     *
     * @var array
     */
    // @codingStandardsIgnoreStart
    public $fields = [
        'id' => ['type' => 'integer', 'length' => 11, 'unsigned' => false, 'null' => false, 'default' => null, 'comment' => '', 'autoIncrement' => true, 'precision' => null],
        'email' => ['type' => 'string', 'length' => 255, 'null' => false, 'default' => null, 'comment' => '', 'precision' => null, 'fixed' => null],
        'password' => ['type' => 'string', 'length' => 255, 'null' => false, 'default' => null, 'comment' => '', 'precision' => null, 'fixed' => null],
        'name' => ['type' => 'string', 'length' => 255, 'null' => false, 'default' => null, 'comment' => '', 'precision' => null, 'fixed' => null],
        'last_name' => ['type' => 'string', 'length' => 255, 'null' => false, 'default' => null, 'comment' => '', 'precision' => null, 'fixed' => null],
        'administrator' => ['type' => 'integer', 'length' => 1, 'unsigned' => true, 'null' => false, 'default' => 0],
        'active' => ['type' => 'integer', 'length' => 1, 'unsigned' => true, 'null' => false, 'default' => 1],
        'created' => ['type' => 'datetime', 'length' => null, 'null' => true, 'default' => null, 'comment' => '', 'precision' => null],
        'modified' => ['type' => 'datetime', 'length' => null, 'null' => true, 'default' => null, 'comment' => '', 'precision' => null],
        '_constraints' => [
            'primary' => ['type' => 'primary', 'columns' => ['id'], 'length' => []],
        ],
        '_options' => [
            'engine' => 'InnoDB',
            'collation' => 'latin1_swedish_ci'
        ],
    ];
    // @codingStandardsIgnoreEnd

   public function init()
   {
       $hasher = new DefaultPasswordHasher();
       $this->records = [
           [
               'id' => 1,
               'email' => 'admin@acciona.net',
               'password' => $hasher->hash('12345%Abcd'),
               'administrator' => 1,
               'active' => 1,
               'name' => 'Super',
               'last_name' => 'Administrator',
               'created' => date('Y-m-d H:i:s'),
               'modified' => date('Y-m-d H:i:s'),
           ],
           [
               'id' => 2,
               'email' => 'user2@acciona.net',
               'password' => $hasher->hash('12345%Abcd'),
               'administrator' => 0,
               'active' => 1,
               'name' => 'User2',
               'last_name' => 'Users',
               'created' => date('Y-m-d H:i:s'),
               'modified' => date('Y-m-d H:i:s'),
           ],
           [
               'id' => 3,
               'email' => 'user3@acciona.net',
               'password' => $hasher->hash('12345%Abcd'),
               'administrator' => 0,
               'active' => 1,
               'name' => 'User3',
               'last_name' => 'Users',
               'created' => date('Y-m-d H:i:s'),
               'modified' => date('Y-m-d H:i:s'),
           ],
           [
               'id' => 4,
               'email' => 'user4@acciona.net',
               'password' => $hasher->hash('12345%Abcd'),
               'administrator' => 0,
               'active' => 0,
               'name' => 'User4',
               'last_name' => 'Users',
               'created' => date('Y-m-d H:i:s'),
               'modified' => date('Y-m-d H:i:s'),
           ],
           [
               'id' => 5,
               'email' => 'user5@acciona.net',
               'password' => $hasher->hash('12345%Abcd'),
               'administrator' => 0,
               'active' => 1,
               'name' => 'User5',
               'last_name' => 'Users',
               'created' => date('Y-m-d H:i:s'),
               'modified' => date('Y-m-d H:i:s'),
           ],
       ];
       parent::init();
   }
}
