<?php
return array (
  'debug' => true,
  'Error' => 
  array (
    'errorLevel' => 16383,
  ),
  'App' => 
  array (
    'namespace' => 'Users\\Test\\App',
    'encoding' => 'UTF-8',
  ),
  'Session' => 
  array (
    'defaults' => 'php',
  ),
  'plugins' => 
  array (
  ),
  'Users' => 
  array (
    'auth' => true,
    'passwordHasher' => '\\Cake\\Auth\\DefaultPasswordHasher',
    'Token' => 
    array (
      'expiration' => 18000,
    ),
    'PasswordRecovery' => 
    array (
      'sender' => 'no-reply@accionasolutions.net',
      'template' => 'password_recovery',
      'layout' => 'default',
      'link' => '',
      'expiration' => 300,
    ),
    'minPasswordLen' => 6,
  ),
);