<?php
/**
 * Created by IntelliJ IDEA.
 * User: luojinbo
 * Date: 2019-01-07
 * Time: 18:33
 */

namespace Swoole\Server;


namespace Swoole\Server;

use Swoole;

abstract class Base implements Driver
{
    protected static $options = [];
    public $setting = [];

    public $protocol;
    public $host = '0.0.0.0';
    public $port;
    public $timeout;

    public $runtimeSetting;

    public $buffer_size = 8192;
    public $write_buffer_size = 2097152;
    public $server_block = 0;   // 0 block,1 noblock
    public $client_block = 0;   // 0 block,1 noblock

    // 最大连接数
    public $max_connect = 1000;
    public $client_num = 0;

    // 客户端socket列表
    public $client_sock;
    public $server_sock;

    /**
     * 文件描述符
     * @var array
     */
    public $fds = [];

    protected $processName;


    /**
     * 获取进程名称
     * @return string
     */
    public function getProcessName()
    {
        if (empty($this->processName)) {
            global $argv;
            return "php {$argv[0]}";
        } else {
            return $this->processName;
        }
    }

    /**
     * 设置通信协议
     * @param $protocol
     * @throws \Exception
     */
    public function setProtocol($protocol)
    {
        if (!($protocol instanceof Swoole\IFace\Protocol)) {
            throw new \Exception("The protocol is not instanceof \\Swoole\\IFace\\Protocol");
        }
        $this->protocol = $protocol;
        $protocol->server = $this;
    }

    /**
     * 设置选项
     * @param $key
     * @param $value
     */
    public static function setOption($key, $value)
    {
        self::$options[$key] = $value;
    }


    public abstract function run($setting);

    /**
     * 发送数据到客户端
     * @param $client_id
     * @param $data
     * @return bool
     */
    public abstract function send($client_id, $data);

    /**
     * 关闭连接
     * @param $client_id
     * @return mixed
     */
    public abstract function close($client_id);

    /**
     * @return mixed
     */
    public abstract function shutdown();
}

interface TCP_Server_Driver
{
    public function run($num = 1);

    public function send($client_id, $data);

    public function close($client_id);

    public function shutdown();

    public function setProtocol($protocol);
}
