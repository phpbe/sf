<?php

namespace Be\Sf\Runtime;


/**
 *  运行时
 *
 * @package Be\Sf\Runtime
 */
class Driver extends \Be\F\Runtime\Driver
{

    protected $frameworkName = 'Sf'; // 框架名称 Mf/Sf/Ff

    public function execute()
    {
        $httpServer = new HttpServer();
        $httpServer->start();
    }

}
