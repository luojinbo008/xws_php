<?php
/**
 * Created by PhpStorm.
 * User: luojinbo
 * Date: 2018/11/28
 * Time: 10:24
 */

namespace Swoole;

use Swoole\Exception\NotFound;

class Swoole
{
    public static $app_path;

    /**
     * 对象池
     * @var array
     */
    protected $objects = [];

    /**
     * 实例
     * @var Swoole
     */
    public static $php;

    public $server;

    /**
     * Swoole constructor.
     * @param string $appDir
     */
    private function __construct($appDir = '')
    {
        if (!empty($appDir)) {
            self::$app_path = $appDir;
        } elseif (defined('APPPATH')) {
            self::$app_path = APPPATH;
        }
        if (empty(self::$app_path)) {
            throw new NotFound("define APPPATH");
        }

        $this->config = new Config;
        $this->config->setPath(self::$app_path . '/config');
    }

    /**
     * @return Swoole
     */
    public static function getInstance()
    {
        if (!self::$php) {
            self::$php = new Swoole;
        }
        return self::$php;
    }

    /**
     * 加载内置的Swoole模块
     * @param $module
     * @param $id
     * @throws NotFound
     * @return mixed
     */
    protected function loadModule($module, $id = 'master')
    {
        $key = $module . '_' . $id;
        if (empty($this->objects[$key])) {
            $this->factory_key = $id;
            $user_factory_file = self::$app_path . '/factory/' . $module . '.php';

            // 尝试从用户工厂构建对象
            if (is_file($user_factory_file)) {
                $object = require $user_factory_file;
            } else {
                // 系统默认
                get_factory_file: $system_factory_file = LIBPATH . '/factory/' . $module . '.php';
                // 组件不存在，抛出异常
                if (!is_file($system_factory_file)) {
                    throw new NotFound("module [$module] not found.");
                }
                $object = require $system_factory_file;
            }
            $this->objects[$key] = $object;
        }
        return $this->objects[$key];
    }

    /**
     * @param $lib_name
     * @return mixed
     */
    public function __get($lib_name)
    {
        // 如果不存在此对象，从工厂中创建一个
        if (empty($this->$lib_name)) {
            // 载入组件
            $this->$lib_name = $this->loadModule($lib_name);
        }
        return $this->$lib_name;
    }

    /**
     * @param $func
     * @param $param
     * @return mixed
     */
    public function __call($func, $param)
    {
        if (empty($param[0]) or !is_string($param[0])) {
            throw new \Exception("module name cannot be null.");
        }
        return $this->loadModule($func, $param[0]);

    }
}