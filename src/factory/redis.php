<?php
/**
 * Created by IntelliJ IDEA.
 * User: luojinbo
 * Date: 2019-01-04
 * Time: 14:24
 */

global $php;


$config = $php->config['redis'][$php->factory_key];
if (empty($config) or empty($config['serverInfo']['host'])) {
    throw new Exception("require redis[$php->factory_key] config.");
}

if (empty($config['serverInfo']['port'])) {
    $config['serverInfo']['port'] = 6379;
}

if (empty($config['serverInfo']['timeout'])) {
    $config['serverInfo']['timeout'] = 0.5;
}

$redis = Swoole\Component\Redis::init($config['serverInfo'], $config['conns'] ?? 100);

return $redis;