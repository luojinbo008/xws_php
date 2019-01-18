<?php
/**
 * Created by IntelliJ IDEA.
 * User: luojinbo
 * Date: 2019-01-07
 * Time: 21:52
 */

namespace Swoole\Protocol;

/**
 * 协议基类，实现一些公用的方法
 * @package Swoole\Protocol
 */
abstract class Base implements \Swoole\IFace\Protocol
{
    public $default_port;
    public $default_host;

    /**
     * @var \Swoole\IFace\Log
     */
    public $log;

    /**
     * @var \Swoole\Server
     */
    public $server;

    /**
     * @var array
     */
    protected $clients;

    /**
     * 设置Logger
     * @param $log
     */
    public function setLogger($log)
    {
        $this->log = $log;
    }

    /**
     * @param $array
     */
    public function run($array)
    {
        $this->server->run($array);
    }

    /**
     *
     */
    public function daemonize()
    {
        $this->server->daemonize();
    }

    /**
     * 打印Log信息
     * @param $msg
     * @param string $type
     */
    public function log($msg)
    {
        $this->log->info($msg);
    }

    /**
     * @param $task
     * @param int $dstWorkerId
     * @param null $callback
     */
    public function task($task, $dstWorkerId = -1, $callback = null)
    {
        $this->server->task($task, $dstWorkerId = -1, $callback);
    }

    /**
     * @param $server
     */
    public function onStart($server)
    {

    }

    /**
     * @param $server
     * @param $client_id
     * @param $from_id
     */
    public function onConnect($server, $client_id, $from_id)
    {

    }

    /**
     * @param $server
     * @param $client_id
     * @param $from_id
     */
    public function onClose($server, $client_id, $from_id)
    {

    }

    /**
     * @param $server
     */
    public function onShutdown($server)
    {

    }
}