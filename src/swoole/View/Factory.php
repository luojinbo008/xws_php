<?php
/**
 * Created by IntelliJ IDEA.
 * User: luojinbo
 * Date: 2019-01-22
 * Time: 10:22
 */

namespace Swoole\View;

use Swoole\Core\Factory as CFactory;
class Factory
{
    /**
     * @param string $adapter
     * @return mixed
     * @throws \Swoole\Exception\NotFound
     */
    public static function getInstance($adapter = 'Json')
    {
        $className = __NAMESPACE__ . "\\Adapter\\{$adapter}";
        return CFactory::getInstance($className);
    }
}