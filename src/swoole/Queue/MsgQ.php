<?php
/**
 * 是对Linux Sysv系统消息队列的封装，单台服务器推荐使用
 * Created by IntelliJ IDEA.
 * User: luojinbo
 * Date: 2019-01-23
 * Time: 16:48
 */

namespace Swoole\Queue;

class MsgQ implements \Swoole\IFace\Queue
{
    protected $msgid;
    protected $msgtype = 1;
    protected $msg;

    public function __construct($config)
    {
        if (!empty($config['msgid'])) {
            $this->msgid = $config['msgid'];
        } else {
            $this->msgid = \ftok(__FILE__, 0);
        }

        if (!empty($config['msgtype'])) {
            $this->msgtype = $config['msgtype'];
        }
        $this->msg = \msg_get_queue($this->msgid);
    }

    public function pop()
    {
        $ret = \msg_receive($this->msg, 0, $this->msgtype, 65525, $data);
        if ($ret) {
            return $data;
        }
        return false;
    }

    public function push($data)
    {
        return \msg_send($this->msg, $this->msgtype, $data);
    }
}