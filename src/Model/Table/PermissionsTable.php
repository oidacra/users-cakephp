<?php
namespace Acciona\Users\Model\Table;

use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;
use Acciona\Users\Utils\CollectionsUtils;

/**
 * Permissions Model
 *
 * @property \Cake\ORM\Association\HasMany $PermissionsActions
 * @property \Cake\ORM\Association\BelongsToMany $Roles
 */
class PermissionsTable extends Table
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

        $this->table('permissions');
        $this->displayField('id');
        $this->primaryKey('id');

        $this->hasMany('PermissionsActions', [
            'foreignKey' => 'permission_id',
            'className' => 'Acciona/Users.PermissionsActions'
        ]);
        $this->belongsToMany('Roles', [
            'foreignKey' => 'permission_id',
            'targetForeignKey' => 'rol_id',
            'joinTable' => 'roles_permissions',
            'className' => 'Acciona/Users.Roles',
            'through' => 'Acciona/Users.RolesPermissions'
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
            ->requirePresence('entity', 'create')
            ->notEmpty('entity');

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
        $rules->add($rules->isUnique(['entity']));
        return $rules;
    }

    /**
     * Returns the actions from database given
     *
     * @param int $userId
     * @param String $domain
     * @param String $entity
     * @param String $action
     * @return array[int]
     */
    public function getUserActionsByEntity($userId, $domain, $entity)
    {
      $permissions = $this
                        ->findUserPermissionsByEntity(
                          $userId,
                          $domain,
                          $entity)->first();
      if ($permissions) {
        return CollectionsUtils::flatMap($permissions->roles, function ($rol) {
          return $this->splitActions($rol->_joinData['actions']);
        });
      }

      return [];
    }

    private function splitActions($actions)
    {
      if ($actions == '*') {
        return ['*'];
      }

      return explode(',', $actions);
    }

    public function getUserActions($userId)
    {
        $permissionsQuery = $this->findUserPermissions($userId);
        // TODO(Danilo): obtain all actions and ids to create a map and then update data
        // TODO(Danilo): return format should be plugin, controller, action

    }

    /**
     * Returns permissions by user id
     * @param $userId int
     */
    public function findUserPermissions($userId)
    {
        $queryRoles = $this->Roles->findRolesByUserId($userId);
        $query = $this
          ->find()
          ->contain(['Roles'])
          ->innerJoinWith('Roles', function ($q) use($queryRoles) {
            return $q->where(['Roles.id IN' => $queryRoles]);
        });

        return $query;
    }

    public function findUserPermissionsByEntity($userId, $domain, $entity)
    {
        $query = $this
          ->findUserPermissions($userId)
          ->where(['Permissions.domain' => $domain,
                   'Permissions.entity' => $entity]);

        return $query;
    }
}
