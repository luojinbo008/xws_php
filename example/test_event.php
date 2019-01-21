<?php
/**
 * Created by IntelliJ IDEA.
 * User: luojinbo
 * Date: 2019-01-18
 * Time: 17:08
 */

ini_set('date.timezone','Asia/Shanghai');
define('DEBUG', 'on');
define('APPPATH', realpath(__DIR__ . '/app'));
define('ROOT_PATH', realpath(__DIR__ . '/..'));

$loader = require_once ROOT_PATH . "/vendor/autoload.php";
$loader->setPsr4("App\\", APPPATH . "/classes");

require dirname(__DIR__) . '/example/bootstrap/init.php';

go(function () {
    global $php;
    $php->event->trigger('test_event', [
        "test" => 123
    ]);

});

Swoole\Swoole::$php->event->runWorker(1, false);
