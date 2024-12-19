<?php

namespace Wikibots\Models;

class UserManager
{
    public function isUserLoggedIn() : bool
    {
        return empty($_SESSION['user']);
    }

    public function wsLogin() : bool
    {
        
    }
}

