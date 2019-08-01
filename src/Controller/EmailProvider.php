<?php
namespace Acciona\Users\Controller;

class EmailProvider 
{
    public function getEmailer() 
    {
        return new Email();
    }
}

