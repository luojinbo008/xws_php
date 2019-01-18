<?php
namespace Swoole\Platform;

class Linux
{
    /**
     * @param $pid
     * @param $signo
     * @return bool
     */
    public function kill($pid, $signo)
    {
        return posix_kill($pid, $signo);
    }

    /**
     * @return int
     */
    public function fork()
    {
        return pcntl_fork();
    }
}