<?php
/**
 * Created by IntelliJ IDEA.
 * User: luojinbo
 * Date: 2019-01-21
 * Time: 17:24
 */

namespace Swoole\Protocol;


class Request
{
    private static $_params;
    private static $_ctrl = 'IndexController';
    private static $_method = 'index';


    /**
     * @param $ctrl
     * @param $method
     * @param array $params
     * @throws \Exception
     * @desc 请求初始化
     */
    public static function init($ctrl, $method, array $params)
    {
        if ($ctrl) {
            self::$_ctrl = $ctrl;
        } else {
            self::$_ctrl = \Swoole\Swoole::$php->config['project']['default_ctrl_name'] ?? self::$_ctrl;
        }
        if ($method) {
            self::$_method = $method;
        } else {
            self::$_method = \Swoole\Swoole::$php->config['project']['default_method_name'] ?? self::$_method;
        }
        self::$_params = $params;
        if (!is_string(self::$_ctrl) || !is_string(self::$_method)) {
            throw new \Exception('ctrl or method no string');
        }
    }

    /**
     * @return string
     */
    public static function getMethod()
    {
        return self::$_method;
    }

    /**
     * @return string
     */
    public static function getCtrl()
    {
        return self::$_ctrl;
    }


}