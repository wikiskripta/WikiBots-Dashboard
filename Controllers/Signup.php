<?php

namespace Wikibots\Controllers;

use Wikibots\Models\UserException;

/**
 * @see Controller
 */
class Signup extends Controller
{

    /**
     * @inheritDoc
     */
    public function process(array $args = []): int
    {
        self::$data['layout']['title'] = 'Zaregistrovat se';

        self::$views[] = 'signup';

        return 200;
    }
}

