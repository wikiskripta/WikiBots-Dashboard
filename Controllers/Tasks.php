<?php

namespace Wikibots\Controllers;

use Wikibots\Models\UserManager;

/**
 * @see Controller
 */
class Tasks extends Controller
{

    /**
     * @inheritDoc
     */
    public function process(array $args = []): int
    {
        $um = new UserManager();
        if (!$um->isUserLoggedIn()) {

        }

        self::$data['layout']['title'] = 'Správa úkonů';
        self::$views[] = 'tasks';

        return 200;
    }
}

