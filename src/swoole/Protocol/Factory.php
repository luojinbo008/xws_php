<?php
/**
 * Created by IntelliJ IDEA.
 * User: luojinbo
 * Date: 2019-01-21
 * Time: 19:57
 */

namespace Swoole\Protocol;
use Swoole\Core\Factory as CFactory;

class Factory
{
    /**
     * @param string $adapter
     * @return mixed
     * @throws \Swoole\Exception\NotFound
     */
    public static function getInstance($adapter = 'HttpServer')
    {
        $className = __NAMESPACE__ . "\\Adapter\\{$adapter}";
        return CFactory::getInstance($className);
    }
}