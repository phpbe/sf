<?php
namespace Be\Sf\App\System\Config;

/**
 * @BeConfig("管理")
 */
class Admin
{

    /**
     * @BeConfigItem("用户名", driver="FormItemInput")
     */
    public $username = 'admin';

    /**
     * @BeConfigItem("官码（MD5）", driver="FormItemInput")
     */
    public $password = '21232f297a57a5a743894a0e4a801fc3';

}
