<?php
namespace Acciona\Users\Controller\Component;

use \Cake\Controller\Component;


/**
 * Authorization Component for Users Pluging
 *
 * @author Danilo Dominguez Perez
 */
class AccionaAuthComponent extends Component
{
    private $defaultConfig = [
        'authenticate' => [
            'Form' => [
                'scope' => ['Users.active' => 1],
                'fields' => ['username' => 'email']
            ]
        ],
        'authorize' => 'Acciona/Users.Acciona'
    ];

    /**
     * Initialize method, setup Auth if not already done passing the $config provided and
     * setup the default table to Users.Users if not provided
     *
     * @param array $config config options
     * @return void
     */
    public function initialize(array $config)
    {
        parent::initialize($config);

        // initialize the auth component
        $this->_registry->getController()->loadComponent('Auth', $this->getAuthConfigArray($config));
        $this->_registry->getController()->Auth->allow([
            'login',
            'register',
            'validateEmail',
            'resendTokenValidation',
            'resetPassword',
            'requestResetPassword',
            'changePassword',
        ]);
    }

    private function getAuthConfigArray(array $passedConfig)
    {
        $config = array_merge($this->defaultConfig, $passedConfig);
        $config['storage'] = 'Session';
        if ($config['authenticate'] == 'Jwt') {
            $config['storage'] = 'Memory';
            $config['unauthorizedRedirect'] = false;
            $config['checkAuthIn'] = 'Controller.initialize';
            $config['loginAction'] = '';
            $config['loginRedirect'] = '';
            $config['authenticate'] = [
                'Form' => [
                    'scope' => ['Users.active' => 1],
                    'finder' => 'auth',
                    'userModel' => 'Acciona/Users.Users',
                    'fields' => ['username' => 'email']
                ],
                'ADmad/JwtAuth.Jwt' => [
                    'parameter' => 'token',
                    'userModel' => 'Users',
                    'scope' => ['Users.active' => 1],
                    'fields' => [
                        'username' => 'email'
                    ],
                    'finder' => 'auth',
                    'userModel' => 'Acciona/Users.Users',
                    'queryDatasource' => true
                ],
            ];
        } else {
          $config['loginAction'] = [
              'controller' => 'Users',
              'action' => 'login',
              'plugin' => 'Acciona/Users'
          ];
          $config['loginRedirect'] = [
              'plugin' => 'Acciona/Users',
              'controller' => 'Users',
              'action' => 'index'
          ];
          $config['logoutRedirect'] = [
              'plugin' => 'Acciona/Users',
              'controller' => 'Users',
              'action' => 'login'
          ];
          $config['authError'] = __('You do not have permissions to see this page');
        }

        return $config;
    }
}
