<?php
/**
 * Created by PhpStorm.
 * User: luojinbo
 * Date: 2018/11/28
 * Time: 10:20
 */
if (PHP_OS == 'WINNT') {
    die("windows system not access this server！");
}

define("LIBPATH", __DIR__);

global $php;

$php = Swoole\Swoole::getInstance();