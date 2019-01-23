<?php
/**
 * Created by IntelliJ IDEA.
 * User: luojinbo
 * Date: 2019-01-21
 * Time: 13:15
 */
ini_set('date.timezone','Asia/Shanghai');
define('DEBUG', 'on');
define('APPPATH', realpath(__DIR__ . '/../app'));
define('ROOT_PATH', realpath(__DIR__ . '/../..'));

$loader = require_once ROOT_PATH . "/vendor/autoload.php";
$loader->setPsr4("App\\", APPPATH . "/classes");

require dirname(__DIR__) . '/../example/bootstrap/init.php';

Swoole\Config::$debug = false;

// 设置PID文件的存储路径
Swoole\Network\Server::setPidFile(__DIR__ . '/http_svr.pid');


/**
 * 显示Usage界面
 * php app_server.php start|stop|reload
 */
Swoole\Network\Server::start(function () {


    $AppSvr = Swoole\Protocol\Factory::getInstance("HttpServer");

    $AppSvr->setLogger(new Swoole\Log\EchoLog(true)); // Loggers
    $server = Swoole\Network\Server::autoCreate('0.0.0.0', 8888);
    $server->setProtocol($AppSvr);

    //$server->daemonize();  // 作为守护进程
    $server->run([
        'worker_num' => 8,
        'task_worker_num' => 100,
        'max_request' => 5000,
        'log_file' => '/tmp/swoole.log'
    ]);
});
