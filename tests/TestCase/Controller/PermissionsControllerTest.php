<?php
namespace Acciona\Users\Test\TestCase\Controller;

use Acciona\Users\Controller\PermissionsController;
use Cake\TestSuite\IntegrationTestCase;

/**
 * Acciona\Users\Controller\PermissionsController Test Case
 */
class PermissionsControllerTest extends IntegrationTestCase
{

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

    /**
     * Test permissions method
     *
     * @return void
     */
    public function testPermissions()
    {
        // log in user
        $this->configRequest([
            'headers' => ['Accept' => 'application/json']
        ]);
        $this->post('/users/login.json', [
            'email' => 'user2@acciona.net',
            'password' => '12345%Abcd'
        ]);
        $this->assertResponseSuccess();

        $data = json_decode($this->_response->body());

        $token = $data->data->token;
        $this->configRequest([
            'headers' => [
                'Accept' => 'application/json',
                'authorization' => 'Bearer ' . $token
            ]
        ]);
        $this->get('/permissions/permissions.json');
        $this->assertResponseOk();
        $result = json_decode($this->_response->body(), true);
        $expected = [
            [
                'domain' => 'Acciona/Users',
                'entity' => 'Users',
                'action' => 'index',
            ],
            [
                'domain' => 'Acciona/Users',
                'entity' => 'Users',
                'action' => 'add',
            ],
            [
                'domain' => '',
                'entity' => 'Controllers2',
                'action' => 'action2',
            ],
            [
                'domain' => '',
                'entity' => 'Controllers2',
                'action' => 'action3',
            ]
        ];
        $this->assertEquals($expected, $result['actions']);
    }


}
