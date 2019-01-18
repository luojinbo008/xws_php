<?php
/**
 * Created by IntelliJ IDEA.
 * User: luojinbo
 * Date: 2019-01-04
 * Time: 17:21
 */

namespace Swoole\Component;


class Redis
{
    protected static $init;

    /**
     * @param array $config
     * @param int $size
     * @return \Swoole\Coroutine\Component\Redis
     * @throws \Swoole\Exception\RuntimeException
     */
    public static function init(array $config, $size = 10)
    {
        if (self::$init == null) {
            self::$init = new \Swoole\Coroutine\Component\Redis($config, $size);
        }
        return self::$init;
    }
}