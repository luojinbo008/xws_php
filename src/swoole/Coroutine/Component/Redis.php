<?php
/**
 * Created by IntelliJ IDEA.
 * User: luojinbo
 * Date: 2019-01-04
 * Time: 12:01
 */

namespace Swoole\Coroutine\Component;

use Swoole\Coroutine\Redis as CoRedis;

class Redis extends Base
{
    protected $type = 'redis';

    /**
     * @return bool|mixed|CoRedis
     */
    public function create()
    {
        $redis = new CoRedis($this->config);
        if ($redis->connect($this->config['host'], $this->config['port']) === false) {
            return false;
        }

        if (!empty($this->config['pwd'])
            && $redis->auth($this->config['pwd']) === false) {
            return false;
        }

        if (isset($this->config['database'])) {
            if (!$redis->select(intval($this->config['database']))) {
                return false;
            }
        }

        return $redis;
    }

    /**
     * @param $name
     * @param $arguments
     * @return mixed
     */
    public function __call($name, $arguments)
    {
        // TODO: Implement __call() method.
        $redis = $this->_getObject();
        if (!$redis->connected) {
            $redis = $this->create();
        }
        $res = call_user_func_array([$redis, $name], $arguments);
        $this->_createObject($redis);
        return $res;

    }

}
