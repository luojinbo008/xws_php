<?php
/**
 * Created by IntelliJ IDEA.
 * User: luojinbo
 * Date: 2019-01-14
 * Time: 10:39
 */

return [
    'master' => [
        'serverInfo' => [
            "host"      => getenv("MYSQL_HOST") ? getenv("MYSQL_HOST") : '10.66.231.6',
            "port"      => getenv("MYSQL_PORT") ? getenv("MYSQL_PORT") : '3306',
            "user"      => getenv("MYSQL_USER") ? getenv("MYSQL_USER") : 'root',
            "password"  => getenv("MYSQL_PASSWORD") ? getenv("MYSQL_PASSWORD") : 'Sharing2017',
            "database"  => getenv("MYSQL_DATABASE") ? getenv("MYSQL_DATABASE") : 'sharing-messages',
            "charset"   => getenv("MYSQL_CHARSET") ? getenv("MYSQL_CHARSET") : 'utf8',
            'timeout'   => 10
        ],
        'conns' => getenv('MYSQL_MAX_CONN') ? intval(getenv('MYSQL_MAX_CONN')) : 10,
    ]
];