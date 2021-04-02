<?php

namespace Be\Sf\Runtime;

use Be\F\Config\ConfigFactory;
use Be\F\Db\DbFactory;
use Be\F\Redis\RedisFactory;
use Be\F\Runtime\RuntimeException;
use Be\F\Runtime\RuntimeFactory;
use Be\Sf\Be;

class RpcServer
{

    /**
     * @var \Swoole\Server
     */
    private $swooleServer = null;

    public function __construct()
    {
    }

    public function start()
    {
        if ($this->swooleServer !== null) {
            return;
        }

        \Co::set(['hook_flags' => SWOOLE_HOOK_ALL]);

        $configSystem = ConfigFactory::getInstance('System.System');
        date_default_timezone_set($configSystem->timezone);

        $configServer = ConfigFactory::getInstance('System.Server');
        $this->swooleServer = new \Swoole\Server($configServer->rpc_host, $configServer->rpc_port);

        $setting = [
            'enable_coroutine' => true,
        ];

        if ($configServer->rpc_reactor_num > 0) {
            $setting['reactor_num'] = $configServer->rpc_reactor_num;
        }

        if ($configServer->rpc_worker_num > 0) {
            $setting['worker_num'] = $configServer->rpc_worker_num;
        }

        if ($configServer->rpc_max_request > 0) {
            $setting['max_request'] = $configServer->rpc_max_request;
        }

        if ($configServer->rpc_max_conn > 0) {
            $setting['max_conn'] = $configServer->rpc_max_conn;
        }

        $this->swooleServer->set($setting);

        // 初始化数据库，Redis连接池
        DbFactory::init();
        RedisFactory::init();

        if ($configServer->clearCacheOnStart) {
            $dir = RuntimeFactory::getInstance()->getCachePath();
            \Be\F\Util\FileSystem\Dir::rm($dir);
        }

        $this->swooleServer->on('Receive', function ($server, $fd, $reactorId, $data) {
            try {
                $data = \Swoole\Serialize::unpack($data);

                if (!isset($data['service'])) {
                    throw new RuntimeException('参数（service）缺失！');
                }

                if (!isset($data['method'])) {
                    throw new RuntimeException('参数（method）缺失！');
                }

                if (!isset($data['params'])) {
                    throw new RuntimeException('参数（params）缺失！');
                }

                $service = $data['service'];
                $method = $data['method'];
                $params = $data['params'];

                $service = Be::getService($service);
                $result = $service->$method(...$params);

                $server->send($fd, \Swoole\Serialize::pack([
                    'success' => true,
                    'message' => '',
                    'data' => $result
                ]));
                $server->close($fd);

            } catch (\Throwable $t) {
                Be::getLog()->emergency($t);

                $server->send($fd, \Swoole\Serialize::pack([
                    'success' => false,
                    'message' => $t->getMessage(),
                ]));
                $server->close($fd);
            }
        });

        $this->swooleServer->start();
    }



    public function stop()
    {
        $this->swooleServer->stop();
    }

    public function reload()
    {
        $this->swooleServer->reload();
    }


    public function getSwooleServer()
    {
        return $this->swooleServer;
    }
    
}
