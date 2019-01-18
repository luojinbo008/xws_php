<?php
/**
 * Created by IntelliJ IDEA.
 * User: luojinbo
 * Date: 2019-01-08
 * Time: 12:03
 */

namespace Swoole\Protocol;


class HttpServer extends Base implements \Swoole\IFace\Protocol
{
    protected $config = [];

    protected $requests;
    protected $swoole_server;

    protected $document_root;

    /**
     * HttpServer constructor.
     * @param array $config
     */
    public function __construct($config = [])
    {
        $this->config = $config;
    }

    /**
     * @param $serv
     * @param int $worker_id
     */
    public function onStart($serv, $worker_id = 0)
    {
        if (isset($this->config['server']['user'])) {
            \Swoole\Console::changeUser($this->config['server']['user']);
        }

        $this->swoole_server = $serv;
        \Swoole\Swoole::$php->server = $this;

        set_error_handler([$this, 'onErrorHandle'], E_USER_ERROR);
        register_shutdown_function([$this, 'onErrorShutDown']);
    }

    public function setDocumentRoot($path)
    {
        $this->document_root = $path;
    }

    /**
     * @return \swoole_server
     */
    public function getSwooleServer()
    {
        return $this->swoole_server;
    }

    /**
     * @param $serv
     */
    public function onShutdown($serv)
    {

    }

    /**
     * @param $serv
     * @param $client_id
     * @param $from_id
     */
    public function onConnect($serv, $client_id, $from_id)
    {
        $this->log("Event: client[#$client_id@$from_id] connect");
    }

    /**
     * @param $serv
     * @param $client_id
     * @param $from_id
     */
    public function onClose($serv, $client_id, $from_id)
    {
        $this->log("Event: client[#$client_id@$from_id] close");
    }

    /**
     * 捕获set_error_handle错误
     * @param $errno
     * @param $errstr
     * @param $errfile
     * @param $errline
     */
    public function onErrorHandle($errno, $errstr, $errfile, $errline)
    {
        $error = [
            'message' => $errstr,
            'file' => $errfile,
            'line' => $errline,
        ];
        $this->error($error);
    }

    /**
     * 捕获register_shutdown_function错误
     */
    public function onErrorShutDown()
    {
        $error = error_get_last();
        if (!isset($error['type'])) return;
        switch ($error['type']) {
            case E_ERROR :
            case E_PARSE :
            case E_USER_ERROR :
            case E_CORE_ERROR :
            case E_COMPILE_ERROR :
                break;
            default:
                return;
        }
        $this->error($error);
    }

    /**
     * 错误显示
     * @param $error
     */
    private function error($error)
    {
        $this->log(json_encode($error, true));
    }

    /**
     * 处理请求
     * @param $request
     * @return \Http\Swoole\Response
     */
    public function onRequest(\Swoole\Http\Request $request, \Swoole\Http\Response $response)
    {
        // 请求路径
        if ($request->server['request_uri'][strlen($request->server['request_uri']) - 1] == '/') {
            $request->server['request_uri'] .= $this->config['request']['default_page'] ?? 'default';
        }

        $path = $this->document_root . $request->server['request_uri'];

        $len = strrpos($path, '.');
        if ($len) {
            $path = substr($path, 0, strrpos($path, '.'));
        }

        $path .= '.php';


        // 目前json 格式
        $response->header('Content-type', 'application/json');

        if (is_file($path)) {
            try {
                ob_start();

                $data = include $path;

                ob_end_clean();
                if (!is_array($data)) {
                    throw new \Exception("server response err");
                }

                $response->status(200);
                $response->end(json_encode($data, true));
            } catch (\Exception $e) {
                $response->status(500);
                $response->end($e->getMessage());
            }
        } else {
            $response->status(404);
            $response->end('Not Found');
        }
        $this->server->close($request->fd);
    }

    /**
     * @param \swoole_server $serv
     * @param $task_id
     * @param $from_id
     * @param $data
     * @return int
     */
    public function onTask(\swoole_server $serv, $task_id, $from_id, $data)
    {
        $this->log("task start" );
        return call_user_func_array($data['func'], $data['data']);
    }

    public function onFinish()
    {
        $this->log("task finish");
    }
}
