<?php
namespace Acciona\Users\Model\Table;

use Acciona\Users\Model\Entity\Role;
use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Roles Model
 *
 * @property \Cake\ORM\Association\BelongsToMany $Permissions
 * @property \Cake\ORM\Association\BelongsToMany $Users
 */
class RolesTable extends Table
{

    /**
     * Initialize method
     *
     * @param array $config The configuration for the Table.
     * @return void
     */
    public function initialize(array $config)
    {
        parent::initialize($config);

        $this->table('roles');
        $this->displayField('name');
        $this->primaryKey('id');

        $this->belongsToMany('Permissions', [
            'through' => 'Acciona/Users.RolesPermissions'
        ]);
        $this->belongsToMany('Users', [
            'foreignKey' => 'rol_id',
            'targetForeignKey' => 'user_id',
            'joinTable' => 'users_roles',
            'className' => 'Acciona/Users.Users'
        ]);
    }

    /**
     * Default validation rules.
     *
     * @param \Cake\Validation\Validator $validator Validator instance.
     * @return \Cake\Validation\Validator
     */
    public function validationDefault(Validator $validator)
    {
        $validator
            ->integer('id')
            ->allowEmpty('id', 'create');

        $validator
            ->requirePresence('name', 'create')
            ->notEmpty('name');

        return $validator;
    }

    public function getRolesByUserId($userId)
    {
        return $this
                  ->find()
                  ->select(['id'])
                  ->contain(['Users' => function ($q) use($userId) {
                    return $q->where(['Users.id' => $userId]);
                  }]);
    }
}
