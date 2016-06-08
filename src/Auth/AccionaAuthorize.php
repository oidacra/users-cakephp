<?php
namespace Acciona\Users\Auth;

use Cake\Auth\BaseAuthorize;
use Cake\Network\Request;

/**
 * Acciona Authorization Component
 *
 * @author Danilo Dominguez Perez
 */
class AccionaAuthorize extends BaseAuthorize
{
    public function authorize($user, Request $request)
    {
        if (isset($user['administrator']) && $user['administrator'] == '1') {
            return true;
        }

        // use ACL component to validate the user
        $ACL = $this->_registry->load('Acciona/Users.AccionaACL');

        return $ACL->check($user, $request);
    }
}
