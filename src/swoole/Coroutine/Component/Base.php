<?php
/**
 * Created by IntelliJ IDEA.
 * User: luojinbo
 * Date: 2019-01-02
 * Time: 18:42
 */

namespace Swoole\Coroutine\Component;

use Swoole;

abstract class Base
{
    /**
     * @var \Swoole\Coroutine\Channel
     */
    protected $pool;
    protected $config;

    /**
     * Base constructor.
     * @param $config
     * @param int $size
     * @throws Swoole\Exception\RuntimeException
     */
    public function __construct($config, $size = 10)
    {
        $this->config = $config;
        $this->pool = new \Swoole\Coroutine\Channel($size);
        for ($i = 0; $i < $size; $i++) {
            $obj = $this->create();
            if (false === $obj) {
                throw new \Swoole\Exception\RuntimeException("failed to connect " . $this->type . " server.");
            }
            $this->_createObject($obj);
        }

    }

    /**
     * @param $res
     */
    public function _createObject($res)
    {
        $this->pool->push($res);
    }

    /**
     * @return mixed
     */
    public function _getObject()
    {
        return $this->pool->pop();
    }

    /**
     * @return mixed
     */
    public abstract function create();
}
