<?php
/**
 * Created by IntelliJ IDEA.
 * User: luojinbo
 * Date: 2019-01-14
 * Time: 11:07
 */

global $php;
$config = $php->config['db'][$php->factory_key];
if (empty($config) or empty($config['serverInfo']['host'])) {
    throw new Exception("require db[$php->factory_key] config.");
}

if (empty($config['serverInfo']['port'])) {
    $config['serverInfo']['port'] = 3306;
}

if (empty($config['serverInfo']['timeout'])) {
    $config['serverInfo']['timeout'] = 0.5;
}

$mysql = Swoole\Component\MySQL::init($config['serverInfo'], $config['conns'] ?? 100);

return $mysql;
