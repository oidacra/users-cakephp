<?php

namespace Acciona\Users\Model\Table;

use Acciona\Users\Model\Entity\User;
use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;
use Cake\I18n\Time;

/**
 * UsersLogs Model
 *
 * @property \App\Model\Table\UsersTable|\Cake\ORM\Association\BelongsTo $Users
 *
 * @method \App\Model\Entity\UsersLog get($primaryKey, $options = [])
 * @method \App\Model\Entity\UsersLog newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\UsersLog[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\UsersLog|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\UsersLog patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\UsersLog[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\UsersLog findOrCreate($search, callable $callback = null, $options = [])
 */
class UsersLogsTable extends Table
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

        $this->setTable('users_logs');
        $this->setDisplayField('id');
        $this->setPrimaryKey(['id', 'user_id']);

        $this->belongsTo('Users', [
            'foreignKey' => 'user_id',
            'joinType' => 'INNER'
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
            ->dateTime('date')
            ->requirePresence('date', 'create')
            ->notEmpty('date');

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
        $rules->add($rules->existsIn(['user_id'], 'Users'));

        return $rules;
    }

    public function logAccess($user)
    {
        $data = [
            'user_id' => $user['data']['id'],
            'date' => Time::now()
        ];
        $log = $this->newEntity();
        $log = $this->patchEntity($log, $data);
        $this->save($log);
    }
}
