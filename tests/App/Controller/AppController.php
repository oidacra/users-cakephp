<?php
namespace Acciona\Users\Test\App\Controller;

use Cake\Controller\Controller;

class AppController extends Controller
{
    public function initialize()
    {
        parent::initialize();

        // load core components
        $this->loadComponent('RequestHandler');
        $this->loadComponent('Flash');
        $this->loadComponent('Acciona/Users.AccionaAuth', [
            'authenticate' => 'Jwt'
        ]);
        //$this->loadComponent('Security');
        //$this->loadComponent('Csrf');
    }

    public function isRestCall() {
      return $this->request->is('json') || $this->request->is('xml');
    }
}
