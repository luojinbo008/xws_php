<?php
/**
 * Created by IntelliJ IDEA.
 * User: luojinbo
 * Date: 2019-01-14
 * Time: 10:54
 */

namespace Swoole\Coroutine\Component;

use Swoole\Coroutine\Mysql as CoMysql;

class MySQL extends Base
{
    /**
     * @return bool|CoMysql
     */
    public function create()
    {
        $mysql = new CoMysql();
        if ($mysql->connect($this->config) === false) {
            return false;
        }
        return $mysql;
    }

    /**
     * @param $name
     * @param $arguments
     * @return mixed
     */
    public function __call($name, $arguments)
    {
        // TODO: Implement __call() method.
        $mysql = $this->_getObject();
        if (!$mysql->connected) {
            $mysql = $this->create();
        }
        $res = call_user_func_array([$mysql, $name], $arguments);
        $this->_createObject($mysql);

        if ($mysql->errno) {
            throw new MySQLException($mysql->errno, $mysql->error);
        }
        return $res;
    }

    /**
     * @param $table
     * @param $data
     */
    public function insert($table, array $data)
    {
        if ($this->isMap($data)) {
            $data = [
                $data
            ];
        }

        $fields = null;
        $list = null;

        $last_id = 0;
        foreach ($data as $ret) {
            ksort($ret);
            if (null === $fields) {
                $fields = array_keys($ret);
                array_walk(
                    $fields,
                    function (&$item) {
                        $item = sprintf('`%s`', $item);
                    }
                );
            }

            $val = array_values($ret);
            array_walk(
                $val,
                function (&$item) {
                    if (!is_numeric($item)) {
                        $item = sprintf('"%s"', addslashes($item));
                    }
                }
            );
            $vas = implode(',', $val);
            $list[] = sprintf('(%s)', $vas);

        }

        if (!empty($list)) {
            $field = implode(',', $fields);

            $value = implode(',', $list);
            $sql = sprintf(
                ' INSERT INTO %s (%s) VALUES %s ',
                $table,
                $field,
                $value
            );
            $mysql = $this->_getObject();
            if (!$mysql->connected) {
                $mysql = $this->create();
            }
            $mysql->query($sql);
            $last_id = $mysql->insert_id;
            $this->_createObject($mysql);
            if ($mysql->errno) {
                throw new MySQLException($mysql->error, $mysql->errno);
            }
        }

        return $last_id;
    }

    /**
     * @param $data
     * @return bool
     */
    protected function isMap($data): bool
    {
        return is_array($data) && !empty(array_diff_assoc(array_keys($data), range(0, count($data))));
    }
}

class MySQLException extends \Exception
{

}