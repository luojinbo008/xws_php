<?php
/**
 * Created by IntelliJ IDEA.
 * User: luojinbo
 * Date: 2019-01-07
 * Time: 16:15
 */
return [
    "master" => [
        "enable_cache" => false,
        "type"  => getenv('APP_LOG_TYPE') ? getenv('APP_LOG_TYPE') : 'FileLog',
        "dir"   => getenv('APP_LOG_PATH') ? getenv('APP_LOG_PATH')
            : '/data/release/sharing-notice-alert/storage/logs',
        "date"  => true,
        "leave" => \Swoole\Log::INFO
    ]
];
