<?php
/**
 * Created by IntelliJ IDEA.
 * User: luojinbo
 * Date: 2019-01-14
 * Time: 11:09
 */

namespace Swoole\Component;


class MySQL
{
    protected static $init;

    /**
     * @param array $config
     * @param int $size
     * @return \Swoole\Coroutine\Component\MySQL
     * @throws \Swoole\Exception\RuntimeException
     */
    public static function init(array $config, $size = 10)
    {
        if (self::$init == null) {
            self::$init = new \Swoole\Coroutine\Component\MySQL($config, $size);
        }
        return self::$init;
    }
}