<?php
/**
 * Created by IntelliJ IDEA.
 * User: luojinbo
 * Date: 2018-12-18
 * Time: 17:43
 */

namespace Swoole\Log;


class EchoLog extends \Swoole\Core\Log implements \Swoole\IFace\Log
{
    protected $display = true;

    public function __construct($config)
    {
        if (isset($config['display']) and $config['display'] == false) {
            $this->display = false;
        }
        parent::__construct($config);
    }

    public function put($msg, $level = self::INFO)
    {
        if ($this->display) {
            $log = $this->format($msg, $level);
            if ($log) echo $log;
        }
    }
}