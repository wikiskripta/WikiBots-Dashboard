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
        setcookie('wsdb_session', "deleted", 1, '/', '.wikiskripta.eu', true, true); //NOTE: not universal
        header('Location: /');
        exit();
    }
}