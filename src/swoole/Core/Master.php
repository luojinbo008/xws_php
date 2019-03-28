<?php
/**
 * Created by IntelliJ IDEA.
 * User: luojinbo
 * Date: 2019-03-26
 * Time: 10:29
 */

namespace Swoole\Core;

class Master
{
    public static $pidFile;

    protected static $beforeStopCallback;
    protected static $beforeReloadCallback;

    /**
     * 显示命令行指令
     * @param $startFunctions
     * @throws \Exception
     */
    public static function start(...$startFunctions)
    {
        if (empty(self::$pidFile)) {
            throw new \Exception("require pidKey.");
        }

        // todo 所有pid
        $server_pid = self::getPids();
        global $argv;
        if (empty($argv[1]) or isset($opt['help'])) {
            goto usage;

        } elseif ($argv[1] == 'reload') {
            if (empty($server_pid)) {
                exit("Server is not running");
            }
            if (self::$beforeReloadCallback) {
                call_user_func(self::$beforeReloadCallback);
            }
            foreach ($server_pid as $pid) {
                \Swoole\Swoole::$php->os->kill($pid, SIGUSR1);
            }
            exit;
        } elseif ($argv[1] == 'stop') {
            if (empty($server_pid)) {
                exit("Server is not running\n");
            }
            if (self::$beforeStopCallback) {
                call_user_func(self::$beforeStopCallback);
            }
            foreach ($server_pid as $pid) {
                \Swoole\Swoole::$php->os->kill($pid, SIGTERM);
            }
            exit;
        } elseif ($argv[1] == 'start') {

            // 已存在ServerPID，并且进程存在
            foreach ($server_pid as $pid) {
                if (\Swoole\Swoole::$php->os->kill($pid, 0)) {
                    exit("Server is already running.\n");
                }
            }

        } else {
            usage:
            echo ("php {$argv[0]} start|stop|reload\n");
            exit("\n");
        }

        foreach ($startFunctions as $startFunction) {
            $startFunction();
        }
    }

    /**
     * @param callable $function
     */
    public static function beforeStop(callable $function)
    {
        self::$beforeStopCallback = $function;
    }

    /**
     * @param callable $function
     */
    public static function beforeReload(callable $function)
    {
        self::$beforeReloadCallback = $function;
    }

    /**
     * 设置PID文件
     * @param $pidFile
     */
    public static function setPidFile($pidFile)
    {
        self::$pidFile = $pidFile;
    }

    /**
     * @param $pid
     */
    public static function addPid($pid)
    {
        $pids = self::getPids();
        $pids[] = $pid;
        file_put_contents(self::$pidFile, implode(",", $pids));
    }

    /**
     * @param $pid
     */
    public static function removePid($pid)
    {
        $pids = self::getPids();
        $key = array_search($pid, $pids);
        if (false !== $key) {
            unset($pids[$key]);
        }

        if (empty($pids)) {
            unlink(self::$pidFile);
            return ;
        }
        file_put_contents(self::$pidFile, implode(",", array_unique($pids)));
    }

    /**
     * @return array
     */
    public static function getPids()
    {
        if (!file_exists(self::$pidFile)) {
            return [];
        }
        $pids = file_get_contents(self::$pidFile);
        return explode(",", $pids);
    }
}