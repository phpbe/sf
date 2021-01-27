<?php

namespace Be\Sf\App\System\Controller;

use Be\Sf\Be;


class Index
{

    public function index()
    {
        Be::getResponse()->end('#' . \Swoole\Coroutine::getuid() . ' working...' );
    }

}
