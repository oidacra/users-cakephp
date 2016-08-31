<?php
namespace Acciona\Users\Model\Table;

use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;
use Cake\Log\Log;
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
        // TODO(Danilo): keep just one register for rol,permission
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
                          $entity);
      if (!$permissions->isEmpty()) {
          return CollectionsUtils::flatMap(function ($e) {
               return array_unique(CollectionsUtils::flatMap(function ($permission) {
                   return $this->splitActions($permission->actions);
               }, $e->_matchingData));
          }, $permissions->toArray());
      }

      return [];
    }

    public function getUserActions($userId)
    {
        $permissions = $this->findUserPermissions($userId);

        if (!$permissions->isEmpty()) {
            // get all actions map[id -> name]
            $actionsMap = $this->PermissionsActions->getActionsMap();
            $results = CollectionsUtils::flatMap(function ($permission) use($actionsMap) {
                $actionsResults = CollectionsUtils::flatMap(
                function ($rolesPermission) use($actionsMap, $permission) {
                    $actions = $this->splitActions($rolesPermission->actions);
                    return array_map(function ($action) use($permission, $actionsMap) {
                        if ($action == '*') {
                            return [
                                'domain' => $permission->domain,
                                'entity' => $permission->entity,
                                'action' => '*',
                            ];
                        } else {
                            if (!isset($actionsMap[$action])) {
                                Log::write('error',
                                    __('Action {1} for permission {2}
                                        does not exist', [$action,
                                        $permission->entity]));
                                return [];
                            }
                            return [
                                'domain' => $permission->domain,
                                'entity' => $permission->entity,
                                'action' => $actionsMap[$action],
                            ];
                        }
                    }, $actions);
                }, $permission->_matchingData);

                return $actionsResults;
             }, $permissions->toArray());

             return $results;
        }

        return [];
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

    /**
     * Returns permissions by user id
     * @param $userId int
     */
    public function findUserPermissions($userId)
    {
        $queryRoles = $this->Roles->findRolesByUserId($userId);

        $query = $this
          ->find()
          ->select([
              'Permissions.id',
              'Permissions.entity',
              'Permissions.domain',
              'RolesPermissions.actions'
          ])
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
