<?php
/**
 * Created by IntelliJ IDEA.
 * User: luojinbo
 * Date: 2019-01-02
 * Time: 13:58
 */

namespace Swoole\IFace;


interface Queue
{
    public function push($data);
    public function pop();
}