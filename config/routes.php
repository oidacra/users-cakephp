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
<<<<<<< HEAD
            $routes->resources('Roles',
                [
                    'map' => [
                        'lista' => [
                            'action' => 'lista',
                            'method' => 'GET'
                        ],
                    ]
                ]);
=======
            $routes->resources('Roles',[
                'map' => [
                    'lista' => [
                        'action' => 'lista',
                        'method' => 'GET'
                    ],
                ]
            ]);
>>>>>>> 9ef6e965a79c05e8fa25d12f1cea092d9f303e0c
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
