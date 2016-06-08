<?php

namespace Acciona\Users\Controller;

use App\Controller\AppController as BaseController;
use Cake\Error\Debugger;

class AppController extends BaseController
{
    public function initialize()
    {
        parent::initialize();

        // load core components
        $this->loadComponent('RequestHandler');
        $this->loadComponent('Flash');
        //$this->loadComponent('Security');
        //$this->loadComponent('Csrf');
        /*$this->loadComponent('Acciona/Users.AccionaAuth', [
            'authenticate' => 'Jwt'
            // 'authenticate' => 'Acciona'
        ]);*/
    }

    public function isRestCall() {
      return $this->request->is('json') || $this->request->is('xml');
    }
}
