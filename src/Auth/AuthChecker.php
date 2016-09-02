<?php
namespace Acciona\Users\Auth;

use Cake\Network\Request;

interface AuthChecker
{
    public function check($user, Request $request);
}
