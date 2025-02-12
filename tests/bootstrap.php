<?php

$findRoot = function ($root) {
    do {
        $lastRoot = $root;
        $root = dirname($root);
        if (is_dir($root . '/vendor/cakephp/cakephp')) {
            return $root;
        }
    } while ($root !== $lastRoot);
    throw new Exception("Cannot find the root of the application, unable to run tests");
};
$root = $findRoot(__FILE__);
unset($findRoot);
chdir($root);
if (!defined('DS')) {
    define('DS', DIRECTORY_SEPARATOR);
}
define('ROOT', $root);
define('APP_DIR', 'App');
define('WEBROOT_DIR', 'webroot');
define('APP', ROOT . '/tests/App/');
define('CONFIG', ROOT . '/tests/config/');
define('WWW_ROOT', ROOT . DS . WEBROOT_DIR . DS);
define('TESTS', ROOT . DS . 'tests' . DS);
define('TMP', ROOT . DS . 'tmp' . DS);
define('LOGS', TMP . 'logs' . DS);
define('CACHE', TMP . 'cache' . DS);
define('CAKE_CORE_INCLUDE_PATH', ROOT . '/vendor/cakephp/cakephp');
define('CORE_PATH', CAKE_CORE_INCLUDE_PATH . DS);
define('CAKE', CORE_PATH . 'src' . DS);
require ROOT . '/vendor/cakephp/cakephp/src/basics.php';
require ROOT . '/vendor/autoload.php';
//Cake\Core\Configure::write('Error.errorLevel', E_ALL & ~E_USER_DEPRECATED);
Cake\Core\Configure::write('App', ['namespace' => 'Users\Test\App']);
Cake\Core\Configure::write('debug', true);
Cake\Core\Configure::write('App.encoding', 'UTF-8');
ini_set('intl.default_locale', 'en_US');
//error_reporting(E_ALL & ~E_USER_DEPRECATED);
$TMP = new \Cake\Filesystem\Folder(TMP);
$TMP->create(TMP . 'cache/models', 0777);
$TMP->create(TMP . 'cache/persistent', 0777);
$TMP->create(TMP . 'cache/views', 0777);

// set salt
Cake\Utility\Security::salt(uniqid(mt_rand(), true));

$cache = [
    'default' => [
        'engine' => 'File',
    ],
    '_cake_core_' => [
        'className' => 'File',
        'prefix' => 'users_myapp_cake_core_',
        'path' => CACHE . 'persistent/',
        'serialize' => true,
        'duration' => '+10 seconds',
    ],
    '_cake_model_' => [
        'className' => 'File',
        'prefix' => 'users_app_cake_model_',
        'path' => CACHE . 'models/',
        'serialize' => 'File',
        'duration' => '+10 seconds',
    ],
];
Cake\Cache\Cache::config($cache);
Cake\Core\Configure::write('Session', [
    'defaults' => 'php'
]);

//init router
\Cake\Routing\Router::reload();
\Cake\Core\Plugin::load('ADmad/JwtAuth', [
    'path' => ROOT . '/vendor/admad/cakephp-jwt-auth',
    'routes' => false
]);
\Cake\Core\Plugin::load('Acciona/Users', [
    'path' => dirname(dirname(__FILE__)) . DS,
    'routes' => true
]);

// load configs for Users Plugin
Cake\Core\Configure::load('Acciona/Users.users');
collection((array)Cake\Core\Configure::read('Users.config'))->each(function ($file) {
    Cake\Core\Configure::load($file);
});
/*if (file_exists($root . '/config/bootstrap.php')) {
    //require $root . '/config/bootstrap.php';
}*/
Cake\Routing\DispatcherFactory::add('Routing');
Cake\Routing\DispatcherFactory::add('ControllerFactory');

class_alias('Acciona\Users\Test\App\Controller\AppController', 'App\Controller\AppController');


// Ensure default test connection is defined
if (!getenv('db_dsn')) {
    putenv('db_dsn=sqlite:///:memory:');
}
Cake\Datasource\ConnectionManager::config('test', [
    'url' => getenv('db_dsn'),
    'timezone' => 'UTC'
]);
