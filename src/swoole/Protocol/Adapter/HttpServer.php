<?php
/**
 * Created by IntelliJ IDEA.
 * User: luojinbo
 * Date: 2019-01-08
 * Time: 12:03
 */

namespace Swoole\Protocol\Adapter;

use Swoole\Common\Route;
use Swoole\Protocol\Request;
use Swoole\Protocol\Response;

class HttpServer extends Base implements \Swoole\IFace\Protocol
{
    protected $config = [];

    protected $requests;
    protected $swoole_server;

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
            \Swoole\Core\Console::changeUser($this->config['server']['user']);
        }

        $this->swoole_server = $serv;
        \Swoole\Swoole::$php->server = $this;

        set_error_handler([$this, 'onErrorHandle'], E_USER_ERROR);
        register_shutdown_function([$this, 'onErrorShutDown']);
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
        $clientInfo = $serv->getClientInfo($client_id);
        $logInfo = sprintf("client_ip[#%s:%s] client[#%s@%s] connect", $clientInfo['remote_ip'],
            $clientInfo['remote_port'], $client_id, $from_id);
        $this->log($logInfo);
    }

    /**
     * @param $serv
     * @param $client_id
     * @param $from_id
     */
    public function onClose($serv, $client_id, $from_id)
    {
        $clientInfo = $serv->getClientInfo($client_id);
        $logInfo = sprintf("client_ip[#%s:%s] client[#%s@%s] close", $clientInfo['remote_ip'],
            $clientInfo['remote_port'], $client_id, $from_id);
        $this->log($logInfo);
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
            'errno' => $errno,
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
        $pathInfo = $request->server['request_uri'];
        $client_ip = $request->server['remote_addr'];
        $client_port = $request->server['remote_port'];
        $request_method = $request->server['request_method'];

        // 日志
        $logInfo = sprintf("client_ip[#%s:%s] path [#%s] method[#%s]",
            $client_ip, $client_port, $pathInfo, $request_method);

        $this->log->info($logInfo);

        $params = [];
        try {
            $projectConfig = \Swoole\Swoole::$php->config['project'];
            $ctrlName = $projectConfig['default_ctrl_name'] ?? 'DefaultController';
            $methodName = $projectConfig['default_method_name'] ?? 'notFound';

            if (!empty($pathInfo) && '/' !== $pathInfo) {
                $routeMap = Route::match(\Swoole\Swoole::$php->config['route'] ?? false, $pathInfo);

                if (is_array($routeMap)) {
                    $ctrlName = \str_replace('/', '\\', $routeMap[0]);
                    $methodName = $routeMap[1];
                    if (!empty($routeMap[2]) && is_array($routeMap[2])) {
                        // 数优先
                        $params = $params + $routeMap[2];
                    }
                }
            }

            // 基本数据初始化
            Request::init($ctrlName, $methodName, $params);

            // set headers
            Request::addHeaders($request->header);

            // set request
            Request::setRequest($request);

            // set response
            Response::setResponse($response);
            $view = \Swoole\Core\Route::route();
            $response->status(200);
            $response->end($view);
        } catch (\Exception $e) {
            $response->status(500);
            $this->error([
                'errno' => $e->getCode(),
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);
            $response->end("server is Error");
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
        $this->log("task start");
        return call_user_func_array($data['func'], $data['data']);
    }

    public function onFinish()
    {
        $this->log("task finish");
    }
}
