<?php
/**
 * Created by IntelliJ IDEA.
 * User: luojinbo
 * Date: 2019-01-02
 * Time: 18:26
 */
namespace Swoole\Queue;

class Redis implements \Swoole\IFace\Queue
{
    protected $redis_factory_key;
    protected $key = 'swoole:queue';

    public function __construct($config)
    {
        if (empty($config['id'])) {
            $config['id'] = 'master';
        }
        $this->redis_factory_key = $config['id'];
        if (!empty($config['key'])) {
            $this->key = $config['key'];
        }
    }

    /**
     * 出队
     * @return bool|mixed
     */
    public function pop()
    {
        global $php;
        $redis = $php->redis($this->redis_factory_key);
        $ret = $redis->lPop($this->key);
        if ($ret) {
            return unserialize($ret);
        } else {
            return false;
        }
    }

    /**
     * 入队
     * @param $data
     * @return int
     */
    public function push($data)
    {
        global $php;
        $redis = $php->redis($this->redis_factory_key);
        $ret = $redis->lPush($this->key, serialize($data));
        return $ret;
    }
}
