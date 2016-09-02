<?php
namespace Acciona\Users\Test\TestCase\Controller\Component;

use Acciona\Users\Controller\Component\AccionaACLComponent;
use Cake\Controller\Controller;
use Cake\Controller\ComponentRegistry;
use Cake\Network\Request;
use Cake\Network\Response;
use Cake\TestSuite\TestCase;

class AccionaACLComponentTest extends TestCase
{
    public $component = null;
    public $controller = null;
    public $request = null;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'plugin.acciona/users.permissions',
        'plugin.acciona/users.permissions_actions',
        'plugin.acciona/users.roles',
        'plugin.acciona/users.roles_permissions',
        'plugin.acciona/users.users',
        'plugin.acciona/users.users_roles'
    ];


    public function setUp()
    {
        parent::setUp();
        // Setup our component and fake test controller
        $request = new Request();
        $response = new Response();
        $this->controller = $this->createMock(
            'Cake\Controller\Controller',
            null,
            [$request, $response]
        );
        $registry = new ComponentRegistry($this->controller);
        $this->component = new AccionaACLComponent($registry, []);
        $this->request = $request;
    }

    public function testCheck()
    {
        $user = [
            'id' => 1
        ];
        $this->request->param('controller', 'Users');
        $this->request->param('plugin', 'Acciona/Users');
        $this->request->param('action', 'index');

        $this->assertFalse($this->component->check($user, $this->request));

        $user['id'] = 2;
        $this->assertTrue($this->component->check($user, $this->request));
    }
}
