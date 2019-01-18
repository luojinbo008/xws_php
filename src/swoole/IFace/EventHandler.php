<?php
/**
 * Created by IntelliJ IDEA.
 * User: luojinbo
 * Date: 2019-01-02
 * Time: 14:02
 */

namespace Swoole\IFace;

interface EventHandler
{
    public function trigger($type, $data);
}