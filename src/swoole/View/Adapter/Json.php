<?php
/**
 * Created by IntelliJ IDEA.
 * User: luojinbo
 * Date: 2019-01-22
 * Time: 10:25
 */

namespace Swoole\View\Adapter;

use Swoole\Protocol\Request;
use Swoole\Protocol\Response;
use Swoole\View\Base;

class Json extends Base
{
    /**
     * @return false|string|null
     */
    public function display()
    {
        $data = \json_encode($this->model, JSON_UNESCAPED_UNICODE);

        if (Request::isHttp()) {
            Response::sendHttpHeader();
            $params = Request::getParams();
            $key = \Swoole\Swoole::$php->config['project']['jsonp'] ?? 'jsoncallback';
            if (isset($params[$key])) {
                Response::header("Content-Type", 'application/x-javascript; charset=utf-8');
                $data = $params[$key] . '(' . $data . ')';
            } else {
                Response::header("Content-Type", "application/json; charset=utf-8");
            }
        }
        return $data;

    }
}