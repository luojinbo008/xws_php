<?php
/**
 * Created by IntelliJ IDEA.
 * User: luojinbo
 * Date: 2019-01-02
 * Time: 18:22
 */
$event['master'] = [
    'type' => Swoole\Queue\Redis::class,
    'async' => true,
];
return $event;