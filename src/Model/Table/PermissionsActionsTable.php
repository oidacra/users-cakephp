<?php
namespace Acciona\Users\Model\Table;

use Acciona\Users\Model\Entity\PermissionsAction;
use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * PermissionsActions Model
 *
 * @property \Cake\ORM\Association\BelongsTo $Permissions
 */
class PermissionsActionsTable extends Table
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

        $this->table('permissions_actions');
        $this->displayField('id');
        $this->primaryKey('id');

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
            ->allowEmpty('action');

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
        $rules->add($rules->existsIn(['permission_id'], 'Permissions'));
        return $rules;
    }

    /**
     * Return the action id depending on the domain, entity and action text
     *
     * @param String $domain
     * @param String  $entity
     * @param String  $action
     * @return int | null
     */
    public function getActionId($domain, $entity, $action)
    {
      $actionRecord = $this
        ->find()
        ->contain(['Permissions' => function ($q) use($domain, $entity) {
          return $q->where(['Permissions.domain' => $domain,
                     'Permissions.entity' => $entity]);
        }])
        ->select(['id'])
        ->where(['PermissionsActions.action' => $action])->first();

      return $actionRecord ? $actionRecord->id : null;
    }
}
