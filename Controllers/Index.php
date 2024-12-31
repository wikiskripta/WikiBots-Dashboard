<?php

namespace Wikibots\Controllers;

/**
 * @see Controller
 */
class Index extends Controller
{

    /**
     * @inheritDoc
     */
    public function process(array $args = []): int
    {
        self::$data['layout']['title'] = 'Správa robotů WikiSkript';

        self::$views[] = 'index';

        return 200;
    }
}

