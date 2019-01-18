<?php
/**
 * Created by IntelliJ IDEA.
 * User: luojinbo
 * Date: 2019-01-07
 * Time: 18:34
 */

namespace Swoole\Server;


interface Driver
{
    public function run($setting);
    public function send($client_id, $data);
    public function close($client_id);
    public function shutdown();
    public function setProtocol($protocol);
}