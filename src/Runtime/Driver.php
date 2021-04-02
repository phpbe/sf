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

    /**
     * @var HttpServer
     */
    protected $httpServer = null;

    public function execute()
    {
        if ($this->httpServer == null) {
            $this->httpServer = new HttpServer();
            $this->httpServer->start();
        }
    }

    public function getHttpServer() {
        return $this->httpServer;
    }

}
