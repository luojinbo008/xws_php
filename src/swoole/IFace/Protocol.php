<?php
/**
 * Created by IntelliJ IDEA.
 * User: luojinbo
 * Date: 2019-01-07
 * Time: 19:00
 */

namespace Swoole\IFace;


interface Protocol
{
    public function onStart($server);
    public function onConnect($server, $client_id, $from_id);
    public function onClose($server, $client_id, $from_id);
    public function onShutdown($server);
}