<?php

namespace Be\Sf;

use Be\F\Cache\CacheFactory;
use Be\F\Config\ConfigFactory;
use Be\F\Db\DbFactory;
use Be\F\Db\TableFactory;
use Be\F\Db\TablePropertyFactory;
use Be\F\Db\TupleFactory;
use Be\F\Es\EsFactory;
use Be\F\Lib\LibFactory;
use Be\F\Log\LogFactory;
use Be\F\Property\PropertyFactory;
use Be\F\Redis\RedisFactory;
use Be\F\Request\RequestFactory;
use Be\F\Response\ResponseFactory;
use Be\F\Runtime\RuntimeFactory;
use Be\F\Runtime\RuntimeException;
use Be\F\App\ServiceFactory;
use Be\F\Session\SessionFactory;


/**
 *  BE系统资源工厂
 * @package Be\Mf
 *
 */
abstract class Be
{

    /**
     * 获取运行时对象
     *
     * @return \Be\F\Runtime\Driver
     */
    public static function getRuntime()
    {
        return RuntimeFactory::getInstance();
    }

    /**
     * 获取请求对象
     *
     * @return \Be\F\Request\Driver
     */
    public static function getRequest()
    {
        return RequestFactory::getInstance();
    }

    /**
     * 获取输出对象
     *
     * @return \Be\F\Response\Driver
     */
    public static function getResponse()
    {
        return ResponseFactory::getInstance();
    }

    /**
     * 获取指定的配置文件
     *
     * @param string $name 配置文件名
     * @return mixed
     */
    public static function getConfig($name)
    {
        return ConfigFactory::getInstance($name);
    }

    /**
     * 新创建一个指定的配置文件
     *
     * @param string $name 配置文件名
     * @return mixed
     */
    public static function newConfig($name)
    {
        return ConfigFactory::newInstance($name);
    }

    /**
     * 获取日志记录器
     *
     * @return \Be\F\Log\Driver
     */
    public static function getLog()
    {
        return LogFactory::getInstance();
    }

    /**
     * 获取一个属性
     *
     * @param string $name 名称
     * @return \Be\F\Property\Driver
     * @throws RuntimeException
     */
    public static function getProperty($name)
    {
        return PropertyFactory::getInstance($name);
    }

    /**
     * 获取SESSION
     *
     * @return \Be\F\Session\Driver
     */
    public static function getSession()
    {
        return SessionFactory::getInstance();
    }

    /**
     * 获取Cache
     *
     * @return \Be\F\Cache\Driver
     */
    public static function getCache()
    {
        return CacheFactory::getInstance();
    }

    /**
     * 获取Redis对象
     *
     * @param string $name Redis名
     * @return \Be\F\Redis\Driver|\Redis
     * @throws RuntimeException
     */
    public static function getRedis($name = 'master')
    {
        return RedisFactory::getInstance($name);
    }

    /**
     * 新创建一个Redis对象
     *
     * @param string $name Redis名
     * @return \Be\F\Redis\Driver|\Redis
     * @throws RuntimeException
     */
    public static function newRedis($name = 'master')
    {
        return RedisFactory::newInstance($name);
    }

    /**
     * 获取ES对象
     *
     * @return \Elasticsearch\Client
     * @throws RuntimeException
     */
    public static function getEs()
    {
        return EsFactory::getInstance();
    }

    /**
     * 新创建一个ES对象
     *
     * @return \Elasticsearch\Client
     * @throws RuntimeException
     */
    public static function newEs()
    {
        return EsFactory::newInstance();
    }

    /**
     * 获取数据库对象
     *
     * @param string $name 数据库名
     * @return \Be\F\Db\Driver
     * @throws RuntimeException
     */
    public static function getDb($name = 'master')
    {
        return DbFactory::getInstance($name);
    }

    /**
     * 新创建一个数据库对象
     *
     * @param string $name 数据库名
     * @return \Be\F\Db\Driver
     * @throws RuntimeException
     */
    public static function newDb($name = 'master')
    {
        return DbFactory::newInstance($name);
    }

    /**
     * 获取指定的一个数据库行记灵对象
     *
     * @param string $name 数据库行记灵对象名
     * @param string $db 库名
     * @return \Be\F\Db\Tuple | mixed
     */
    public static function getTuple($name, $db = 'master')
    {
        return TupleFactory::getInstance($name, $db);
    }

    /**
     * 新创建一个数据库行记灵对象
     *
     * @param string $name 数据库行记灵对象名
     * @param string $db 库名
     * @return \Be\F\Db\Tuple | mixed
     */
    public static function newTuple($name, $db = 'master')
    {
        return TupleFactory::newInstance($name, $db);
    }

    /**
     * 获取指定的一个数据库表对象
     *
     * @param string $name 表名
     * @param string $db 库名
     * @return \Be\F\Db\Table
     */
    public static function getTable($name, $db = 'master')
    {
        return TableFactory::getInstance($name, $db);
    }

    /**
     * 新创建一个数据库表对象
     *
     * @param string $name 表名
     * @param string $db 库名
     * @return \Be\F\Db\Table
     */
    public static function newTable($name, $db = 'master')
    {
        return TableFactory::newInstance($name, $db);
    }

    /**
     * 获取指定的一个数据库表属性
     *
     * @param string $name 表名
     * @param string $db 库名
     * @return \Be\F\Db\TableProperty
     */
    public static function getTableProperty($name, $db = 'master')
    {
        return TablePropertyFactory::getInstance($name, $db);
    }

    /**
     * 获取指定的一个服务
     *
     * @param string $name 服务名
     * @return mixed
     */
    public static function getService($name)
    {
        return ServiceFactory::getInstance($name);
    }

    /**
     * 新创建一个服务
     *
     * @param string $name 服务名
     * @return mixed
     */
    public static function newService($name)
    {
        return ServiceFactory::newInstance($name);
    }

    /**
     * 获取指定的库
     *
     * @param string $name 库名，可指定命名空间，调用第三方库
     * @return mixed
     * @throws RuntimeException
     */
    public static function getLib($name)
    {
        return LibFactory::getInstance($name);
    }

    /**
     * 新创建一个指定的库
     *
     * @param string $name 库名，可指定命名空间，调用第三方库
     * @return mixed
     * @throws RuntimeException
     */
    public static function newLib($name)
    {
        return LibFactory::newInstance($name);
    }

}
