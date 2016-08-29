<?php
namespace Acciona\Users\Model\Table;

use Acciona\Users\Model\Entity\PasswordToken;
use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * PasswordTokens Model
 *
 * @property \Cake\ORM\Association\BelongsTo $Users
 */
class PasswordTokensTable extends Table
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

        $this->table('password_tokens');
        $this->displayField('id');
        $this->primaryKey('id');

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
            ->requirePresence('token', 'create')
            ->notEmpty('token');

        $validator
            ->dateTime('expiration')
            ->requirePresence('expiration', 'create')
            ->notEmpty('expiration');

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

    /**
     * Create new token in database and return the token string
     *
     * @return string | boolean
     */
    public function createToken($userId, $expiration)
    {
        $tokenRecord = $this->newEntity();
        $tokenRecord->token = bin2hex(random_bytes(78));
        $tokenRecord->expiration = time() + $expiration;
        $tokenRecord->user_id = $userId;
        if (!$this->save($tokenRecord)) {
            return false;
        }

        return $tokenRecord->token;
    }
}
