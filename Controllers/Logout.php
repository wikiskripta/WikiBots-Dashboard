<?php

namespace Wikibots\Controllers;

use Wikibots\Controllers\Controller;

class Logout extends Controller
{

    /**
     * @inheritDoc
     */
    public function process(array $args = []): int
    {
        unset($_SESSION['user']);
        header('Location: /');
        exit();
    }
}