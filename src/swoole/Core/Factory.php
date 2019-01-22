<?php
/**
 * Created by IntelliJ IDEA.
 * User: luojinbo
 * Date: 2019-01-21
 * Time: 17:41
 */

namespace Swoole\Core;


use Swoole\Exception\NotFound;

class Factory
{
    private static $instances = [];

    /**
     * @param $className
     * @param null $params
     * @return mixed
     * @throws NotFound
     */
    public static function getInstance($className, $params = null)
    {
        $keyName = $className;

        if (!empty($params['_prefix'])) {
            $keyName .= $params['_prefix'];
        }
        if (isset(self::$instances[$keyName])) {
            return self::$instances[$keyName];
        }
        if (!\class_exists($className)) {
            throw new NotFound("no class {$className}");
        }
        if (empty($params)) {
            self::$instances[$keyName] = new $className();
        } else {
            self::$instances[$keyName] = new $className($params);
        }
        return self::$instances[$keyName];
    }
}