<?php
use Cake\Routing\Router;

Router::plugin(
    'Acciona/Users',
    ['path' => '/'],
    function ($routes) {
        $routes->fallbacks('DashedRoute');
        $routes->scope('/', function ($routes) {
            $routes->extensions(['json']);
            $routes->resources('Users', [
              'map' => [
                'login' => [
                  'action' => 'login',
                  'method' => 'POST'
                ],
                'logout' => [
                  'action' => 'logout',
                  'method' => 'GET'
                ],
                'register' => [
                  'action' => 'register',
                  'method' => 'POST'
                ],
                'user' => [
                  'action' => 'user',
                  'method' => 'GET'
                ],
                'password_recovery' => [
                  'action' => 'passwordRecovery',
                  'method' => 'POST'
                ],
                'reset' => [
                  'action' => 'reset',
                  'method' => 'POST'
                ],
              ]
            ]);
            $routes->resources('Roles',[
                'map' => [
                    'lista' => [
                        'action' => 'lista',
                        'method' => 'GET'
                    ],
                ]
            ]);
            $routes->resources('Permissions',
                [
                  'map' => [
                    'permissions' => [
                      'action' => 'permissions',
                      'method' => 'GET'
                    ]
                  ]
                ]
            );
        });
    }
);
