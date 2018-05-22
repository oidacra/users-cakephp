<?php
namespace Acciona\Users\Shell;

use Cake\Console\Shell;
use Cake\Core\App;
use Cake\Core\Plugin;
use Acciona\Users\Utils\StringUtils;
use Cake\ORM\TableRegistry;

/**
 * Import permissions from controllers' actions
 *
 * @package Acciona\Users\Shell
 * @property \Acciona\Users\Model\Table\PermissionsTable $Permissions
 */
class PermissionsShell extends Shell
{
    protected $Permissions;

    public function initialize()
    {
        parent::initialize();
        $config = [];
        if (!TableRegistry::exists('Permissions')) {
            $config = ['className' => App::className('Acciona/Users.PermissionsTable', 'Model/Table')];
        }
        $this->Permissions = TableRegistry::get('Permissions', $config);
    }

    public function main()
    {
        $this->out('Importing permissions from controller files...');

        // get src directory for app and plugins
        $permissions = $this->getPermissions();
        // save permissions
        try {
            $this->Permissions->connection()->transactional(function () use ($permissions) {
                foreach ($permissions as $permission) {
                    // if the permission exists just update the actions
                    $permissionRecord = $this->Permissions
                                                    ->findByEntity($permission->entity)
                                                    ->contain(['PermissionsActions'])
                                                    ->first();

                    // save just new actions if permission exists
                    if ($permissionRecord) {
                        $permissionId = $permissionRecord->id;
                        $actions = $permissionRecord->permissions_actions;
                        // filter out old actions
                        $newActions = array_filter($permission->permissions_actions, function ($newAction) use($actions) {
                            foreach ($actions as $action) {
                                if ($action->action == $newAction->action) {
                                    return false;
                                }
                            }

                            return true;
                        });

                        if (!empty($newActions)) {
                            foreach ($newActions as $newAction) {
                                $newAction->permission_id = $permissionId;
                                $this->Permissions->PermissionsActions->save($newAction, ['atomic' => false]);
                            }
                        }
                    } else {
                        $this->Permissions->save($permission, ['atomic' => false]);
                    }
                }
            });
            $this->out('Permissions were imported correctly.');
        } catch (Exception $e) {
            $this->out('Error importing permissions. Please try again.');
        }
    }

    protected function getPermissions()
    {
        $permissions = [];

        // get controllers from app
        $controllersPaths = App::path('Controller');
        foreach ($controllersPaths as $controllersPath) {
            $permissions = array_merge($permissions, $this->getPermissionsFromPath($controllersPath));
        }

        // get controllers from plugins
        foreach ($this->getPluginControllersPaths() as $plugin) {
            list($domain, $path) = $plugin;
            if (file_exists($path)) {
                $permissions = array_merge($permissions, $this->getPermissionsFromPath($path, $domain));
            }
        }

        return $permissions;
    }

    protected function getPermissionsFromPath($controllersPath, $domain = '')
    {
        $permissions = [];
        foreach (scandir($controllersPath) as $file) {
            if ($file != 'AppController.php' && StringUtils::endsWith($file, 'Controller.php')) {
                $tokens = token_get_all(file_get_contents($controllersPath . '/' . $file));
                $controllerClass = '';
                $namespaceName = '';
                for ($i = 0; $i < count($tokens); $i++) {
                    if ($tokens[$i][0] === T_CLASS) {
                        for ($j=$i+1;$j<count($tokens);$j++) {
                            if ($tokens[$j] === '{' && isset($tokens[$i+2][1])) {
                                $controllerClass = $tokens[$i+2][1];
                            }
                        }
                    }

                    if ($tokens[$i][0] === T_NAMESPACE) {
                        for ($j = $i + 1; $j < count($tokens); $j++) {
                            if ($tokens[$j] === ';') {
                                break;
                            }
                            if (trim($tokens[$j][1]) !== '') {
                                $namespaceName .= $tokens[$j][1];
                            }
                        }
                    }
                }

                if (!empty($controllerClass) && !empty($namespaceName)) {
                    $reflectionClass = new \ReflectionClass($namespaceName . '\\' . $controllerClass);
                    $actions = array_map(function ($method) {
                        $entity = $this->Permissions->PermissionsActions->newEntity();
                        $entity->action = $method->name;
                        return $entity;
                    }, array_filter($reflectionClass->getMethods(), function($method) use($controllerClass) {
                        return $method->class != 'Cake\Controller\Controller' && $method->isPublic();
                    }));

                    $permission = $this->Permissions->newEntity();
                    $permission->entity = substr($controllerClass, 0, strlen($controllerClass) - 10);
                    $permission->domain = $domain;
                    $permission->permissions_actions = $actions;
                    $permissions[] = $permission;
                }
            }
        }

        return $permissions;
    }

    protected function getPluginControllersPaths()
    {
        $plugins = array_map(function ($plugin) {
            return array($plugin, Plugin::path($plugin) . 'src/Controller');
        }, Plugin::loaded());

        return $plugins;
    }
}
