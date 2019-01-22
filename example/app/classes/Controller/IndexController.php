<?php
/**
 * Created by IntelliJ IDEA.
 * User: luojinbo
 * Date: 2019-01-21
 * Time: 20:51
 */
namespace App\Controller;

class IndexController implements \Swoole\IFace\Controller
{

    /**
     * 测试
     * @return int
     */
    public function index()
    {
        return [
            "em" => "Hello World!",
            "data" => [
            ]
        ];
    }

    public function _after()
    {
        // TODO: Implement _after() method.
        return true;
    }

    public function _before()
    {
        // TODO: Implement _before() method.
        return true;
    }
}