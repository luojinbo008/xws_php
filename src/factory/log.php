<?php
/**
 * Created by IntelliJ IDEA.
 * User: luojinbo
 * Date: 2019-01-07
 * Time: 16:09
 */

global $php;

$config = $php->config['log'];
if (empty($config[$php->factory_key])) {
    throw new \Swoole\Exception\Factory("log->{$php->factory_key} is not found.");
}
$conf = $config[$php->factory_key];
if (empty($conf['type'])) {global $php;
$config = $php->config['log'];
if (empty($config[$php->factory_key])) {
    throw new \Swoole\Exception\Factory("log->{$php->factory_key} is not found.");
}
$conf = $config[$php->factory_key];
if (empty($conf['type'])) {
    $conf['type'] = 'EchoLog';
}
$class = '\\Swoole\\Log\\' . $conf['type'];
$log = new $class($conf);
if (!empty($conf['level'])) {
    $log->setLevel($conf['level']);
}
return $log;
    $conf['type'] = 'EchoLog';
}
$class = '\\Swoole\\Log\\' . $conf['type'];
$log = new $class($conf);
if (!empty($conf['level'])) {
    $log->setLevel($conf['level']);
}
return $log;