<?php
/**
 * Created by IntelliJ IDEA.
 * User: luojinbo
 * Date: 2018-12-18
 * Time: 17:13
 */

namespace Swoole\IFace;

interface Log
{
    /**
     * 写入日志
     * @param $msg
     * @param int $type
     * @return mixed
     */
    public function put($msg, $type = \Swoole\Log::INFO);
}