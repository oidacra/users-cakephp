<?php

use Cake\Core\Configure;

$config = [
    'Users' => [
        //configure Auth component
        'auth' => true,
        //Password Hasher
        'passwordHasher' => '\Cake\Auth\DefaultPasswordHasher',
        //token expiration, 5 hr
        'Token' => ['expiration' => 18000],
        //Password recovery configuration
        'PasswordRecovery' => [
            'sender' => 'danilo@accionasolutions.net',
            'template' => 'password_recovery',
            'layout' => 'default',
            'link' => '',
            'expiration' => 300 // 5 minutes
        ],
        //Minimum Password length
        'minPasswordLen' => 6,
    ]
];

return $config;
