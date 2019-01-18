<?php
/**
 * Created by IntelliJ IDEA.
 * User: luojinbo
 * Date: 2019-01-18
 * Time: 17:18
 */
namespace App\Event;

class TestEvent implements \Swoole\IFace\EventHandler
{
    public function trigger($type, $data)
    {
        var_dump($data);
    }
}