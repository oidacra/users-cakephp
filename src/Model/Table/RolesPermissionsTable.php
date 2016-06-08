<?php
namespace Acciona\Users\Model\Table;

use Acciona\Users\Model\Entity\RolesPermission;
use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * RolesPermissions Model
 *
 * @property \Cake\ORM\Association\BelongsTo $Roles
 * @property \Cake\ORM\Association\BelongsTo $Permissions
 */
class RolesPermissionsTable extends Table
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

        $this->table('roles_permissions');
        $this->displayField('id');
        $this->primaryKey('id');

        $this->belongsTo('Roles', [
            'foreignKey' => 'rol_id',
            'joinType' => 'INNER',
            'className' => 'Acciona/Users.Roles'
        ]);
        $this->belongsTo('Permissions', [
            'foreignKey' => 'permission_id',
            'joinType' => 'INNER',
            'className' => 'Acciona/Users.Permissions'
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
            ->requirePresence('actions', 'create')
            ->notEmpty('actions');

        return $validator;
    }

    /**
     * Returns a rules checker object that will be used for validating
     * application integrity.
     *
     * @param \Cake\ORM\RulesChecker $rules The rules object to be modified.
     * @return \Cake\ORM\RulesChecker
     */
    public function buildRules(RulesChecker $rules)
    {
        $rules->add($rules->existsIn(['rol_id'], 'Roles'));
        $rules->add($rules->existsIn(['permission_id'], 'Permissions'));
        return $rules;
    }
}
