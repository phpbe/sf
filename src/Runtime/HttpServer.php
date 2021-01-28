<?php

namespace Be\Sf\Runtime;

use Be\F\Db\DbFactory;
use Be\F\Redis\RedisFactory;
use Be\F\Request\RequestFactory;
use Be\F\Response\ResponseFactory;
use Be\F\Runtime\RuntimeException;
use Be\Sf\Be;

class HttpServer
{

    /**
     * @var \Swoole\Http\Server
     */
    private $server = null;

    public function __construct()
    {
    }


    public function start()
    {
        if ($this->server !== null) {
            return;
        }

        \Co::set(['hook_flags' => SWOOLE_HOOK_ALL]);

        // 检查网站配置， 是否暂停服务
        $configSystem = Be::getConfig('System.System');
        date_default_timezone_set($configSystem->timezone);

        $this->server = new \Swoole\Http\Server("0.0.0.0", 80);
        $this->server->set(['enable_coroutine' => true]);

        // 初始化数据库，Redis连接池
        DbFactory::init();
        RedisFactory::init();

        $this->server->on('request', function ($swRequest, $swResponse) {
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
                Be::getLogger()->emergency($t);
            }

            Be::release();
            return true;
        });

        $this->server->start();
    }


    public function stop()
    {
        $this->server->stop();
    }


    public function reload()
    {
        $this->server->reload();
    }

}
