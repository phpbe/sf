<?php

namespace Be\Sf\App\JsonRpc\Service;

use Be\F\App\ServiceException;
use Be\Sf\Be;


/**
 * 记录日志
 *
 * Class Log
 * @package Be\Mf\App\JsonRpc\Service
 */
class Log
{

    /**
     * 访问日志
     *
     * @param $requestStr
     * @param $responseStr
     * @throws \Be\F\Db\DbException
     * @throws \Be\F\Runtime\RuntimeException
     */
    public function accessLog($requestStr, $responseStr)
    {
        $path = Be::getRuntime()->getDataPath() . '/JsonRpc/access_log/' . date('Y/m/d');
        $dir = dirname($path);
        if (!file_exists($dir)) {
            mkdir($dir, 0755, true);
            chmod($dir, 0755);
        }

        $data = date('Y-m-d H:i:s') .':' . "\n";
        $data .= $requestStr . "\n";
        $data .= $responseStr . "\n\n";

        file_put_contents($path, $data, FILE_APPEND);
    }

    /**
     * 错误日志
     *
     * @param $requestStr
     * @param $responseStr
     * @throws \Be\F\Db\DbException
     * @throws \Be\F\Runtime\RuntimeException
     */
    public function errorLog($requestStr, $responseStr)
    {
        $path = Be::getRuntime()->getDataPath() . '/JsonRpc/error_log/' . date('Y/m/d');
        $dir = dirname($path);
        if (!file_exists($dir)) {
            mkdir($dir, 0755, true);
            chmod($dir, 0755);
        }

        $data = date('Y-m-d H:i:s') .':' . "\n";
        $data .= $requestStr . "\n";
        $data .= $responseStr . "\n\n";

        file_put_contents($path, $data, FILE_APPEND);
    }
}
