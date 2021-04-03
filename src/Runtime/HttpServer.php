<?php

namespace Be\Sf\Runtime;

use Be\F\Config\ConfigFactory;
use Be\F\Db\DbFactory;
use Be\F\Redis\RedisFactory;
use Be\F\Request\RequestFactory;
use Be\F\Response\ResponseFactory;
use Be\F\Runtime\RuntimeException;
use Be\F\Runtime\RuntimeFactory;
use Be\Sf\Be;

class HttpServer
{

    /**
     * @var \Swoole\Http\Server
     */
    private $swooleHttpServer = null;

    /**
     * @var \Swoole\Server\Port
     */
    private $swooleServerPort = null;

    public function __construct()
    {
    }

    public function start()
    {
        if ($this->swooleHttpServer !== null) {
            return;
        }

        $configServer = ConfigFactory::getInstance('System.Server');
        if (!$configServer->admin && !!$configServer->jsonRpc) {
            return;
        }

        \Co::set(['hook_flags' => SWOOLE_HOOK_ALL]);

        // 检查网站配置， 是否暂停服务
        $configSystem = Be::getConfig('System.System');
        date_default_timezone_set($configSystem->timezone);

        $this->swooleHttpServer = new \Swoole\Http\Server($configServer->http_host, $configServer->http_port);

        $setting = [
            'enable_coroutine' => true,
        ];

        if ($configServer->reactor_num > 0) {
            $setting['reactor_num'] = $configServer->reactor_num;
        }

        if ($configServer->worker_num > 0) {
            $setting['worker_num'] = $configServer->worker_num;
        }

        if ($configServer->max_request > 0) {
            $setting['max_request'] = $configServer->max_request;
        }

        if ($configServer->max_conn > 0) {
            $setting['max_conn'] = $configServer->max_conn;
        }

        $this->swooleHttpServer->set($setting);

        // 初始化数据库，Redis连接池
        DbFactory::init();
        RedisFactory::init();

        if ($configServer->clearCacheOnStart) {
            $dir = RuntimeFactory::getInstance()->getCachePath();
            \Be\F\Util\FileSystem\Dir::rm($dir);
        } else {
            $sessionConfig = ConfigFactory::getInstance('System.Session');
            if ($sessionConfig->driver == 'File') {
                $dir = RuntimeFactory::getInstance()->getCachePath() . '/session';
                \Be\F\Util\FileSystem\Dir::rm($dir);
            }
        }

        $this->swooleHttpServer->on('request', function ($swRequest, $swResponse) {
            /**
             * @var \Swoole\Http\Response $swResponse
             */
            $swResponse->header('Server', 'be/sf', false);
            $uri = $swRequest->server['request_uri'];

            if ($uri == '/favicon.ico') {
                $swResponse->sendfile(Be::getRuntime()->getRootPath() . '/favicon.ico');
                return true;
            }

            $swRequest->request = null;
            if ($swRequest->get !== null) {
                if ($swRequest->post !== null) {
                    $swRequest->request = array_merge($swRequest->get, $swRequest->post);
                } else {
                    $swRequest->request = $swRequest->get;
                }
            } else {
                if ($swRequest->post !== null) {
                    $swRequest->request = $swRequest->post;
                }
            }

            $request = new \Be\F\Request\Driver($swRequest);
            $response = new \Be\F\Response\Driver($swResponse);

            RequestFactory::setInstance($request);
            ResponseFactory::setInstance($response);

            try {

                // 检查网站配置， 是否暂停服务
                $configSystem = Be::getConfig('System.System');

                $app = null;
                $controller = null;
                $action = null;

                // 从网址中提取出 路径
                if ($configSystem->urlRewrite) {

                    // 移除 .html
                    $lenSefSuffix = strlen($configSystem->urlSuffix);
                    if (substr($uri, -$lenSefSuffix, $lenSefSuffix) == $configSystem->urlSuffix) {
                        $uri = substr($uri, 0, strrpos($uri, $configSystem->urlSuffix));
                    }

                    // 移除结尾的 /
                    if (substr($uri, -1, 1) == '/') $uri = substr($uri, 0, -1);

                    // /{action}[/{k-v}]
                    $uris = explode('/', $uri);
                    $len = count($uris);
                    if ($len > 3) {
                        $app = $uris[1];
                        $controller = $uris[2];
                        $action = $uris[3];
                    }

                    if ($len > 4) {
                        /**
                         * 把网址按以下规则匹配
                         * /{action}/{参数名1}-{参数值1}/{参数名2}-{参数值2}/{参数名3}-{参数值3}
                         * 其中{参数名}-{参数值} 值对不限数量
                         */
                        for ($i = 4; $i < $len; $i++) {
                            $pos = strpos($uris[$i], '-');
                            if ($pos !== false) {
                                $key = substr($uris[$i], 0, $pos);
                                $val = substr($uris[$i], $pos + 1);

                                $swRequest->get[$key] = $swRequest->request[$key] = $val;
                            }
                        }
                    }
                }

                // 默认访问控制台页面
                if (!$app) {
                    $route = $request->request('route', $configSystem->home);
                    $routes = explode('.', $route);
                    if (count($routes) == 3) {
                        $app = $routes[0];
                        $controller = $routes[1];
                        $action = $routes[2];
                    } else {
                        throw new RuntimeException('路由参数（' . $route . '）无法识别！');
                    }
                }

                $request->setRoute($app, $controller, $action);

                $class = 'Be\\Sf\\App\\' . $app . '\\Controller\\' . $controller;
                if (!class_exists($class)) {
                    throw new RuntimeException('控制器 ' . $app . '/' . $controller . ' 不存在！');
                } else {
                    $instance = new $class();
                    if (method_exists($instance, $action)) {
                        $instance->$action();
                    } else {
                        throw new RuntimeException('未定义的任务：' . $action);
                    }
                }

            } catch (\Throwable $t) {
                $response->end($t->getMessage());
                Be::getLog()->emergency($t);
            }

            Be::release();
            return true;
        });

        $this->swooleServerPort = $this->swooleHttpServer->addListener($configServer->rpc_host, $configServer->rpc_port, SWOOLE_SOCK_TCP);
        $this->swooleServerPort->set(array());
        $this->swooleServerPort->on('Receive', function ($server, $fd, $reactorId, $data) {
            $calls = json_decode($data, true);
            if (!is_array($calls)) {
                $server->send($fd, json_encode([
                    'success' => false,
                    'message' => '参数非数组格式！',
                ]));
                return;
            }

            $results = [];
            foreach ($calls as $call) {
                $id = null;
                try {
                    if (!isset($call['service'])) {
                        throw new RuntimeException('参数（service）缺失！');
                    }

                    if (!isset($call['method'])) {
                        throw new RuntimeException('参数（method）缺失！');
                    }

                    if (!isset($call['params'])) {
                        throw new RuntimeException('参数（params）缺失！');
                    }

                    $service = $call['service'];
                    $method = $call['method'];
                    $params = $call['params'];

                    $service = Be::getService($service);
                    $result = $service->$method(...$params);
                    $results[] = [
                        'success' => true,
                        'message' => '',
                        'data' => $result
                    ];
                } catch (\Throwable $t) {
                    //Be::getLog()->emergency($t);
                    $results[] = [
                        'success' => false,
                        'message' => $t->getMessage(),
                    ];
                }
            }
            $server->send($fd, json_encode($results));
            //$server->close($fd);
        });

        $this->swooleHttpServer->start();
    }


    public function stop()
    {
        $this->swooleHttpServer->stop();
    }

    public function reload()
    {
        $this->swooleHttpServer->reload();
    }

    public function getSwooleHttpServer()
    {
        return $this->swooleHttpServer;
    }

    public function getSwooleServerPort()
    {
        return $this->swooleServerPort;
    }
}
