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
        self::$data['layout']['page_id'] = 'index';
        self::$data['layout']['title'] = 'Prázdný web';

        self::$views[] = 'index';

        return 200;
    }
}

