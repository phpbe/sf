<?php

namespace Be\Sf\App\JsonRpc\Controller;


use Be\F\App\ControllerException;
use Be\Sf\Be;

class Index
{

    public function __construct()
    {
        $configServer = Be::getConfig('System.Server');
        if (!$configServer->admin) {
            throw new ControllerException('管理模块未启用！');
        }
    }

}
