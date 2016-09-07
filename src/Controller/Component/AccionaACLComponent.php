<?php
namespace Acciona\Users\Controller\Component;

use Acciona\Users\Auth\AuthChecker;
use Cake\Controller\Component;
use Cake\Core\App;
use Cake\Error\Debugger;
use Cake\ORM\TableRegistry;
use Cake\Network\Request;
use Cake\Controller\ComponentRegistry;
use Cake\Log\Log;

/**
 * Verifies if the user has permissions to execute an action
 *
 * @package Acciona\Users\Controller\Component
 * @property \Acciona\Users\Model\Table\PermissionsTable $Permissions
 * @property \Acciona\Users\Model\Table\PermissionsActionsTable $PermissionsActions
 */
class AccionaACLComponent extends Component implements AuthChecker
{
    public function __construct(ComponentRegistry $registry, array $config)
    {
        parent::__construct($registry, $config);

        $this->Permissions = $this
                              ->getTable(
                                  'Acciona/Users.PermissionsTable',
                                  'Permissions');
        $this->PermissionsActions = $this
                              ->getTable(
                                  'Acciona/Users.PermissionsActionsTable',
                                  'PermissionsActions');
    }

    private function getTable($className, $name)
    {
      $config = [];
      if (!TableRegistry::exists($name)) {
          $config = ['className' => App::className($className, 'Model/Table')];
      }

      return TableRegistry::get($name, $config);
    }

    /**
     * Verifies whether the user has access to an specific action
     *
     * @param $user array with id and extra information
     * @param $request Request
     */
    public function check($user, Request $request)
    {
        $userId = $user['id'];
        $entity = $request->param('controller');
        $action = $request->param('action');
        $domain = $request->param('plugin');

        $currentActionId = $this
                              ->PermissionsActions
                              ->getActionId($domain, $entity, $action);

        if (!$currentActionId) {
          Log::write('error',
                      __('Permission for domain: {0}, entity: {1}, action: {2}
                          does not exists', [$domain, $entity, $action]));
          return false;
        }

        $actions = $this->Permissions
                        ->getUserActionsByEntity(
                            $userId,
                            $domain,
                            $entity);

       return $this->hasPermissions($actions, $currentActionId);
    }

    protected function hasPermissions($actions, $currentActionId)
    {
      if ($actions) {
         foreach ($actions as $actionId) {
           if ($actionId == '*' || $actionId == $currentActionId) {
             return true;
           }
         }
       }

       return false;
    }
}
