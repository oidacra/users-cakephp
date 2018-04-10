<?php
namespace Acciona\Users\Model\Table;

use Acciona\Users\Model\Entity\User;
use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Event\Event;
use Cake\Validation\Validator;
use Cake\Core\Configure;
use Cake\Utility\Hash;
/**
 * Users Model
 *
 */
class UsersTable extends Table
{
    protected $minPasswordLen;
    const EVENT_BEFORE_AUTH = 'Users.Model.Users.beforeAuth';

    /**
     * Initialize method
     *
     * @param array $config The configuration for the Table.
     * @return void
     */
    public function initialize(array $config)
    {
        parent::initialize($config);

        $this->table('users');
        $this->displayField('name');
        $this->primaryKey('id');

        $this->addBehavior('Timestamp');

        $this->hasMany('PasswordTokens', [
            'foreignKey' => 'user_id',
            'className' => 'Acciona/Users.PasswordTokens'
        ]);

        $this->belongsToMany('Roles', [
            'foreignKey' => 'user_id',
            'targetForeignKey' => 'rol_id',
            'joinTable' => 'users_roles',
            'className' => 'Acciona/Users.Roles'
        ]);

        $this->minPasswordLen = Configure::read('Users.minPasswordLen', 6);
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
            ->email('email')
            ->requirePresence('email', 'create')
            ->notEmpty('email');

        $validator
            ->notEmpty('name')
            ->maxLength('name', 70);

        $validator
            ->notEmpty('last_name')
            ->maxLength('last_name', 70);

        // the password validator can be configured by the application
        $passwordValidator = Configure::check('Users.passwordValidator') ?
            Configure::read('Users.passwordValidator') : [$this, 'isStrongPassword'];

        $validator
            ->requirePresence('password', 'create')
            ->add('password',
                'strong', [
                    'rule' => $passwordValidator,
                    'message' => __d('Users', 'Password should have at least an upper case letter,
                                     lower case letter, a number and a symbol.')
                ]
            )
            ->add('password',
                'match', [
                    'rule' => function ($value, $context) {
                        $confirm = Hash::get($context, 'data.retype_password');
                        if (!is_null($confirm) && $value != $confirm) {
                            return false;
                        }
                        return true;
                    },
                    'message' => __d('Users', 'Passwords do not match')
                ]
            );

        $validator
            ->requirePresence('retype_password', 'create')
            ->notEmpty('retype_password');

        return $validator;
    }

    /**
     * Verifies if a password has at least one upper case letter, one lower case letter,
     * a number and a symbol and at least 6 characters.
     *
     * @param $password
     * @param $context
     * @return bool
     */
    public function isStrongPassword($password, $context)
    {
        $hasLetters = preg_match("/[a-zA-Z]/", $password);
        $hasNumbers = preg_match("/[0-9]/", $password);
        $hasPunctuation = preg_match("/[^a-zA-Z0-9]/", $password);
        $hasUpperCaseLetter = preg_match("/[a-z]+.*[A-Z]+|[A-Z]+.*[a-z]/", $password);
        $r = ($hasLetters && $hasNumbers && $hasPunctuation && $hasUpperCaseLetter);

        return strlen($password) >= $this->minPasswordLen && $r;
    }

    public function register(User $user)
    {
        // manually set the user to be inactive
        $user->active = 0;
        return $this->save($user);
    }

    public function saveAndUpdateToken(User $user, $passwordToken) {
        return $this->connection()->transactional(function () use ($user, $passwordToken) {
            if (!$this->save($user)) {
                return false;
            }

            // update token and return result of update
            $passwordToken->active = 0;
            return $this->PasswordTokens->save($passwordToken);
        });
    }

    public function findAuth(Query $query, array $options)
    {
        // dispatch events registered
        $event = new Event(UsersTable::EVENT_BEFORE_AUTH, $this, [
            'Users' => $this, 'query' => $query
        ]);
        $this->eventManager()->dispatch($event);
        if (!empty($event->result)) {
            $query = $event->result;
        }

        return $query;
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
        $rules->add($rules->isUnique(['email']));
        return $rules;
    }
}
