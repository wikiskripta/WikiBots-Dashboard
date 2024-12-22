<?php

namespace Wikibots\Controllers;

use Wikibots\Models\TaskManager;
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
        $tm = new TaskManager();
        self::$data['tasks']['tasks'] = $tm->getTasks();
        self::$views[] = 'tasks';

        return 200;
    }
}

