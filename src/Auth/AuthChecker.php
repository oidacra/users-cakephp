<?php
namespace Acciona\Users\Auth;

use Cake\Network\Request;

interface AuthChecker
{
    /**
     * Verifies whether the user has access to an specific action
     *
     * @param $user array with id and extra information
     * @param $request Request 
     */
    public function check($user, Request $request);
}
