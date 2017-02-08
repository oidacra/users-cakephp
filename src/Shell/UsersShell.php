<?php
namespace Acciona\Users\Shell;

use Acciona\Users\Model\Entity\User;
use Cake\Console\Shell;

/**
 * Creates a user in the database
 *
 * @package Acciona\Users\Shell
 * @property \Acciona\Users\Model\Table\UsersTable $Users
 */

class UsersShell extends Shell
{
    protected $Users;

    public function initialize()
    {
        parent::initialize();
        $this->Users = $this->loadModel('Acciona/Users.Users'); //TableRegistry::get('Users', $config);
    }

    public function main()
    {
        $this->out('Create a new user: ');
        $data = [];
        $data['email'] = trim($this->in('Email: '));
        $data['name'] = trim($this->in('Name: '));
        $data['last_name'] = trim($this->in('Last Name: '));
        $data['password'] = $this->in('Password: ');
        $data['retype_password'] = $this->in('Repeat password: ');
        $administrator = $this->in('Administrator?: Y/N', ['Y', 'N'], 'N');
        $data['administrator'] = strtolower($administrator) == 'y' ? 1 : 0;

        $User = $this->Users->newEntity($data);
        if ($this->Users->save($User)) {
            $this->out('User ' . $User->email . ' has been created');
        } else {
            $this->err('Error creating user with followin errors:');
            \Cake\Error\Debugger::dump($User->errors());
        }
    }
}