<?php
namespace Be\Mf\App\System\Config;

/**
 * @BeConfig("服务器")
 */
class Server
{

    /**
     * @BeConfigItem("RPC 监听的IP地址", driver="FormItemInput")
     */
    public $rpc_host = '0.0.0.0';

    /**
     * @BeConfigItem("RPC 端口号",
     *     driver="FormItemInputNumberInt",
     *     ui="return [':min' => 1];")
     */
    public $rpc_port = 10240;

    /**
     * @BeConfigItem("RPC Reactor线程数",
     *     driver="FormItemInputNumberInt",
     *     ui="return [':min' => 0];")
     */
    public $rpc_reactor_num = 0;

    /**
     * @BeConfigItem("RPC Worker进程数",
     *     driver="FormItemInputNumberInt",
     *     ui="return [':min' => 0];")
     */
    public $rpc_worker_num = 0;

    /**
     * @BeConfigItem("RPC Worker进程最大任务数",
     *     driver="FormItemInputNumberInt",
     *     ui="return [':min' => 0];")
     */
    public $rpc_max_request = 0;

    /**
     * @BeConfigItem("RPC 最大连接数",
     *     driver="FormItemInputNumberInt",
     *     ui="return [':min' => 0];")
     */
    public $rpc_max_conn = 0;

    /**
     * @BeConfigItem("Http 监听的IP地址", driver="FormItemInput")
     */
    public $http_host = '0.0.0.0';

    /**
     * @BeConfigItem("Http 端口号",
     *     driver="FormItemInputNumberInt",
     *     ui="return [':min' => 1];")
     */
    public $http_port = 80;

    /**
     * @BeConfigItem("Http Reactor线程数",
     *     driver="FormItemInputNumberInt",
     *     ui="return [':min' => 0];")
     */
    public $http_reactor_num = 0;

    /**
     * @BeConfigItem("Http Worker进程数",
     *     driver="FormItemInputNumberInt",
     *     ui="return [':min' => 0];")
     */
    public $http_worker_num = 0;

    /**
     * @BeConfigItem("Http Worker进程最大任务数",
     *     driver="FormItemInputNumberInt",
     *     ui="return [':min' => 0];")
     */
    public $http_max_request = 0;

    /**
     * @BeConfigItem("Http 最大连接数",
     *     driver="FormItemInputNumberInt",
     *     ui="return [':min' => 0];")
     */
    public $http_max_conn = 0;

    /**
     * @BeConfigItem("启动时清空Cache", driver="FormItemSwitch")
     */
    public $clearCacheOnStart = true;

    /**
     * @BeConfigItem("启用管理模块", driver="FormItemSwitch")
     */
    public $admin = true;

    /**
     * @BeConfigItem("启用 JSON RPC", driver="FormItemSwitch")
     */
    public $jsonRpc = false;

}
