<?php
/**
 * Created by IntelliJ IDEA.
 * User: luojinbo
 * Date: 2019-03-25
 * Time: 23:06
 */

namespace Swoole\Network;


use Swoole\Core\Master;

class Event
{
    public $eventName;

    protected $processName;

    /**
     * @param $processName
     * @return mixed
     */
    public function setProcessName($processName)
    {
        return $this->processName = $processName;
    }

    /**
     * 获取进程名称
     * @return string
     */
    public function getProcessName()
    {
        if (empty($this->processName)) {
            global $argv;
            return "php {$argv[0]}";
        } else {
            return $this->processName;
        }
    }

    /**
     * @param array $setting
     */
    public function run($setting = [])
    {
        $process = new \swoole_process(function(\swoole_process $worker) {
            global $php;
            $event = $php->{$this->eventName};
            $event->onStop([$this, 'onStop']);
            $event->onStart([$this, 'onStart']);

            $event->runWorker(4, true);
        }, false, false);

        $process->start();
    }

    public function onStop($event)
    {
        Master::removePid($event->master_pid);
    }

    public function onStart($event)
    {
        Master::addPid($event->master_pid);
    }
}