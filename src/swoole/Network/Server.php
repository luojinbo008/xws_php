<?php
/**
 * Created by IntelliJ IDEA.
 * User: luojinbo
 * Date: 2019-01-07
 * Time: 18:26
 */

namespace Swoole\Network;

use Swoole\Core\Master;
use Swoole\Protocol\Request;

class Server
{
    public $protocol;
    public static $isHttp = false;

    public $host = '0.0.0.0';
    public $port;
    public $flag;

    protected $processName;
    public static $swooleMode;

    /**
     * @var \swoole_server
     */
    protected $sw;

    public static $swoole;

    /**
     * @param $processName
     * @return mixed
     */
    public function setProcessName($processName)
    {
        return $this->processName = $processName;
    }

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
     * 杀死所有进程
     * @param $name
     * @param int $signo
     * @return string
     */
    public static function killProcessByName($name, $signo = 9)
    {
        $cmd = 'ps -eaf |grep "' . $name . '" | grep -v "grep"| awk "{print $2}"|xargs kill -' . $signo;
        return exec($cmd);
    }

    /**
     * @param $host
     * @param $port
     * @param bool $ssl
     * @return Server
     */
    public static function autoCreate($host, $port, $ssl = false)
    {
        return new self($host, $port, $ssl);
    }

    /**
     * Server constructor.
     * @param $host
     * @param $port
     * @param bool $ssl
     */
    public function __construct($host, $port, $ssl = false)
    {
        if (!empty(self::$options['base'])) {
            self::$swooleMode = SWOOLE_BASE;
        } elseif (extension_loaded('swoole')) {
            self::$swooleMode = SWOOLE_PROCESS;
        }

        $this->flag = $ssl ? (SWOOLE_SOCK_TCP | SWOOLE_SSL) : SWOOLE_SOCK_TCP;
        $this->host = $host;
        $this->port = $port;
        $this->runtimeSetting = [
            'backlog' => 128,        // listen backlog
        ];
    }

    /**
     *
     */
    public function daemonize()
    {
        $this->runtimeSetting['daemonize'] = 1;
    }

    /**
     * @param $serv
     */
    public function onMasterStart($serv)
    {
        \Swoole\Core\Console::setProcessName($this->getProcessName()
            . ': master -host=' . $this->host . ' -port=' . $this->port);
        Master::addPid($serv->master_pid);
        if (method_exists($this->protocol, 'onMasterStart')) {
            $this->protocol->onMasterStart($serv);
        }
    }

    /**
     * @param $serv
     */
    public function onMasterStop($serv)
    {
        Master::removePid($serv->master_pid);
        if (method_exists($this->protocol, 'onMasterStop')) {
            $this->protocol->onMasterStop($serv);
        }
    }

    /**
     *
     */
    public function onManagerStop()
    {

    }

    /**
     * @param $serv
     * @param $worker_id
     */
    public function onWorkerStart($serv, $worker_id)
    {
        /**
         * 清理Opcache缓存
         */
        if (function_exists('opcache_reset')) {
            opcache_reset();
        }
        /**
         * 清理APC缓存
         */
        if (function_exists('apc_clear_cache')) {
            apc_clear_cache();
        }

        if ($worker_id >= $serv->setting['worker_num']) {
            \Swoole\Core\Console::setProcessName($this->getProcessName() . ': task');
        } else {
            \Swoole\Core\Console::setProcessName($this->getProcessName() . ': worker');
        }
        if (method_exists($this->protocol, 'onStart')) {
            $this->protocol->onStart($serv, $worker_id);
        }
        if (method_exists($this->protocol, 'onWorkerStart')) {
            $this->protocol->onWorkerStart($serv, $worker_id);
        }
    }

    /**
     * @param array $setting
     */
    public function run($setting = [])
    {
        if (self::$isHttp) {
            $this->sw = new \swoole_http_server($this->host, $this->port, self::$swooleMode, $this->flag);
        } else {
            $this->sw = new \swoole_server($this->host, $this->port, self::$swooleMode, $this->flag);
        }

        $this->runtimeSetting = array_merge($this->runtimeSetting, $setting);

        $this->sw->set($this->runtimeSetting);
        $this->sw->on('ManagerStart', function ($serv) {
            \Swoole\Core\Console::setProcessName($this->getProcessName() . ': manager');
        });
        $this->sw->on('Start', [$this, 'onMasterStart']);
        $this->sw->on('Shutdown', [$this, 'onMasterStop']);
        $this->sw->on('ManagerStop', [$this, 'onManagerStop']);
        $this->sw->on('WorkerStart', [$this, 'onWorkerStart']);

        if (is_callable([$this->protocol, 'onConnect'])) {
            $this->sw->on('Connect', [$this->protocol, 'onConnect']);
        }
        if (is_callable([$this->protocol, 'onClose'])) {
            $this->sw->on('Close', [$this->protocol, 'onClose']);
        }

        if (self::$isHttp) {
            $this->sw->on('Request', [$this->protocol, 'onRequest']);
        } else {
            $this->sw->on('Receive', [$this->protocol, 'onReceive']);
        }

        if (is_callable([$this->protocol, 'WorkerStop'])) {
            $this->sw->on('WorkerStop', [$this->protocol, 'WorkerStop']);
        }

        if (is_callable([$this->protocol, 'onTask'])) {
            $this->sw->on('Task', [$this->protocol, 'onTask']);
            $this->sw->on('Finish', [$this->protocol, 'onFinish']);
        }
        self::$swoole = $this->sw;
        $this->sw->start();
    }

    /**
     * @return mixed
     */
    public function shutdown()
    {
        return $this->sw->shutdown();
    }

    /**
     * @param $client_id
     * @return mixed
     */
    public function close($client_id)
    {
        return $this->sw->close($client_id);
    }

    /**
     * @param $protocol
     * @throws \Exception
     */
    public function setProtocol($protocol)
    {
        if (!($protocol instanceof \Swoole\IFace\Protocol)) {
            throw new \Exception("The protocol is not instanceof \\Swoole\\IFace\\Protocol");
        }

        if ($protocol instanceof \Swoole\Protocol\Adapter\HttpServer) {
            self::$isHttp = true;
            Request::setHttpServer(1);
        }
        $this->protocol = $protocol;
        $protocol->server = $this;
    }

    /**
     * @param $client_id
     * @param $data
     * @return mixed
     */
    public function send($client_id, $data)
    {
        return $this->sw->send($client_id, $data);
    }

    /**
     * @param $data
     * @param $func
     */
    public static function task($data, $func)
    {
        $params = [
            'func' => $func,
            'data' => $data,
        ];
        self::$swoole->task($params);
    }


    /**
     * @param $func
     * @param $params
     * @return mixed
     */
    public function __call($func, $params)
    {
        return call_user_func_array([$this->sw, $func], $params);
    }
}

class ServerOptionException extends \Exception
{

}