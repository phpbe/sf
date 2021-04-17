<?php
namespace Be\Sf\App\System\Config;

/**
 * @BeConfig("服务器")
 */
class Server
{

    /**
     * @BeConfigItem("Http 监听的IP地址", driver="FormItemInput")
     */
    public $host = '0.0.0.0';

    /**
     * @BeConfigItem("Http 端口号",
     *     driver="FormItemInputNumberInt",
     *     ui="return [':min' => 1];")
     */
    public $port = 80;

    /**
     * @BeConfigItem("Http Reactor线程数",
     *     driver="FormItemInputNumberInt",
     *     ui="return [':min' => 0];")
     */
    public $reactor_num = 0;

    /**
     * @BeConfigItem("Http Worker进程数",
     *     driver="FormItemInputNumberInt",
     *     ui="return [':min' => 0];")
     */
    public $worker_num = 0;

    /**
     * @BeConfigItem("Http Worker进程最大任务数",
     *     driver="FormItemInputNumberInt",
     *     ui="return [':min' => 0];")
     */
    public $max_request = 0;

    /**
     * @BeConfigItem("Http 最大连接数",
     *     driver="FormItemInputNumberInt",
     *     ui="return [':min' => 0];")
     */
    public $max_conn = 0;

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
    public $jsonRpc = true;

}
