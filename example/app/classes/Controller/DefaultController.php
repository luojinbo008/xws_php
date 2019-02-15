<?php
/**
 * Created by IntelliJ IDEA.
 * User: luojinbo
 * Date: 2019-01-21
 * Time: 20:51
 */
namespace App\Controller;

class DefaultController implements \Swoole\IFace\Controller
{
    /**
     * 404 notFound
     * @return array
     */
    public function notFound()
    {
        return [
            "code" => "404",
            "message" => "Not Found",
            "data" => []
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