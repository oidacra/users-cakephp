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
              ]
            ]);
            $routes->resources('Roles');
            $routes->resources('Permissions');
        });
    }
);
